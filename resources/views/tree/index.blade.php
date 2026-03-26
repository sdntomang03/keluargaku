<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-2xl text-slate-800 tracking-tight">
                Silsilah <span class="text-blue-600">{{ $family->name }}</span>
            </h2>

            <div class="flex flex-wrap items-center gap-3">
                {{-- DROPDOWN FILTER KETURUNAN --}}
                <div class="relative">
                    <select id="filter-node"
                        class="bg-white border border-slate-300 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full py-2 px-4 shadow-sm font-semibold cursor-pointer">
                        <option value="all">🌍 Tampilkan Semua Keturunan</option>
                    </select>
                </div>

                <a href="{{ route('person.create', $family->id) }}"
                    class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-lg whitespace-nowrap">
                    + Leluhur Utama
                </a>
            </div>
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
        setTimeout(function() {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
    @endif

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-300 shadow-xl rounded-2xl p-4 relative">
                <div id="tree-wrapper">
                    <div id="tree" style="width: 100%; height: 75vh;"></div>
                </div>
            </div>
        </div>
    </div>

    <form id="action-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://balkan.app/js/familytree.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data sudah mencakup relasi father_id, mother_id, dan spouses dari Model yang baru
            const rawPeople = @json($people);
            const familyId = "{{ $family->id }}";

            if (!rawPeople || rawPeople.length === 0) {
                document.getElementById('tree-wrapper').innerHTML = '<div class="flex h-full flex-col items-center justify-center text-slate-400 font-bold h-[75vh]"><span class="text-4xl mb-3">🌳</span>Belum ada data keluarga. Klik "+ Leluhur Utama" untuk memulai.</div>';
                document.getElementById('filter-node').style.display = 'none';
                return;
            }

            let familyData = [];
            let nodeMap = {};

            // ------------------------------------------
            // 1. MAPPING DATA UTAMA (SUPER BERSIH)
            // ------------------------------------------
            rawPeople.forEach(person => {
                // Ekstrak ID pasangan dari relasi spouses (marriages)
                let spouseIds = [];
                if (person.spouses && person.spouses.length > 0) {
                    spouseIds = person.spouses.map(s => s.id);
                }

                let personNode = {
                    id: person.id,                 // ID Asli database (Tidak perlu p_)
                    fid: person.father_id,         // Ambil langsung dari tabel
                    mid: person.mother_id,         // Ambil langsung dari tabel
                    pids: spouseIds,               // Array ID pasangan
                    name: person.name,
                    gender: person.gender === 'L' ? 'male' : 'female',
                    img: person.photo_path
                         ? "{{ url('storage') }}/" + person.photo_path
                         : `https://ui-avatars.com/api/?name=${encodeURIComponent(person.name)}&background=0D8ABC&color=fff`
                };

                familyData.push(personNode);
                nodeMap[person.id] = personNode;
            });


            // ------------------------------------------
            // 2. FUNGSI RENDER ULANG KANVAS
            // ------------------------------------------
            function renderTree(dataToRender) {
                document.getElementById('tree-wrapper').innerHTML = '<div id="tree" style="width: 100%; height: 75vh;"></div>';

                let chart = new FamilyTree(document.getElementById("tree"), {
                    template: "tommy",
                    orientation: FamilyTree.orientation.top,
                    layout: FamilyTree.mixed,
                    mouseScrol: FamilyTree.action.zoom,
                    nodeBinding: {
                        field_0: "name",
                        img_0: "img"
                    },
                    editForm: { readOnly: true },

                    menu: {
                        pdf: { text: "📄 Cetak / Simpan PDF" },
                        png: { text: "🖼️ Simpan Gambar (PNG)" },
                        svg: { text: "📐 Simpan Vektor (SVG)" },
                        csv: { text: "📊 Export Data (Excel/CSV)" }
                    },

                    nodeMenu: {
                        add_parent: {
                            text: "👴 Tambah Orang Tua",
                            onClick: function(nodeId) {
                                // Karena semua adalah Person, tidak ada lagi error blokir klik pasangan
                                window.location.href = `/family/${familyId}/person/create?child_id=${nodeId}`;
                            }
                        },
                        add_child: {
                            text: "👶 Tambah Anak",
                            onClick: function(nodeId) {
                                window.location.href = `/family/${familyId}/person/create?parent_id=${nodeId}`;
                            }
                        },
                        add_spouse: {
                            text: "💍 Tambah Pasangan",
                            onClick: function(nodeId) {
                                window.location.href = `/family/${familyId}/person/${nodeId}/spouse/create`;
                            }
                        },
                        edit_data: {
                            text: "✏️ Edit Data",
                            onClick: function(nodeId) {
                                // Sangat bersih, satu route edit untuk semua orang
                                window.location.href = `/family/${familyId}/person/${nodeId}/edit`;
                            }
                        },
                        remove_data: {
                            text: "🗑️ Hapus",
                            onClick: function(nodeId) {
                                let node = nodeMap[nodeId];
                                if (confirm(`Yakin ingin menghapus ${node.name}?`)) {
                                    const form = document.getElementById('action-form');
                                    // Sangat bersih, satu route delete untuk semua orang
                                    form.action = `/family/${familyId}/person/${nodeId}`;
                                    form.submit();
                                }
                            }
                        }
                    }
                });

                chart.on('click', function (sender, args) {
                    let nodeId = args.node.id;
                    if (nodeMap[nodeId]) {
                        window.location.href = `/family/${familyId}/person/${nodeId}/edit`;
                    }
                    return false;
                });

                chart.load(dataToRender);
            }


            // ------------------------------------------
            // 3. LOGIKA FILTER KETURUNAN
            // ------------------------------------------
            const filterSelect = document.getElementById('filter-node');

            // Masukkan daftar nama ke Dropdown (Tidak perlu difilter is_spouse)
            rawPeople.forEach(person => {
                let option = document.createElement('option');
                option.value = person.id;
                option.text = "🌿 " + person.name;
                filterSelect.appendChild(option);
            });

            filterSelect.addEventListener('change', function() {
                // Pastikan yang diambil adalah integer karena ID dari db sekarang angka murni
                const selectedId = this.value === 'all' ? 'all' : parseInt(this.value);

                if (selectedId === 'all') {
                    renderTree(familyData);
                    return;
                }

                let resultIds = new Set();
                let queue = [selectedId];
                resultIds.add(selectedId);

                // Tarik pasangan si target
                familyData.forEach(n => {
                    if (n.pids && n.pids.includes(selectedId)) {
                        resultIds.add(n.id);
                    }
                });

                // Tarik keturunan
                while(queue.length > 0) {
                    let currentId = queue.shift();

                    familyData.forEach(node => {
                        if (node.fid === currentId || node.mid === currentId) {
                            if (!resultIds.has(node.id)) {
                                resultIds.add(node.id);
                                queue.push(node.id);

                                // Tarik pasangan keturunan
                                familyData.forEach(s => {
                                    if (s.pids && s.pids.includes(node.id)) resultIds.add(s.id);
                                });
                            }
                        }
                    });
                }

                let safeFilteredData = [];
                familyData.forEach(node => {
                    if (resultIds.has(node.id)) {
                        let safeNode = Object.assign({}, node);

                        // Potong ikatan ke atas agar tidak ada garis putus
                        if (safeNode.id === selectedId || (safeNode.pids && safeNode.pids.includes(selectedId))) {
                            delete safeNode.fid;
                            delete safeNode.mid;
                        }
                        safeFilteredData.push(safeNode);
                    }
                });

                renderTree(safeFilteredData);
            });

            // ==========================================
            // RENDER AWAL
            // ==========================================
            renderTree(familyData);
        });
    </script>

    <style>
        .balkan-link {
            display: none !important;
        }

        #tree svg {
            background-color: transparent !important;
            cursor: pointer;
        }

        .bft-menu {
            background: white !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 8px !important;
            z-index: 1000 !important;
        }

        .bft-menu li {
            color: #334155 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 10px 15px !important;
            border-radius: 6px !important;
            cursor: pointer;
            transition: all 0.2s;
        }

        .bft-menu li:hover {
            background-color: #f1f5f9 !important;
            color: #2563eb !important;
        }
    </style>
</x-app-layout>