<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\ProofController;
use App\Http\Controllers\Admin\SellRequestController as AdminSellRequestController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellRequestController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────
// Driver accounts are kept out of the storefront so they can focus on COD.
Route::middleware('driver.guest')->group(function () {
Route::get('/', HomeController::class)->name('home');

// Identitas & kepercayaan
Route::view('/tentang-kami', 'about')->name('about');
Route::view('/standar-garansi', 'grading')->name('grading');

Route::get('/iphone', [ListingController::class, 'index'])->name('listings.index');
Route::get('/iphone/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');

// ─── Demo login ───────────────────────────────────────────────────────────────
Route::get('/masuk', [LoginController::class, 'create'])->name('login');
Route::post('/masuk', [LoginController::class, 'store'])->name('login.store');
Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
Route::post('/daftar', [RegisterController::class, 'store'])->name('register.store');
Route::post('/keluar', [LoginController::class, 'destroy'])->name('logout');

// ─── Wishlist & Cart (session-based, guests allowed) ─────────────────────────
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/{listing}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart');
Route::post('/keranjang/{listing}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/keranjang/{listing}', [CartController::class, 'remove'])->name('cart.remove');

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Jual: pengajuan ke admin/toko (tanpa role penjual)
    Route::get('/jual', [SellRequestController::class, 'create'])->name('sell.create');
    Route::post('/jual', [SellRequestController::class, 'store'])->name('sell.store');
    Route::get('/pengajuan-jual', [SellRequestController::class, 'index'])->name('sell.index');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order:code}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/pesanan/{order:code}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/poll', [ChatController::class, 'pollIndex'])->name('chat.poll-index');
    Route::get('/chat/{thread}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{thread}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/{thread}/typing', [ChatController::class, 'typing'])->name('chat.typing')->middleware('throttle:60,1');
    Route::delete('/chat/{thread}/clear', [ChatController::class, 'clear'])->name('chat.clear');
    Route::delete('/chat/{thread}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::post('/chat/mulai/{listing:slug}', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/{thread}/kirim', [ChatController::class, 'store'])->name('chat.store');

    // Account
    Route::get('/akun', AccountController::class)->name('account');
});
});

// ─── Driver COD ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/', [DriverController::class, 'index'])->name('index');
    Route::get('/{order}', [DriverController::class, 'show'])->name('show');
    Route::post('/{order}/berangkat', [DriverController::class, 'depart'])->name('depart');
    Route::post('/{order}/bukti-sold', [DriverController::class, 'uploadProof'])->name('proof');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'driver.guest', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::get('/produk', [Admin\ProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/produk', [Admin\ProductController::class, 'store'])->name('products.store');
    Route::get('/produk/{listing}/edit', [Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produk/{listing}', [Admin\ProductController::class, 'update'])->name('products.update');
    Route::patch('/produk/{listing}/setujui', [Admin\ProductController::class, 'approve'])->name('products.approve');
    Route::patch('/produk/{listing}/tolak', [Admin\ProductController::class, 'reject'])->name('products.reject');
    Route::delete('/produk/{listing}', [Admin\ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/pesanan', [Admin\OrderController::class, 'index'])->name('orders');
    Route::patch('/pesanan/{order}/maju', [Admin\OrderController::class, 'advance'])->name('orders.advance');
    Route::get('/pengguna', [Admin\UserController::class, 'index'])->name('users');
    Route::get('/bukti-sold', [ProofController::class, 'index'])->name('proofs');
    Route::get('/pengajuan-jual', [AdminSellRequestController::class, 'index'])->name('sell-requests');
    Route::post('/pengajuan-jual/{sellRequest}/setujui', [AdminSellRequestController::class, 'approve'])->name('sell-requests.approve');
    Route::post('/pengajuan-jual/{sellRequest}/tolak', [AdminSellRequestController::class, 'reject'])->name('sell-requests.reject');
    Route::patch('/bukti-sold/{order}/setujui', [ProofController::class, 'approve'])->name('proofs.approve');
    Route::patch('/bukti-sold/{order}/tolak', [ProofController::class, 'reject'])->name('proofs.reject');
});
