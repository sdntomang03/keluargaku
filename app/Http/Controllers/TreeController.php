<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Support\Facades\Auth;

class TreeController extends Controller
{
    /**
     * Menampilkan pohon silsilah dari keluarga tertentu.
     */
    public function show(Family $family)
    {
        $user = Auth::user();

        // OTORISASI: Cek apakah user boleh melihat keluarga ini
        // Jika bukan admin DAN tidak terdaftar di tabel family_user untuk keluarga ini, tolak!
        if (! $user->hasRole('admin') && ! $user->families->contains($family->id)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat silsilah keluarga ini.');
        }

        // Ambil semua anggota keluarga beserta pasangannya
        $people = $family->people()->with('spouses')->get();

        // Cari Leluhur Utama (Root Nodes).
        // Menggunakan whereNull karena leluhur pertama tidak memiliki parent_id.
        $rootNodes = $people->whereNull('parent_id');

        return view('tree.index', compact('family', 'people', 'rootNodes'));
    }
}
