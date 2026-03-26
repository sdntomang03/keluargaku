<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-2xl text-slate-800 tracking-tight">
                📂 Daftar Keluarga Saya
            </h2>
            <a href="{{ route('family.create') }}"
                class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-lg whitespace-nowrap">
                + Buat Keluarga Baru
            </a>
        </div>
    </x-slot>

    {{-- TOAST NOTIFIKASI BERHASIL --}}
    @if (session('success'))
    <div id="toast-success"
        class="fixed top-24 right-5 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-xl shadow-2xl border-l-4 border-green-500 transform transition-all duration-500 translate-x-0"
        role="alert">
        <div
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
        </div>
        <div class="ml-3 text-sm font-semibold text-slate-700">{{ session('success') }}</div>
        <button type="button"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
            onclick="document.getElementById('toast-success').remove()">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
    <script>
        setTimeout(() => { const t = document.getElementById('toast-success'); if(t) { t.style.opacity='0'; t.style.transform='translateX(100%)'; setTimeout(()=>t.remove(),500); } }, 4000);
    </script>
    @endif

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($families->isEmpty())
            {{-- TAMPILAN JIKA KOSONG --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <div class="text-6xl mb-4">🌳</div>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Data Keluarga</h3>
                <p class="text-slate-500 mb-6">Mulai bangun silsilah pertama Anda sekarang juga.</p>
                <a href="{{ route('family.create') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                    Buat Silsilah Pertama
                </a>
            </div>
            @else
            {{-- GRID KARTU KELUARGA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($families as $family)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col">
                    <div class="p-6 flex-grow">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">Silsilah</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-2 leading-tight">{{ $family->name }}</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">
                            {{ $family->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    {{-- Aksi Bawah --}}
                    <div class="bg-slate-50 p-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('family.tree', $family->id) }}"
                            class="text-blue-600 font-bold text-sm hover:text-blue-800 flex items-center">
                            🌳 Lihat Bagan
                        </a>

                        <div class="flex gap-3">
                            <a href="{{ route('family.edit', $family->id) }}"
                                class="text-slate-400 hover:text-amber-500 transition" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                            </a>
                            <form action="{{ route('family.destroy', $family->id) }}" method="POST" class="inline-block"
                                onsubmit="return confirm('Yakin ingin menghapus seluruh silsilah keluarga {{ $family->name }} ini? Semua data orang di dalamnya akan ikut terhapus permanen!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</x-app-layout>