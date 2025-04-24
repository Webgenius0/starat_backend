<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\backend\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
