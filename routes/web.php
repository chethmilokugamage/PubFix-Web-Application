<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (file_exists(__DIR__.'/review.php')) {
    require __DIR__.'/review.php';
}

