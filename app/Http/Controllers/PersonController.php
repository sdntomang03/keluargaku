<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    private function checkAccess($family_id)
    {
        $user = Auth::user();
        if (! $user->hasRole('admin') && ! $user->families->contains($family_id)) {
            abort(403, 'Akses ditolak.');
        }
    }

    // ==================================================
    // 1. CRUD UTAMA (LELUHUR, ANAK, DAN ORANG TUA)
    // ==================================================

    public function create(Request $request, Family $family)
    {
        $this->checkAccess($family->id);

        $parentId = $request->query('parent_id');
        $childId = $request->query('child_id');

        $parent = $parentId ? Person::with('spouses')->find($parentId) : null;
        $child = $childId ? Person::find($childId) : null;

        return view('person.create', compact('family', 'parent', 'child'));
    }

    public function store(Request $request, Family $family)
    {
        $this->checkAccess($family->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'parent_id' => 'nullable|exists:people,id',
            'child_id' => 'nullable|exists:people,id',
            'spouse_id' => 'nullable|exists:people,id', // Diambil dari people, bukan tabel spouses lagi
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos/people', 'public');
        }

        // -----------------------------------------------------
        // LOGIKA CERDAS: MENENTUKAN FATHER_ID DAN MOTHER_ID
        // -----------------------------------------------------
        $father_id = null;
        $mother_id = null;

        if (! empty($validated['parent_id'])) {
            $parent = Person::find($validated['parent_id']);

            // Jika orang tuanya Laki-laki, dia jadi Bapak. Pasangannya jadi Ibu.
            if ($parent->gender === 'L') {
                $father_id = $parent->id;
                $mother_id = $validated['spouse_id'] ?? null;
            }
            // Jika orang tuanya Perempuan, dia jadi Ibu. Pasangannya jadi Bapak.
            else {
                $mother_id = $parent->id;
                $father_id = $validated['spouse_id'] ?? null;
            }
        }

        // Eksekusi Pembuatan Data
        $person = Person::create([
            'family_id' => $family->id,
            'father_id' => $father_id,
            'mother_id' => $mother_id,
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'photo_path' => $photoPath,
        ]);

        // -----------------------------------------------------
        // LOGIKA REVERSE: JIKA SEDANG MENAMBAH ORANG TUA (KE ATAS)
        // -----------------------------------------------------
        if (! empty($validated['child_id'])) {
            $child = Person::find($validated['child_id']);
            if ($child) {
                // Jika yang dibuat Laki-laki, masukkan sbg Ayah. Jika Perempuan, sbg Ibu.
                if ($person->gender === 'L') {
                    $child->update(['father_id' => $person->id]);
                } else {
                    $child->update(['mother_id' => $person->id]);
                }
            }
        }

        return redirect()->route('family.tree', $family->id)->with('success', 'Data berhasil ditambahkan!');
    }

    // ==================================================
    // 2. FUNGSI KHUSUS: TAMBAH PASANGAN (BUKU NIKAH)
    // ==================================================

    public function createSpouse(Family $family, Person $person)
    {
        $this->checkAccess($family->id);

        return view('person.create_spouse', compact('family', 'person'));
    }

    public function storeSpouse(Request $request, Family $family, Person $person)
    {
        $this->checkAccess($family->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos/people', 'public');
        }

        // 1. Buat data pasangannya sebagai Person biasa
        $spouse = Person::create([
            'family_id' => $family->id,
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'photo_path' => $photoPath,
        ]);

        // 2. Ikat mereka dalam pernikahan (Tabel Marriages Bi-Directional)
        $person->spouses()->attach($spouse->id);
        $spouse->spouses()->attach($person->id); // Ikat balik agar terbaca dari kedua sisi

        return redirect()->route('family.tree', $family->id)->with('success', 'Pasangan berhasil ditambahkan!');
    }

    // ==================================================
    // 3. EDIT, UPDATE, & DELETE GLOBAL (UNTUK SEMUA ORANG)
    // ==================================================

    public function edit(Family $family, Person $person)
    {
        $this->checkAccess($family->id);

        return view('person.edit', compact('family', 'person'));
    }

    public function update(Request $request, Family $family, Person $person)
    {
        $this->checkAccess($family->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
                Storage::disk('public')->delete($person->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('photos/people', 'public');
        }

        $person->update($validated);

        return redirect()->route('family.tree', $family->id)->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Family $family, Person $person)
    {
        $this->checkAccess($family->id);

        // Hapus foto dari storage
        if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // Hapus data orangnya.
        // Relasi pernikahan (marriages) dan id orang tua di anak-anak (father_id/mother_id)
        // otomatis diurus oleh cascadeOnDelete dan nullOnDelete di Migration!
        $person->delete();

        return redirect()->route('family.tree', $family->id)
            ->with('success', 'Data berhasil dihapus dari silsilah!');
    }
}
