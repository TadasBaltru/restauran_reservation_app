<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Restaurant Management (Admin only) - Livewire Components
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/restaurants', \App\Livewire\Restaurant\Index::class)->name('restaurants.index');
    Route::get('/restaurants/create', \App\Livewire\Restaurant\Create::class)->name('restaurants.create');
    Route::get('/restaurants/{restaurant}', \App\Livewire\Restaurant\Show::class)->name('restaurants.show');
    Route::get('/restaurants/{restaurant}/edit', \App\Livewire\Restaurant\Edit::class)->name('restaurants.edit');
});

require __DIR__.'/auth.php';
