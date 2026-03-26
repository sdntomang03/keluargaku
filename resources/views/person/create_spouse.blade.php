<x-app-layout>
    {{-- Logika Cerdas untuk Menentukan Peran dan Lawan Jenis --}}
    @php
    $isMale = $person->gender === 'L';
    $spouseRole = $isMale ? 'Istri' : 'Suami';
    $defaultGender = $isMale ? 'P' : 'L';
    $selectedGender = old('gender', $defaultGender);
    $icon = $isMale ? '👰‍♀️' : '🤵‍♂️';
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah {{ $spouseRole }} dari {{ $person->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST"
                    action="{{ route('spouse.store', ['family' => $family->id, 'person' => $person->id]) }}"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- BLOK UNTUK MENAMPILKAN PESAN ERROR --}}
                    @if ($errors->any())
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                        <div class="flex items-center">
                            <span class="text-xl mr-2">⚠️</span>
                            <h3 class="text-red-800 font-bold">Gagal Menyimpan Data</h3>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-600 mt-2">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    {{-- AKHIR BLOK ERROR --}}

                    {{-- BANNER PINK INFO TAMBAH PASANGAN --}}
                    <div class="mb-6 bg-pink-50 border-l-4 border-pink-500 p-4 rounded-md shadow-sm">
                        <p class="text-pink-800 text-sm font-semibold flex items-center">
                            <span class="text-xl mr-2">{{ $icon }}</span>
                            Menambahkan {{ $spouseRole }} untuk: <span class="text-lg ml-2 font-black">{{ $person->name
                                }}</span>
                        </p>
                        <p class="text-pink-700 text-xs mt-1 ml-9">
                            Data ini akan ditampilkan sejajar sebagai ikatan pernikahan dengan {{ $person->name }} di
                            diagram silsilah.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nama Lengkap {{ $spouseRole }}</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            placeholder="Masukkan nama {{ strtolower($spouseRole) }}..." required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                        <select name="gender"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full bg-gray-50 cursor-not-allowed"
                            required>
                            <option value="L" {{ $selectedGender==='L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $selectedGender==='P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            *Sistem otomatis menyesuaikan jenis kelamin sebagai {{ strtolower($spouseRole) }} dari {{
                            $person->name }}.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Foto Profil (Opsional)</label>
                        <input type="file" name="photo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">No. Handphone (Opsional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            placeholder="Contoh: 081234567890">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Alamat Lengkap (Opsional)</label>
                        <textarea name="address" rows="3"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            placeholder="Tuliskan alamat lengkap...">{{ old('address') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('family.tree', $family->id) }}"
                            class="text-gray-600 hover:text-gray-900 mr-4 font-medium">Batal</a>
                        <button type="submit"
                            class="bg-pink-600 text-white px-6 py-2 rounded-md font-bold hover:bg-pink-700 transition shadow-sm">
                            Simpan Data {{ $spouseRole }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>