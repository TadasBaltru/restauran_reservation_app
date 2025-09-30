<?php

use Illuminate\Support\Facades\Route;

// Public Reservation Creation
Route::get('/', \App\Livewire\Reservation\Create::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Reservation Management (Authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/reservations', \App\Livewire\Reservation\Index::class)->name('reservations.index');
    Route::get('/reservations/{reservation}', \App\Livewire\Reservation\Show::class)->name('reservations.show');
});

// Restaurant Management (Admin only) - Livewire Components
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/restaurants', \App\Livewire\Restaurant\Index::class)->name('restaurants.index');
    Route::get('/restaurants/create', \App\Livewire\Restaurant\Create::class)->name('restaurants.create');
    Route::get('/restaurants/{restaurant}', \App\Livewire\Restaurant\Show::class)->name('restaurants.show');
    Route::get('/restaurants/{restaurant}/edit', \App\Livewire\Restaurant\Edit::class)->name('restaurants.edit');
});

require __DIR__.'/auth.php';
