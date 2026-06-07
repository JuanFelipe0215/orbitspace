<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        Route::get('/', function () {
            return view('welcome');
        });

        Route::middleware('auth')->group(function () {

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        Route::prefix('admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.index');
            Route::patch('/{id}/suspend', [\App\Http\Controllers\Admin\AdminController::class, 'suspend'])->name('admin.suspend');
            Route::patch('/{id}/unsuspend', [\App\Http\Controllers\Admin\AdminController::class, 'unsuspend'])->name('admin.unsuspend');
        });

        require __DIR__.'/auth.php';
    });
}
