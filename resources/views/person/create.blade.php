<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(isset($parent))
            Tambah Anak dari {{ $parent->name }}
            @elseif(isset($child))
            Tambah Orang Tua dari {{ $child->name }}
            @else
            Tambah Leluhur Utama
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('person.store', $family->id) }}" enctype="multipart/form-data">
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


                    {{-- ========================================== --}}
                    {{-- INPUT TERSEMBUNYI SESUAI MODE YANG DIPILIH --}}
                    {{-- ========================================== --}}

                    {{-- JIKA MODE TAMBAH ANAK --}}
                    @if(isset($parent))
                    <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                    @endif

                    {{-- JIKA MODE TAMBAH ORANG TUA --}}
                    @if(isset($child))
                    <input type="hidden" name="child_id" value="{{ $child->id }}">

                    {{-- Banner Peringatan Info --}}
                    <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-md">
                        <p class="text-amber-800 text-sm font-semibold flex items-center">
                            <span class="text-xl mr-2">👴</span>
                            Menambahkan Orang Tua (Leluhur) untuk: <span class="text-lg ml-2 font-black">{{ $child->name
                                }}</span>
                        </p>
                        <p class="text-amber-700 text-xs mt-1 ml-7">
                            Setelah disimpan, posisi {{ $child->name }} akan otomatis bergeser ke bawah orang ini.
                        </p>
                    </div>
                    @endif
                    {{-- ========================================== --}}


                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                        <select name="gender"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            required>
                            <option value="L" {{ old('gender')=='L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender')=='P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    {{-- PILIHAN PASANGAN HANYA MUNCUL JIKA MODE TAMBAH ANAK --}}
                    @if(isset($parent))
                    @if($parent->spouses->count() > 1)
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Anak dari Pasangan</label>
                        <select name="spouse_id"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            required>
                            <option value="">-- Pilih Pasangan --</option>
                            @foreach($parent->spouses as $spouse)
                            <option value="{{ $spouse->id }}" {{ old('spouse_id')==$spouse->id ? 'selected' : '' }}>
                                Anak bersama {{ $spouse->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Pilih pasangan karena orang tua memiliki lebih dari satu
                            riwayat pasangan.</p>
                    </div>
                    @elseif($parent->spouses->count() === 1)
                    <input type="hidden" name="spouse_id" value="{{ $parent->spouses->first()->id }}">
                    @endif
                    @endif

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Foto Profil (Opsional)</label>
                        <input type="file" name="photo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">No. Handphone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            placeholder="Contoh: 081234567890">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                        <textarea name="address" rows="3"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            placeholder="Tuliskan alamat lengkap...">{{ old('address') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('family.tree', $family->id) }}"
                            class="text-gray-600 hover:text-gray-900 mr-4 font-medium">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md font-bold hover:bg-blue-700 transition shadow-sm">
                            Simpan Data
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>