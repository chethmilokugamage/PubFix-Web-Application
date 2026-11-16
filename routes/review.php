<?php

use App\Http\Controllers\Review\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/worker/{handymanId}', [ReviewController::class, 'workerReviews'])->name('worker');
    Route::get('/create', [ReviewController::class, 'create'])->name('create');
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
    Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit');
    Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
    Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
});
