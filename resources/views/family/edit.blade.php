<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Silsilah: {{ $family->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-200">

                <form method="POST" action="{{ route('family.update', $family->id) }}">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-6">
                        <label class="block font-bold text-sm text-slate-700 mb-2">Nama Keluarga / Silsilah</label>
                        <input type="text" name="name" value="{{ old('name', $family->name) }}"
                            class="border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm block w-full py-3"
                            required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold text-sm text-slate-700 mb-2">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" rows="4"
                            class="border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm block w-full py-3">{{ old('description', $family->description) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-8 border-t border-slate-100 pt-6">
                        <a href="{{ route('family.index') }}"
                            class="text-slate-500 hover:text-slate-800 mr-6 font-medium">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>