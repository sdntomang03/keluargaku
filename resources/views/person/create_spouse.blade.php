<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Pasangan untuk {{ $person->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('spouse.store', [$family->id, $person->id]) }}"
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
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nama Pasangan</label>
                        <input type="text" name="name"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                        <select name="gender"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            required>
                            <option value="P" {{ $person->gender === 'L' ? 'selected' : '' }}>Perempuan</option>
                            <option value="L" {{ $person->gender === 'P' ? 'selected' : '' }}>Laki-laki</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Foto Profil (Opsional)</label>
                        <input type="file" name="photo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('family.tree', $family->id) }}"
                            class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit"
                            class="bg-emerald-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-emerald-700">
                            Simpan Pasangan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>