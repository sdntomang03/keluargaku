<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    /**
     * Menampilkan daftar keluarga.
     * Admin melihat semua, User biasa melihat miliknya sendiri.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah user adalah admin
        if ($user->hasRole('admin')) {
            // Admin melihat SEMUA data keluarga dari database
            $families = Family::latest()->get();
        } else {
            // User biasa HANYA melihat keluarga di mana dia terdaftar
            $families = $user->families()->latest()->get();
        }

        return view('family.index', compact('families'));
    }

    /**
     * Menampilkan form untuk membuat keluarga baru.
     */
    public function create()
    {
        return view('family.create');
    }

    /**
     * Menyimpan data keluarga baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 1. Buat data keluarga
        $family = Family::create($validated);

        // 2. Hubungkan user yang sedang login ke keluarga ini
        $user = Auth::user();
        $family->users()->attach($user->id);

        return redirect()->route('family.index')->with('success', 'Keluarga baru berhasil dibuat!');
    }

    /**
     * Menampilkan form untuk mengedit keluarga.
     */
    public function edit(Family $family)
    {
        $this->authorizeAccess($family);

        return view('family.edit', compact('family'));
    }

    /**
     * Memperbarui data keluarga di database.
     */
    public function update(Request $request, Family $family)
    {
        $this->authorizeAccess($family);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $family->update($validated);

        return redirect()->route('family.index')->with('success', 'Data keluarga berhasil diperbarui!');
    }

    /**
     * Menghapus data keluarga dari database.
     */
    public function destroy(Family $family)
    {
        $this->authorizeAccess($family);

        $family->delete();

        return redirect()->route('family.index')->with('success', 'Keluarga berhasil dihapus!');
    }

    /**
     * Fungsi bantuan untuk memastikan user berhak mengakses keluarga ini.
     * Admin akan selalu diizinkan masuk.
     */
    private function authorizeAccess(Family $family)
    {
        $user = Auth::user();

        // 1. Jika dia Admin, langsung izinkan (return) tanpa cek relasi
        if ($user->hasRole('admin')) {
            return;
        }

        // 2. Jika bukan admin, pastikan dia adalah anggota pembuat/pengelola silsilah ini
        if (! $user->families->contains($family->id)) {
            abort(403, 'Akses ditolak. Anda bukan pengelola silsilah keluarga ini.');
        }
    }
}
