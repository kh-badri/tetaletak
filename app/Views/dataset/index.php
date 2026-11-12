<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div x-data="{ 
    currentTab: '<?= esc($active_tab ?? 'produk') ?>',
    searchProduk: '',
    filterKategori: '',
    searchTransaksi: '',
    addProdukModalOpen: false,
    addTransaksiModalOpen: false,
    uploadCsvModalOpen: false,
    editModalOpen: false,
    editData: { id: '', nama_produk: '', kategori_produk: '', harga_satuan: '', satuan: '' },
    openEditModal(prod) {
        this.editData = { ...prod };
        this.editModalOpen = true;
    }
}" class="min-h-screen bg-[#FFE1AF] py-6 px-4 sm:px-6 text-[#957C62]">

    <div class="container mx-auto max-w-7xl">

        <!-- Alert Error jika ada -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-2xl mb-5 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs sm:text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- =================================================================== -->
        <!-- HEADER RINGKAS & STATISTIK INLINE -->
        <!-- =================================================================== -->
        <div class="bg-white rounded-3xl p-6 mb-6 shadow-md border border-[#E2B59A]/40 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#957C62]">Data Produk &amp; Transaksi Toko</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola daftar barang sembako dan riwayat struk belanja pelanggan untuk analisis tata letak.</p>
                
                <!-- Ringkasan Statistik Simpel -->
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FFE1AF]/60 text-xs font-bold text-[#957C62]">
                        <span class="w-2 h-2 rounded-full bg-[#B77466]"></span>
                        <?= number_format($totalMasterProduk ?? 0, 0, ',', '.') ?> Produk Sembako
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FFE1AF]/60 text-xs font-bold text-[#957C62]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <?= number_format($totalTrx ?? 0, 0, ',', '.') ?> Struk Belanja
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600">
                        <?= number_format($totalData ?? 0, 0, ',', '.') ?> Item Tercatat
                    </span>
                </div>
            </div>

            <!-- Tombol Cepat CSV -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="<?= base_url('public/dataset_transaksi_sembako_2027.csv') ?>" download
                   class="inline-flex items-center gap-2 bg-[#FFE1AF]/70 hover:bg-[#FFE1AF] text-[#957C62] border border-[#E2B59A] px-4 py-2.5 rounded-xl font-bold text-xs transition">
                    <svg class="w-4 h-4 text-[#B77466]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Contoh CSV
                </a>
                <a href="<?= base_url('dataset/export') ?>" 
                   class="inline-flex items-center gap-2 bg-[#957C62] hover:bg-[#7c664f] text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Data (CSV)
                </a>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- NAVIGASI TAB TUNGGAL (PRODUK vs TRANSAKSI) -->
        <!-- =================================================================== -->
        <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl border border-[#E2B59A]/50 shadow-sm mb-6 max-w-xl">
            <button type="button" @click="currentTab = 'produk'" 
                    :class="currentTab === 'produk' ? 'bg-[#B77466] text-white shadow' : 'text-[#957C62] hover:bg-[#FFE1AF]/30'"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <span>1. Daftar Produk</span>
                <span class="px-2 py-0.5 rounded-full text-[11px]" :class="currentTab === 'produk' ? 'bg-white/25 text-white' : 'bg-[#FFE1AF] text-[#957C62]'"><?= $totalMasterProduk ?></span>
            </button>

            <button type="button" @click="currentTab = 'transaksi'" 
                    :class="currentTab === 'transaksi' ? 'bg-[#B77466] text-white shadow' : 'text-[#957C62] hover:bg-[#FFE1AF]/30'"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <span>2. Riwayat Transaksi</span>
                <span class="px-2 py-0.5 rounded-full text-[11px]" :class="currentTab === 'transaksi' ? 'bg-white/25 text-white' : 'bg-[#FFE1AF] text-[#957C62]'"><?= number_format($totalData, 0, ',', '.') ?></span>
            </button>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 1: DAFTAR MASTER PRODUK SEMBAKO (FULL WIDTH & BERSIH) -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'produk'" x-transition.opacity>
            <div class="bg-white rounded-3xl shadow-lg border border-[#E2B59A]/30 overflow-hidden">
                
                <!-- Toolbar Pencarian & Tambah Produk -->
                <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                        <div class="relative flex-1 max-w-xs">
                            <input type="text" x-model="searchProduk" placeholder="Cari nama barang..." 
                                   class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-gray-200 text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#B77466]">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <select x-model="filterKategori" 
                                class="px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#B77466]">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategoriList as $kat) : ?>
                                <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" @click="addProdukModalOpen = true"
                            class="inline-flex items-center justify-center gap-2 bg-[#B77466] hover:bg-[#957C62] text-white font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Produk Baru
                    </button>
                </div>

                <!-- Tabel Produk -->
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full text-xs sm:text-sm text-left">
                        <thead class="sticky top-0 bg-gray-100 text-[#957C62] text-[11px] uppercase font-bold tracking-wider z-10 border-b">
                            <tr>
                                <th class="px-4 py-3 text-center w-14">No</th>
                                <th class="px-4 py-3">Nama Produk Sembako</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                <th class="px-4 py-3 text-center">Satuan</th>
                                <th class="px-4 py-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-800">
                            <?php if (empty($daftarProduk)) : ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada produk sembako. Klik tombol <strong>+ Tambah Produk Baru</strong> untuk memulai.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($daftarProduk as $idx => $prod) : ?>
                                    <tr data-search="<?= strtolower(esc($prod['nama_produk'])) ?>"
                                        data-kategori="<?= esc($prod['kategori_produk']) ?>"
                                        x-show="
                                            (searchProduk === '' || $el.dataset.search.includes(searchProduk.toLowerCase())) &&
                                            (filterKategori === '' || $el.dataset.kategori === filterKategori)
                                        " class="hover:bg-[#FFE1AF]/20 transition">
                                        <td class="px-4 py-3 text-center font-bold text-gray-400">
                                            <?= $idx + 1 ?>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            <?= esc($prod['nama_produk']) ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                                                <?= esc($prod['kategori_produk']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-gray-700">
                                            Rp <?= number_format($prod['harga_satuan'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-xs text-gray-500">
                                            <?= esc($prod['satuan'] ?? 'Pcs') ?>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button" 
                                                        @click="openEditModal(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8') ?>)"
                                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Produk">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <form action="<?= base_url('dataset/produk/delete/' . $prod['id']) ?>" method="post" 
                                                      onsubmit="return confirm('Hapus produk <?= esc($prod['nama_produk'], 'js') ?>?');" class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 2: RIWAYAT TRANSAKSI PEMBELIAN (FULL WIDTH & BERSIH) -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'transaksi'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-3xl shadow-lg border border-[#E2B59A]/30 overflow-hidden">
                
                <!-- Toolbar Pencarian & Aksi Transaksi -->
                <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3 bg-gray-50/50">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="relative flex-1 max-w-sm">
                            <input type="text" x-model="searchTransaksi" placeholder="Cari nomor struk atau nama barang..." 
                                   class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-gray-200 text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#B77466]">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500 hidden sm:inline">Menampilkan <?= count($dataset) ?> transaksi terbaru</span>
                    </div>

                    <!-- Tombol Aksi Transaksi -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="addTransaksiModalOpen = true"
                                class="inline-flex items-center gap-1.5 bg-[#B77466] hover:bg-[#957C62] text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Catat Transaksi
                        </button>

                        <button type="button" @click="uploadCsvModalOpen = true"
                                class="inline-flex items-center gap-1.5 bg-[#957C62] hover:bg-[#7c664f] text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import CSV
                        </button>

                        <form action="<?= base_url('dataset/hapusSemua') ?>" method="post" 
                              onsubmit="return confirm('PERINGATAN! Semua data transaksi akan dihapus permanen. Lanjutkan?');" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold px-3.5 py-2 rounded-xl text-xs sm:text-sm transition"
                                    title="Kosongkan semua data transaksi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span class="hidden sm:inline">Kosongkan</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabel Data Transaksi -->
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full text-xs sm:text-sm text-left">
                        <thead class="sticky top-0 bg-gray-100 text-[#957C62] text-[11px] uppercase font-bold tracking-wider z-10 border-b">
                            <tr>
                                <th class="px-4 py-3">No Struk</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Nama Produk Sembako</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center w-16">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-800">
                            <?php if (empty($dataset)) : ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada data transaksi. Klik <strong>Import CSV</strong> atau <strong>Catat Transaksi</strong> di atas.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($dataset as $i => $row) : ?>
                                    <tr data-search="<?= strtolower(esc(($row['no_transaksi'] ?? '') . ' ' . ($row['nama_produk'] ?? ''))) ?>"
                                        x-show="searchTransaksi === '' || $el.dataset.search.includes(searchTransaksi.toLowerCase())"
                                        class="<?= ($i % 2 == 0) ? 'bg-white' : 'bg-gray-50/40' ?> hover:bg-[#FFE1AF]/20 transition">
                                        <td class="px-4 py-2.5 whitespace-nowrap font-mono text-xs font-semibold text-[#B77466]">
                                            <?= esc($row['no_transaksi'] ?: 'TRX-' . $row['id']) ?>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-gray-500 text-xs">
                                            <?= date('d/m/Y', strtotime($row['waktu_penjualan'])) ?>
                                        </td>
                                        <td class="px-4 py-2.5 font-medium text-gray-900">
                                            <?= esc($row['nama_produk']) ?>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-xs">
                                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                                <?= esc($row['kategori_produk']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-right text-gray-600">
                                            Rp <?= number_format($row['harga_produk_rp'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-bold text-gray-800">
                                            <?= $row['jumlah_terjual_unit'] ?>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-right font-bold text-[#957C62]">
                                            Rp <?= number_format($row['total_harga'] ?: ($row['harga_produk_rp'] * $row['jumlah_terjual_unit']), 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <form action="<?= base_url('dataset/delete/' . $row['id']) ?>" method="post" onsubmit="return confirm('Hapus item transaksi ini?');" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Baris">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- =================================================================== -->
    <!-- MODAL 1: TAMBAH PRODUK SEMBAKO BARU -->
    <!-- =================================================================== -->
    <div x-show="addProdukModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addProdukModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-[#E2B59A]/50 relative"
             x-transition>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-black text-[#957C62] text-lg">Tambah Produk Sembako</h3>
                <button @click="addProdukModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form action="<?= base_url('dataset/produk/save') ?>" method="post" class="space-y-3.5">
                <?= csrf_field() ?>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Nama Produk</label>
                    <input type="text" name="nama_produk" placeholder="Contoh: Beras Rojolele 5kg" required 
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                </div>

                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Kategori</label>
                    <input type="text" name="kategori_produk" list="kategori_datalist_modal" placeholder="Pilih atau ketik kategori..." required 
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                    <datalist id="kategori_datalist_modal">
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= esc($k) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Harga (Rp)</label>
                        <input type="number" name="harga_satuan" placeholder="72000" required 
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Satuan</label>
                        <input type="text" name="satuan" value="Pcs" placeholder="Kg / Pcs / Botol" required 
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="addProdukModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#B77466] hover:bg-[#957C62] text-white shadow-md">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL 2: EDIT PRODUK SEMBAKO -->
    <!-- =================================================================== -->
    <div x-show="editModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-[#E2B59A]/50 relative"
             x-transition>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-black text-[#957C62] text-lg">Edit Produk Sembako</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form :action="'<?= base_url('dataset/produk/update') ?>/' + editData.id" method="post" class="space-y-3.5">
                <?= csrf_field() ?>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Nama Produk</label>
                    <input type="text" name="nama_produk" x-model="editData.nama_produk" required 
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                </div>

                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Kategori</label>
                    <input type="text" name="kategori_produk" x-model="editData.kategori_produk" required 
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Harga (Rp)</label>
                        <input type="number" name="harga_satuan" x-model="editData.harga_satuan" required 
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Satuan</label>
                        <input type="text" name="satuan" x-model="editData.satuan" required 
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#B77466] hover:bg-[#957C62] text-white shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL 3: CATAT TRANSAKSI MANUAL -->
    <!-- =================================================================== -->
    <div x-show="addTransaksiModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addTransaksiModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-[#E2B59A]/50 relative"
             x-transition>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-black text-[#957C62] text-lg">Catat Transaksi Pembelian</h3>
                <button @click="addTransaksiModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form action="<?= base_url('dataset/save') ?>" method="post" class="space-y-3.5">
                <?= csrf_field() ?>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">No Struk (Opsional)</label>
                        <input type="text" name="no_transaksi" placeholder="Otomatis jika kosong" 
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs text-gray-900">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Tanggal</label>
                        <input type="date" name="waktu_penjualan" value="<?= date('Y-m-d') ?>" required 
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs text-gray-900">
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Pilih Produk Sembako</label>
                    <select name="nama_produk" required 
                            @change="
                                let sel = $event.target.selectedOptions[0];
                                document.getElementById('input_kat_modal').value = sel.getAttribute('data-kat') || '';
                                document.getElementById('input_hrg_modal').value = sel.getAttribute('data-hrg') || '';
                                document.getElementById('input_id_prod_modal').value = sel.getAttribute('data-id') || '';
                            "
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs sm:text-sm text-gray-900">
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($daftarProduk as $p): ?>
                            <option value="<?= esc($p['nama_produk']) ?>" 
                                    data-id="<?= $p['id'] ?>"
                                    data-kat="<?= esc($p['kategori_produk']) ?>" 
                                    data-hrg="<?= $p['harga_satuan'] ?>">
                                <?= esc($p['nama_produk']) ?> (Rp <?= number_format($p['harga_satuan'], 0, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_produk" id="input_id_prod_modal">
                </div>

                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Kategori</label>
                    <input type="text" name="kategori_produk" id="input_kat_modal" placeholder="Terisi otomatis" required 
                           class="w-full px-3.5 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Harga (Rp)</label>
                        <input type="number" name="harga_produk_rp" id="input_hrg_modal" placeholder="0" required 
                               class="w-full px-3.5 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-900">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Jumlah Beli</label>
                        <input type="number" name="jumlah_terjual_unit" value="1" min="1" required 
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] text-xs text-gray-900">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="addTransaksiModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#B77466] hover:bg-[#957C62] text-white shadow-md">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL 4: IMPORT CSV SEKALIGUS -->
    <!-- =================================================================== -->
    <div x-show="uploadCsvModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="uploadCsvModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-[#E2B59A]/50 relative"
             x-transition>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-black text-[#957C62] text-lg">Import Data Transaksi (CSV)</h3>
                <button @click="uploadCsvModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="bg-amber-50 p-3.5 rounded-2xl border border-amber-200/80 text-xs text-amber-900 mb-4 leading-relaxed">
                <strong class="block mb-1">Perhatian:</strong>
                Upload file CSV (pemisah koma <code class="bg-white px-1 rounded">,</code>) akan menggantikan seluruh riwayat transaksi lama dengan data baru di dalam file.
            </div>

            <form action="<?= base_url('dataset/upload') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 uppercase">Pilih File (.csv)</label>
                    <input type="file" name="dataset_csv" accept=".csv,text/csv,text/plain" required
                           class="block w-full text-xs text-[#957C62] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-[#B77466] file:text-white hover:file:bg-[#957C62] cursor-pointer border border-gray-200 rounded-xl p-1.5 bg-gray-50">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="uploadCsvModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#B77466] hover:bg-[#957C62] text-white shadow-md">Upload &amp; Proses File</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
