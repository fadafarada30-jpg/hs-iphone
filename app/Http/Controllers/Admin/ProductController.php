<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\Warranty;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $listings = Listing::with('seller')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('cari'), fn ($q) => $q->where('model', 'like', '%'.$request->string('cari').'%'))
            ->latestFirst()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'listings' => $listings,
            'statuses' => ListingStatus::cases(),
            'pendingCount' => Listing::where('status', ListingStatus::PendingReview)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        $slug = Str::slug($data['model'].' '.$data['storage_gb'].'gb '.$data['color'])
            .'-'.Str::lower(Str::random(6));

        $listing = Listing::create([
            'seller_id' => $request->user()->id,
            'slug' => $slug,
            'model' => $data['model'],
            'series' => $data['series'] ?: null,
            'storage_gb' => $data['storage_gb'],
            'color' => $data['color'],
            'condition' => $data['condition'],
            'battery_health' => $data['battery_health'],
            'price' => $data['price'],
            'original_price' => ! empty($data['original_price']) ? $data['original_price'] : null,
            'completeness' => $data['completeness'] ?? [],
            'warranty' => $data['warranty'],
            'location' => $data['location'],
            'description' => $data['description'] ?: null,
            'status' => $data['status'],
            'views' => 0,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        foreach ($request->file('photos', []) as $index => $photo) {
            $listing->photos()->create([
                'path' => $photo->store("listings/{$listing->id}", 'public'),
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Produk berhasil ditambahkan ke katalog.');
    }

    public function edit(Listing $listing): View
    {
        $listing->load('photos');

        return view('admin.products.edit', array_merge($this->formOptions(), [
            'listing' => $listing,
        ]));
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $data = $request->validate($this->rules($listing), $this->messages());

        $removeIds = array_map('intval', $request->input('remove_photos', []));
        $photosToRemove = $listing->photos()->whereIn('id', $removeIds)->get();

        foreach ($photosToRemove as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        $newPhotos = $request->file('photos', []);
        if ($listing->photos()->count() + count($newPhotos) > 5) {
            return back()->with('error', 'Total foto maksimal 5.')->withInput();
        }

        $listing->update([
            'model' => $data['model'],
            'series' => $data['series'] ?: null,
            'storage_gb' => $data['storage_gb'],
            'color' => $data['color'],
            'condition' => $data['condition'],
            'battery_health' => $data['battery_health'],
            'price' => $data['price'],
            'original_price' => ! empty($data['original_price']) ? $data['original_price'] : null,
            'completeness' => $data['completeness'] ?? [],
            'warranty' => $data['warranty'],
            'location' => $data['location'],
            'description' => $data['description'] ?: null,
            'status' => $data['status'],
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $sortOrder = (int) $listing->photos()->max('sort_order');
        foreach ($newPhotos as $photo) {
            $sortOrder++;
            $listing->photos()->create([
                'path' => $photo->store("listings/{$listing->id}", 'public'),
                'sort_order' => $sortOrder,
            ]);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function approve(Listing $listing): RedirectResponse
    {
        $listing->update(['status' => ListingStatus::Active]);

        return back()->with('success', 'Listing disetujui.');
    }

    public function reject(Listing $listing): RedirectResponse
    {
        $listing->update(['status' => ListingStatus::Rejected]);

        return back()->with('success', 'Listing ditolak.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        foreach ($listing->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $listing->delete();

        return redirect()->route('admin.products')->with('success', 'Listing dihapus.');
    }

    private function formOptions(): array
    {
        return [
            'conditions' => ListingCondition::cases(),
            'warranties' => Warranty::cases(),
            'statuses' => ListingStatus::cases(),
        ];
    }

    private function rules(?Listing $listing = null): array
    {
        $removePhotoRules = ['nullable', 'array'];
        if ($listing) {
            $removePhotoRules[] = Rule::exists('listing_photos', 'id')
                ->where(fn ($query) => $query->where('listing_id', $listing->id));
        }

        return [
            'model' => ['required', 'string', 'max:80'],
            'series' => ['nullable', 'string', 'max:20'],
            'storage_gb' => ['required', 'integer', 'min:16', 'max:2048'],
            'color' => ['required', 'string', 'max:40'],
            'condition' => ['required', Rule::enum(ListingCondition::class)],
            'battery_health' => ['required', 'integer', 'min:0', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'original_price' => ['nullable', 'integer', 'min:0'],
            'warranty' => ['required', Rule::enum(Warranty::class)],
            'location' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(ListingStatus::class)],
            'completeness' => ['nullable', 'array'],
            'completeness.*' => ['string', 'max:40'],
            'is_featured' => ['boolean'],
            'remove_photos' => $removePhotoRules,
            'remove_photos.*' => ['integer'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:2048'],
        ];
    }

    private function messages(): array
    {
        return [
            'model.required' => 'Model wajib diisi.',
            'storage_gb.required' => 'Kapasitas penyimpanan wajib diisi.',
            'color.required' => 'Warna wajib diisi.',
            'condition.required' => 'Kondisi wajib dipilih.',
            'battery_health.required' => 'Kesehatan baterai wajib diisi.',
            'price.required' => 'Harga jual wajib diisi.',
            'warranty.required' => 'Garansi wajib dipilih.',
            'location.required' => 'Lokasi unit wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'photos.max' => 'Maksimal 5 foto diunggah sekaligus.',
        ];
    }
}
