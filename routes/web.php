<?php

use App\Http\Controllers\FamilyController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TreeController;
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
    // ROUTE SILSILAH KELUARGA UTAMA
    // ==========================================
    Route::get('/family/{family}/tree', [TreeController::class, 'show'])->name('family.tree');
    Route::resource('family', FamilyController::class);

    // ------------------------------------------
    // 1. ROUTE GLOBAL UNTUK SEMUA ORANG
    // (Leluhur, Anak, Orang Tua, Istri, Suami)
    // ------------------------------------------
    Route::get('/family/{family}/person/create', [PersonController::class, 'create'])->name('person.create');
    Route::post('/family/{family}/person', [PersonController::class, 'store'])->name('person.store');

    Route::get('/family/{family}/person/{person}/edit', [PersonController::class, 'edit'])->name('person.edit');
    Route::put('/family/{family}/person/{person}', [PersonController::class, 'update'])->name('person.update');
    Route::delete('/family/{family}/person/{person}', [PersonController::class, 'destroy'])->name('person.destroy');

    // ------------------------------------------
    // 2. ROUTE KHUSUS TAMBAH PASANGAN (BUKU NIKAH)
    // (Membutuhkan ID Person yang sedang diklik)
    // ------------------------------------------
    Route::get('/family/{family}/person/{person}/spouse/create', [PersonController::class, 'createSpouse'])->name('spouse.create');
    Route::post('/family/{family}/person/{person}/spouse', [PersonController::class, 'storeSpouse'])->name('spouse.store');

});

require __DIR__.'/auth.php';
