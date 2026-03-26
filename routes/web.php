<?php

use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TreeController; // Pastikan ini di-import
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // ROUTE SILSILAH KELUARGA
    // ==========================================
    Route::get('/family/{family}/tree', [TreeController::class, 'show'])->name('family.tree');

    // ------------------------------------------
    // 1. Route CRUD Keturunan (Person)
    // ------------------------------------------
    Route::get('/family/{family}/person/create', [PersonController::class, 'create'])->name('person.create');
    Route::post('/family/{family}/person', [PersonController::class, 'store'])->name('person.store');

    // Ini adalah route yang error (pastikan 3 baris ini ada)
    Route::get('/family/{family}/person/{person}/edit', [PersonController::class, 'edit'])->name('person.edit');
    Route::put('/family/{family}/person/{person}', [PersonController::class, 'update'])->name('person.update');
    Route::delete('/family/{family}/person/{person}', [PersonController::class, 'destroy'])->name('person.destroy');

    // ------------------------------------------
    // 2. Route CRUD Pasangan (Spouse)
    // ------------------------------------------
    Route::get('/family/{family}/person/{person}/spouse/create', [PersonController::class, 'createSpouse'])->name('spouse.create');
    Route::post('/family/{family}/person/{person}/spouse', [PersonController::class, 'storeSpouse'])->name('spouse.store');

    Route::get('/family/{family}/person/{person}/spouse/{spouse}/edit', [PersonController::class, 'editSpouse'])->name('spouse.edit');
    Route::put('/family/{family}/person/{person}/spouse/{spouse}', [PersonController::class, 'updateSpouse'])->name('spouse.update');
    Route::delete('/family/{family}/person/{person}/spouse/{spouse}', [PersonController::class, 'destroySpouse'])->name('spouse.destroy');
});

require __DIR__.'/auth.php';
