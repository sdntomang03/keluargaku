<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Person;
use App\Models\Spouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    // Mengecek apakah user berhak mengedit keluarga ini
    private function checkAccess($family_id)
    {
        $user = Auth::user();
        if (! $user->hasRole('admin') && ! $user->families->contains($family_id)) {
            abort(403, 'Akses ditolak.');
        }
    }

    // ==================================================
    // 1. CRUD KETURUNAN (PERSON)
    // ==================================================

    public function create(Request $request, Family $family)
    {
        $this->checkAccess($family->id);

        $parentId = $request->query('parent_id');
        $childId = $request->query('child_id'); // <-- BARU: Tangkap id anak

        $parent = $parentId ? Person::with('spouses')->find($parentId) : null;
        $child = $childId ? Person::find($childId) : null; // <-- BARU: Cari data anak

        // Pastikan variabel $child ikut dikirim ke view (compact)
        return view('person.create', compact('family', 'parent', 'child'));
    }

    public function store(Request $request, Family $family)
    {
        $this->checkAccess($family->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'parent_id' => 'nullable|exists:people,id',
            'child_id' => 'nullable|exists:people,id', // <-- BARU: Tambahkan ini di validasi
            'spouse_id' => 'nullable|exists:spouses,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // ... (Logika upload foto biarkan sama persis seperti sebelumnya) ...
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos/people', 'public');
        }

        // Buat data orang tuanya
        $person = Person::create([
            'family_id' => $family->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'spouse_id' => $validated['spouse_id'] ?? null,
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'photo_path' => $photoPath,
        ]);

        // ===============================================================
        // BARU: Jika ini adalah proses Tambah Orang Tua, sambungkan anaknya!
        // ===============================================================
        if (! empty($validated['child_id'])) {
            $child = Person::find($validated['child_id']);
            if ($child) {
                // Update anak (Mashudi) agar parent_id-nya merujuk ke orang tua yang baru dibuat
                $child->update(['parent_id' => $person->id]);
            }
        }

        return redirect()->route('family.tree', $family->id)->with('success', 'Data berhasil ditambahkan!');
    }

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        // 1. Hapus foto orang yang bersangkutan
        if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // 2. Jika Anda ingin anak-anaknya ikut terhapus (Cascade Manual),
        //    tapi karena di migration Anda pakai nullOnDelete, maka baris ini opsional:
        $person->children()->delete();

        // 3. Eksekusi hapus
        $person->delete();

        return redirect()->route('family.tree', $family->id)
            ->with('success', 'Anggota keluarga berhasil dihapus!');
    }

    // ==================================================
    // 2. CRUD PASANGAN (SPOUSE)
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos/spouses', 'public');
        }

        Spouse::create([
            'person_id' => $person->id,
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('family.tree', $family->id)->with('success', 'Pasangan berhasil ditambahkan!');
    }

    public function editSpouse(Family $family, Person $person, Spouse $spouse)
    {
        $this->checkAccess($family->id);

        return view('person.edit_spouse', compact('family', 'person', 'spouse'));
    }

    public function updateSpouse(Request $request, Family $family, Person $person, Spouse $spouse)
    {
        $this->checkAccess($family->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($spouse->photo_path && Storage::disk('public')->exists($spouse->photo_path)) {
                Storage::disk('public')->delete($spouse->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('photos/spouses', 'public');
        }

        $spouse->update($validated);

        return redirect()->route('family.tree', $family->id)->with('success', 'Data pasangan berhasil diperbarui!');
    }

    public function destroySpouse(Family $family, Person $person, Spouse $spouse)
    {
        $this->checkAccess($family->id);

        if ($spouse->photo_path && Storage::disk('public')->exists($spouse->photo_path)) {
            Storage::disk('public')->delete($spouse->photo_path);
        }

        $spouse->delete();

        return redirect()->route('family.tree', $family->id)->with('success', 'Data pasangan berhasil dihapus!');
    }
}
