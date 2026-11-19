<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $threads = ChatThread::with(['listing', 'buyer', 'seller', 'latestMessage'])
            ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->orderByDesc('last_message_at')
            ->get();

        return view('chat.index', compact('threads', 'user'));
    }

    public function pollIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        $threads = ChatThread::with(['listing', 'buyer', 'seller', 'latestMessage'])
            ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->orderByDesc('last_message_at')
            ->limit(30)
            ->get()
            ->map(fn (ChatThread $thread) => [
                'id' => $thread->id,
                'url' => route('chat.show', $thread),
                'delete_url' => route('chat.destroy', $thread),
                'other' => [
                    'name' => $thread->counterpartFor($user)->name,
                    'initials' => $thread->counterpartFor($user)->initials(),
                    'color' => $thread->counterpartFor($user)->avatar_color,
                ],
                'listing' => $thread->listing
                    ? [
                        'model' => $thread->listing->model,
                        'storage_label' => $thread->listing->storageLabel(),
                    ]
                    : null,
                'last' => $thread->latestMessage
                    ? [
                        'id' => $thread->latestMessage->id,
                        'body' => $thread->latestMessage->body,
                        'age' => (int) abs(now()->diffInSeconds($thread->latestMessage->created_at)),
                    ]
                    : null,
                'unread' => $thread->buyer_id === $user->id ? $thread->unread_count : 0,
            ]);

        return response()->json(['threads' => $threads->values()]);
    }

    public function show(ChatThread $thread, Request $request): View
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        $thread->load(['listing', 'buyer', 'seller', 'messages.sender']);

        if ($thread->buyer_id === $user->id && $thread->unread_count > 0) {
            $thread->update(['unread_count' => 0]);
        }

        return view('chat.show', compact('thread', 'user'));
    }

    public function poll(ChatThread $thread, Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        $after = $request->integer('after', 0);

        $messages = $thread->messages()
            ->with('sender')
            ->when($after > 0, fn ($query) => $query->where('id', '>', $after))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn (Message $message) => $this->messagePayload($message, $user));

        if ($messages->isNotEmpty() && $thread->buyer_id === $user->id && $thread->unread_count > 0) {
            $thread->update(['unread_count' => 0]);
        }

        $other = $thread->counterpartFor($user);

        return response()->json([
            'messages' => $messages->values(),
            'typing' => Cache::has($this->typingKey($thread, $other)),
            'cleared_at' => $thread->cleared_at?->timestamp ?? 0,
        ]);
    }

    public function typing(ChatThread $thread, Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        Cache::put($this->typingKey($thread, $user), true, now()->addSeconds(8));

        return response()->json(['ok' => true]);
    }

    public function start(Request $request, Listing $listing): RedirectResponse
    {
        $user = $request->user();
        abort_if($listing->seller_id === $user->id, 422, 'Tidak bisa chat dengan diri sendiri.');

        $thread = ChatThread::firstOrCreate(
            ['listing_id' => $listing->id, 'buyer_id' => $user->id],
            ['seller_id' => $listing->seller_id, 'last_message_at' => now()]
        );

        return redirect()->route('chat.show', $thread);
    }

    public function store(StoreMessageRequest $request, ChatThread $thread): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        $message = Message::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        $thread->update([
            'last_message_at' => now(),
            'unread_count' => $thread->buyer_id === $user->id
                ? 0
                : min(255, $thread->unread_count + 1),
        ]);

        Cache::forget($this->typingKey($thread, $user));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->messagePayload($message, $user),
            ], 201);
        }

        return redirect()->route('chat.show', $thread);
    }

    public function destroy(ChatThread $thread, Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        $other = $thread->counterpartFor($user);
        Cache::forget($this->typingKey($thread, $user));
        Cache::forget($this->typingKey($thread, $other));

        Message::where('chat_thread_id', $thread->id)->delete();
        $thread->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('chat.index');
    }

    public function clear(ChatThread $thread, Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($thread->involves($user), 403);

        $other = $thread->counterpartFor($user);
        $clearedAt = now();

        Message::where('chat_thread_id', $thread->id)->delete();
        $thread->update([
            'cleared_at' => $clearedAt,
            'last_message_at' => null,
            'unread_count' => 0,
        ]);

        Cache::forget($this->typingKey($thread, $user));
        Cache::forget($this->typingKey($thread, $other));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'cleared_at' => $clearedAt->timestamp,
            ]);
        }

        return redirect()->route('chat.show', $thread);
    }

    private function typingKey(ChatThread $thread, User $user): string
    {
        return "chat:typing:{$thread->id}:{$user->id}";
    }

    private function messagePayload(Message $message, User $viewer): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'mine' => $message->sender_id === $viewer->id,
            'sender' => $message->sender?->name,
            'time' => $message->created_at->format('H:i'),
        ];
    }
}
