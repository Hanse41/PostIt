<?php

use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\WorkOSController;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::middleware([
    'auth', 'verified',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});



// Work OS Route
Route::middleware('guest')->group(function () {
    Route::get('auth/workos', [WorkOSController::class, 'redirect'])->name('auth.workos');
    Route::get('auth/workos/callback', [WorkOSController::class, 'callback'])->name('auth.workos.callback');
});

//User Post Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

//Post Action Routes
Route::middleware(['auth'])->group(function () {
    // ...existing routes...
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
});

//User Profile Routes
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');


Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
