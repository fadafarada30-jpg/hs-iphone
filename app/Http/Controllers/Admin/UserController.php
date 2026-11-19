<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::withCount(['listings', 'orders'])
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('cari'), fn ($q) => $q->where('name', 'like', '%'.$request->string('cari').'%')
                ->orWhere('email', 'like', '%'.$request->string('cari').'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'counts' => [
                'total' => User::count(),
                'buyer' => User::where('role', Role::Buyer)->count(),
                'admin' => User::where('role', Role::Admin)->count(),
                'driver' => User::where('role', Role::Driver)->count(),
            ],
        ]);
    }
}
