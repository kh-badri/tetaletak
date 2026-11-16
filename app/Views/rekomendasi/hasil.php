<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<?php
    $manual      = $hasil['perhitungan_manual'] ?? [];
    $ringkasan   = $manual['ringkasan'] ?? [];
    $kasus       = $manual['contoh_kasus'] ?? null;
    $sampel10    = $manual['sampel_10'] ?? [];
    $c1Lengkap   = $manual['c1_lengkap'] ?? [];
    $rekomendasi = $hasil['rekomendasi_tataletak'] ?? [];
    $top3        = array_slice($rekomendasi, 0, 3);
    $totalTrx    = $hasil['total_transaksi'] ?? 788;
?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-6 px-4 sm:px-6 text-slate-800" 
     x-data="{ 
        activeTab: 'rekomendasi', 
        subTabTeknis: 'rules',
        subPerhitunganMode: 'dataset',
        subTabSampel: 'tabel41',
        searchRek: '',
        filterTipe: '',
        searchTeknis: ''
     }">
    <div class="container mx-auto max-w-7xl">

        <!-- =================================================================== -->
        <!-- BARIS ATAS: KEMBALI & SIMPAN -->
        <!-- =================================================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
            <a href="<?= base_url('rekomendasi') ?>" 
               class="inline-flex items-center gap-2 bg-white text-slate-700 hover:text-[#0284C7] border border-sky-200 px-4 py-2 rounded-xl font-bold text-xs sm:text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Ubah Parameter
            </a>

            <?php if (session()->has('hasil_rekomendasi_terakhir')) : ?>
                <?= form_open('rekomendasi/simpan') ?>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white font-bold py-2.5 px-5 rounded-xl shadow-md shadow-sky-500/20 hover:shadow-lg transition text-xs sm:text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Hasil ke Riwayat
                    </button>
                <?= form_close() ?>
            <?php endif; ?>
        </div>

        <!-- =================================================================== -->
        <!-- RINGKASAN SINGKAT (4 KARTU) -->
        <!-- =================================================================== -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 mb-6">
            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Total Data Dianalisis</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">
                    <?= number_format($hasil['total_transaksi'] ?? 0, 0, ',', '.') ?> <span class="text-xs font-normal text-slate-500">struk</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Rekomendasi Penataan</span>
                <div class="text-xl sm:text-2xl font-black text-[#0284C7] mt-0.5">
                    <?= count($rekomendasi) ?> <span class="text-xs font-normal text-slate-500">pasang rak</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Pasangan Terbentuk</span>
                <div class="text-xl sm:text-2xl font-black text-indigo-600 mt-0.5">
                    <?= count($hasil['itemsets_2'] ?? []) ?> <span class="text-xs font-normal text-slate-500">kombinasi</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Standar Uji Digunakan</span>
                <div class="text-xs sm:text-sm font-bold text-slate-800 mt-1">
                    Supp <?= $hasil['min_support'] ?>% &bull; Conf <?= $hasil['min_confidence'] ?>%
                </div>
                <span class="text-[11px] text-teal-600 font-semibold">Min Lift <?= $hasil['min_lift'] ?></span>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- NAVIGASI 2 TAB UTAMA -->
        <!-- =================================================================== -->
        <div class="flex flex-col sm:flex-row gap-2 p-1.5 bg-white rounded-2xl border border-sky-100 shadow-sm mb-6">
            <button type="button" @click="activeTab = 'rekomendasi'" 
                    :class="activeTab === 'rekomendasi' ? 'bg-[#0284C7] text-white shadow-sm' : 'text-slate-600 hover:bg-sky-50'"
                    class="flex-1 py-3 px-5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Panduan Penataan Rak (Bahasa Awam)</span>
            </button>

            <button type="button" @click="activeTab = 'perhitungan'" 
                    :class="activeTab === 'perhitungan' ? 'bg-[#0284C7] text-white shadow-sm' : 'text-slate-600 hover:bg-sky-50'"
                    class="flex-1 py-3 px-5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>Perhitungan Manual &amp; Cara Kerja Apriori</span>
            </button>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 1: PANDUAN PENATAAN RAK (SEDERHANA & MUDAH DIPAHAMI) -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'rekomendasi'" x-transition.opacity>

            <!-- 1. TOP 3 SIMULASI RAK PRIORITAS -->
            <div class="bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                <div class="mb-5 pb-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">3 Pasangan Produk Prioritas Utama</h2>
                        <p class="text-xs text-slate-500">Barang-barang di bawah paling sering dibeli berbarengan, wajib diposisikan berdekatan agar pembeli langsung mengambil keduanya.</p>
                    </div>
                </div>

                <?php if (!empty($top3)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <?php foreach ($top3 as $idx => $rek): ?>
                            <div class="bg-sky-50/30 rounded-2xl p-4 border-2 border-sky-200 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-black text-[#0284C7]">PRIORITAS #<?= $idx + 1 ?></span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200 text-[10px] font-bold">
                                            <?= $rek['lift'] ?>x Lebih Sering
                                        </span>
                                    </div>

                                    <!-- Visual Rak Minimalis -->
                                    <div class="bg-sky-100/60 p-3 rounded-xl border border-sky-200 mb-3">
                                        <div class="grid grid-cols-2 gap-2 text-center items-stretch">
                                            <div class="bg-white p-2.5 rounded-lg border-2 border-[#0284C7] flex flex-col justify-center">
                                                <span class="text-[9px] font-bold text-[#0284C7] uppercase">Barang Utama</span>
                                                <div class="text-xs font-bold text-slate-900 mt-0.5 leading-tight"><?= esc($rek['produk_utama']) ?></div>
                                            </div>
                                            <div class="bg-white p-2.5 rounded-lg border-2 border-indigo-400 flex flex-col justify-center relative">
                                                <span class="text-[9px] font-bold text-indigo-600 uppercase">Pendamping</span>
                                                <div class="text-xs font-bold text-slate-900 mt-0.5 leading-tight"><?= esc($rek['produk_asosiasi']) ?></div>
                                                <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-[#0284C7] text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow">&harr;</div>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-700 h-1.5 rounded-full mt-2.5"></div>
                                        <div class="text-center text-[10px] font-bold text-[#0369A1] mt-1.5">
                                            Taruh di: <?= esc($rek['posisi_rak']) ?>
                                        </div>
                                    </div>

                                    <!-- Fakta & Saran Singkat -->
                                    <p class="text-xs text-slate-700 leading-relaxed mb-2">
                                        <strong class="text-[#0284C7]"><?= round($rek['confidence']) ?> dari 100 pembeli</strong> barang utama juga membeli pendampingnya.
                                    </p>
                                    <p class="text-[11px] text-slate-600 bg-white p-2.5 rounded-xl border border-sky-100 leading-relaxed">
                                        <?= esc(str_replace(['**', '##'], '', $rek['saran_tata_letak'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-slate-500 py-6 text-xs">Tidak ada rekomendasi tata letak yang memenuhi batas minimum.</p>
                <?php endif; ?>
            </div>

            <!-- 2. DAFTAR LENGKAP REKOMENDASI (RINGKAS + BISA DICARI) -->
            <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-6">
                <div class="p-5 border-b border-sky-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-sky-50/30">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900">Daftar Lengkap Saran Penataan Rak</h2>
                        <p class="text-xs text-slate-500">Total <?= count($rekomendasi) ?> pasangan barang siap ditata di toko</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <input type="text" x-model="searchRek" placeholder="Cari nama barang..." 
                               class="px-3 py-2 rounded-xl bg-white border border-sky-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <select x-model="filterTipe" 
                                class="px-3 py-2 rounded-xl bg-white border border-sky-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">Semua Jenis Penataan</option>
                            <option value="sama">Satu Rak Bersebelahan</option>
                            <option value="beda">Penataan Silang / Ujung Lorong</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase font-bold tracking-wider border-b border-sky-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Pasangan Produk (Taruh Berdekatan)</th>
                                <th class="px-4 py-3">Posisi Rak Toko</th>
                                <th class="px-4 py-3 text-center">Peluang Beli Bareng</th>
                                <th class="px-4 py-3">Panduan Aksi untuk Penjaga Toko</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            <?php if (empty($rekomendasi)) : ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada rekomendasi yang terbentuk.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($rekomendasi as $idx => $r) : ?>
                                    <?php $isSame = $r['is_same_category'] ?? ($r['kategori_utama'] === $r['kategori_asosiasi']); ?>
                                    <tr data-search="<?= strtolower(esc($r['produk_utama'] . ' ' . $r['produk_asosiasi'])) ?>"
                                        x-show="(searchRek === '' || $el.dataset.search.includes(searchRek.toLowerCase())) && (filterTipe === '' || filterTipe === '<?= $isSame ? 'sama' : 'beda' ?>')"
                                        class="hover:bg-sky-50/50 transition">
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-bold text-slate-900">
                                                <?= esc($r['produk_utama']) ?> 
                                                <span class="text-[#0284C7] mx-1">&harr;</span> 
                                                <span class="text-[#0284C7]"><?= esc($r['produk_asosiasi']) ?></span>
                                            </div>
                                            <span class="text-[10px] text-slate-500">
                                                <?= esc($r['kategori_utama']) ?> &bull; <?= esc($r['kategori_asosiasi']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold <?= $isSame ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' ?>">
                                                <?= $isSame ? 'Satu Rak Bersebelahan' : 'Ujung Lorong / Silang' ?>
                                            </span>
                                            <div class="text-[11px] text-slate-500 mt-0.5"><?= esc($r['posisi_rak']) ?></div>
                                        </td>
                                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                            <div class="font-black text-[#0284C7]"><?= round($r['confidence']) ?>% Pembeli</div>
                                            <span class="text-[10px] font-bold text-teal-700"><?= $r['lift'] ?>x lebih sering</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-xs text-slate-600 leading-relaxed max-w-md">
                                            <?= esc(str_replace(['**', '##'], '', $r['saran_tata_letak'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. TIPS PRAKTIS RINGKAS -->
            <div class="bg-white rounded-2xl p-5 border border-sky-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="flex items-start gap-3">
                    <span class="text-lg">👁️</span>
                    <div>
                        <strong class="text-slate-900 block">Tinggi Pandangan Mata</strong>
                        <span class="text-slate-600">Taruh barang prioritas di rak tengah (120–140 cm) agar paling mudah terlihat.</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-lg">🛒</span>
                    <div>
                        <strong class="text-slate-900 block">Keranjang Pelengkap</strong>
                        <span class="text-slate-600">Untuk barang beda kategori, pasang gantungan kecil tepat di sebelah barang utama.</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-lg">📦</span>
                    <div>
                        <strong class="text-slate-900 block">Jaga Stok Bersamaan</strong>
                        <span class="text-slate-600">Pastikan stok barang utama dan pendamping terisi bersamaan agar tidak kehabisan.</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- =================================================================== -->
        <!-- TAB 2: PERHITUNGAN MANUAL & CARA KERJA APRIORI (TEKNIS) -->
        <!-- =================================================================== -->
        <div x-show="activeTab === 'perhitungan'" x-transition.opacity style="display: none;">

            <!-- PILIHAN SUB-MODE PERHITUNGAN: DATASET UTAMA VS SIMULASI SAMPEL 10 -->
            <div class="flex flex-col sm:flex-row gap-2 p-1.5 bg-sky-100/70 rounded-2xl border border-sky-200 shadow-inner mb-6">
                <button type="button" @click="subPerhitunganMode = 'dataset'"
                        :class="subPerhitunganMode === 'dataset' ? 'bg-white text-[#0284C7] shadow font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16M9 4v16"/>
                    </svg>
                    <span>Hasil Seluruh Dataset (788 Transaksi) &bull; Tabel 4.10 &amp; 4.11</span>
                </button>
                <button type="button" @click="subPerhitunganMode = 'sampel10'"
                        :class="subPerhitunganMode === 'sampel10' ? 'bg-white text-[#0284C7] shadow font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Simulasi Manual 10 Sampel Pertama &bull; Tabel 4.1 s.d. 4.9</span>
                </button>
            </div>

            <!-- =============================================================== -->
            <!-- SUB-VIEW 1: HASIL PENGOLAHAN SELURUH DATASET (788 TRANSAKSI)   -->
            <!-- =============================================================== -->
            <div x-show="subPerhitunganMode === 'dataset'">
                <!-- Parameter Box Skripsi -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-sky-100">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">Hasil Pengolahan Algoritma Apriori (Seluruh Dataset)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Sesuai Bab 4 Skripsi: Pengolahan seluruh 788 transaksi Toko Sembako Junita (Agustus &ndash; Oktober 2026)</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="px-3 py-1.5 rounded-xl bg-sky-50 text-[#0284C7] border border-sky-200 font-bold">Min Support: <?= $hasil['min_support'] ?>% (&ge; <?= $hasil['min_support_count'] ?> transaksi)</span>
                            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold">Min Confidence: <?= $hasil['min_confidence'] ?>%</span>
                            <span class="px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 font-bold">Min Lift: <?= $hasil['min_lift'] ?></span>
                        </div>
                    </div>

                    <!-- TABEL 4.10: Ringkasan Pembentukan Frequent Itemset pada Seluruh Dataset -->
                    <div class="mt-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#0284C7]"></span>
                                <span>Tabel 4.10 Ringkasan Pembentukan Frequent Itemset pada Seluruh Dataset</span>
                            </h3>
                            <span class="text-[11px] text-slate-400">Total: 788 Transaksi</span>
                        </div>
                        <div class="overflow-x-auto border border-sky-100 rounded-2xl">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-sky-50/60 text-slate-700 font-bold uppercase text-[11px]">
                                    <tr>
                                        <th class="px-4 py-3">Tahap Pembentukan</th>
                                        <th class="px-4 py-3 text-center">Jumlah Kandidat</th>
                                        <th class="px-4 py-3 text-center">Frequent Itemset (Lolos)</th>
                                        <th class="px-4 py-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-100 text-slate-800 bg-white">
                                    <?php 
                                        $t410 = $sampel10['tabel_4_10'] ?? [
                                            ['tahap' => '1-itemset (C1/L1)', 'kandidat' => 95, 'frequent' => 32, 'keterangan' => '32 produk memenuhi minimum support 3%.'],
                                            ['tahap' => '2-itemset (C2/L2)', 'kandidat' => 496, 'frequent' => 36, 'keterangan' => '36 kombinasi memenuhi minimum support 3%.'],
                                            ['tahap' => '3-itemset (C3/L3)', 'kandidat' => 19, 'frequent' => 0, 'keterangan' => 'Tidak ada kandidat yang memenuhi minimum support.'],
                                            ['tahap' => '4-itemset dan seterusnya', 'kandidat' => 'Tidak terbentuk', 'frequent' => 0, 'keterangan' => 'Proses dihentikan karena L3 tidak terbentuk.']
                                        ];
                                        foreach ($t410 as $r410): 
                                    ?>
                                        <tr class="hover:bg-sky-50/40">
                                            <td class="px-4 py-2.5 font-bold text-slate-900"><?= esc($r410['tahap']) ?></td>
                                            <td class="px-4 py-2.5 text-center font-semibold text-slate-700"><?= esc($r410['kandidat']) ?></td>
                                            <td class="px-4 py-2.5 text-center font-black text-[#0284C7]"><?= esc($r410['frequent']) ?></td>
                                            <td class="px-4 py-2.5 text-slate-600"><?= esc($r410['keterangan']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TABEL 4.11: Hasil Aturan Asosiasi pada Seluruh Dataset (22 Aturan Resmi) -->
                <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-8">
                    <div class="p-4 sm:p-5 bg-gradient-to-r from-[#0284C7] to-[#0369A1] text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <h3 class="text-sm sm:text-base font-black">Tabel 4.11 Hasil Aturan Asosiasi pada Seluruh Dataset</h3>
                            <p class="text-xs text-sky-100">Daftar 22 aturan asosiasi final yang memenuhi Min Support 3%, Min Confidence 50%, dan Min Lift 1,2</p>
                        </div>
                        <input type="text" x-model="searchTeknis" placeholder="Cari nama produk di aturan..." 
                               class="px-3.5 py-1.5 rounded-xl bg-white text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-sky-300 w-full sm:w-64 shadow-inner">
                    </div>

                    <div class="overflow-x-auto max-h-[550px] overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10 text-[11px]">
                                <tr>
                                    <th class="px-3 py-3 text-center">No</th>
                                    <th class="px-4 py-3">Aturan Asosiasi (A &rarr; B)</th>
                                    <th class="px-3 py-3 text-center">Frekuensi (A / B / AB)</th>
                                    <th class="px-3 py-3 text-center">Support</th>
                                    <th class="px-3 py-3 text-center">Confidence</th>
                                    <th class="px-3 py-3 text-center">Lift Ratio</th>
                                    <th class="px-3 py-3 text-center">Korelasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                <?php foreach (($hasil['rules'] ?? []) as $idx => $rule) : ?>
                                    <?php 
                                        $cntA  = $rule['count_a'] ?? 0;
                                        $cntB  = $rule['count_b'] ?? 0;
                                        $cntAB = $rule['jumlah_transaksi'] ?? 0;
                                        $confDec = number_format(($rule['confidence'] ?? 0) / 100, 4);
                                    ?>
                                    <tr data-search="<?= strtolower(esc($rule['antecedent'] . ' ' . $rule['consequent'])) ?>"
                                        x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                        class="hover:bg-sky-50/50 transition">
                                        <td class="px-3 py-3 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-3">
                                            <span class="font-bold text-slate-900"><?= esc($rule['antecedent']) ?></span>
                                            <span class="text-[#0284C7] font-black mx-1">&rarr;</span>
                                            <span class="font-bold text-indigo-700"><?= esc($rule['consequent']) ?></span>
                                        </td>
                                        <td class="px-3 py-3 text-center font-mono text-[11px] whitespace-nowrap text-slate-600">
                                            <?= $cntA ?> / <?= $cntB ?> / <strong class="text-slate-900"><?= $cntAB ?></strong>
                                        </td>
                                        <td class="px-3 py-3 text-center font-mono text-[11px] whitespace-nowrap">
                                            <?= $cntAB ?>/<?= $totalTrx ?> = <strong class="text-[#0284C7] font-sans"><?= number_format($rule['support'], 2, ',', '.') ?>%</strong>
                                        </td>
                                        <td class="px-3 py-3 text-center font-mono text-[11px] whitespace-nowrap">
                                            <?= $cntAB ?>/<?= $cntA ?> = <strong class="text-slate-900 font-sans"><?= number_format($rule['confidence'], 2, ',', '.') ?>%</strong>
                                        </td>
                                        <td class="px-3 py-3 text-center font-mono text-[11px] whitespace-nowrap">
                                            <?= $confDec ?> &divide; (<?= $cntB ?>/<?= $totalTrx ?>) = <strong class="text-teal-700 font-sans font-black"><?= $rule['lift_format'] ?? number_format($rule['lift'], 3, ',', '.') ?></strong>
                                        </td>
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $rule['badge_color'] ?? 'bg-teal-50 text-teal-800 border-teal-200' ?>">
                                                <?= $rule['korelasi'] ?? 'Korelasi Kuat' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-sky-50 text-[11px] text-slate-500 border-t border-sky-100 flex items-center justify-between">
                        <span>Sumber: Hasil pengolahan data transaksi Toko Sembako Junita (2026).</span>
                        <span class="font-bold text-[#0284C7]">Menampilkan <?= count($hasil['rules'] ?? []) ?> Aturan Asosiasi</span>
                    </div>
                </div>

                <!-- SUB-TABS TEKNIS DATASET L2, L1, C1 -->
                <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-8">
                    <div class="p-4 bg-slate-800 text-white flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-1.5 bg-black/30 p-1 rounded-xl text-xs">
                            <button type="button" @click="subTabTeknis = 'l2'" 
                                    :class="subTabTeknis === 'l2' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                    class="px-3.5 py-1.5 rounded-lg transition">
                                Frequent 2-Itemset L2 (<?= count($hasil['itemsets_2'] ?? []) ?>)
                            </button>
                            <button type="button" @click="subTabTeknis = 'l1'" 
                                    :class="subTabTeknis === 'l1' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                    class="px-3.5 py-1.5 rounded-lg transition">
                                Frequent 1-Itemset L1 (<?= count($hasil['itemsets_1'] ?? []) ?>)
                            </button>
                            <?php if (!empty($c1Lengkap)): ?>
                            <button type="button" @click="subTabTeknis = 'c1'" 
                                    :class="subTabTeknis === 'c1' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                    class="px-3.5 py-1.5 rounded-lg transition">
                                Calon 1-Itemset C1 (<?= count($c1Lengkap) ?>)
                            </button>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-slate-400">Rincian Pembentukan Tingkat Itemset</span>
                    </div>

                    <!-- Sub-Tab L2 -->
                    <div x-show="subTabTeknis === 'l2'" class="overflow-x-auto max-h-[400px] overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10 text-[11px]">
                                <tr>
                                    <th class="px-4 py-3 text-center">No</th>
                                    <th class="px-4 py-3">Pasangan Produk (L2)</th>
                                    <th class="px-4 py-3 text-center">Frekuensi Bersama</th>
                                    <th class="px-4 py-3 text-right">Perhitungan Support</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                <?php foreach (($hasil['itemsets_2'] ?? []) as $idx => $it2): ?>
                                    <tr data-search="<?= strtolower(esc($it2['item_a'] . ' ' . $it2['item_b'])) ?>"
                                        x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                        class="hover:bg-sky-50/50">
                                        <td class="px-4 py-2.5 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900">
                                            <?= esc($it2['item_a']) ?> <span class="text-[#0284C7]">+</span> <?= esc($it2['item_b']) ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-semibold"><?= $it2['jumlah_transaksi'] ?> struk</td>
                                        <td class="px-4 py-2.5 text-right font-black text-[#0284C7] font-mono">
                                            <?= $it2['jumlah_transaksi'] ?>/<?= $totalTrx ?> = <?= $it2['support'] ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sub-Tab L1 -->
                    <div x-show="subTabTeknis === 'l1'" class="overflow-x-auto max-h-[400px] overflow-y-auto" style="display: none;">
                        <table class="w-full text-xs text-left">
                            <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10 text-[11px]">
                                <tr>
                                    <th class="px-4 py-3 text-center">No</th>
                                    <th class="px-4 py-3">Nama Produk (L1)</th>
                                    <th class="px-4 py-3">Kategori</th>
                                    <th class="px-4 py-3 text-center">Frekuensi</th>
                                    <th class="px-4 py-3 text-right">Support (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                <?php foreach (($hasil['itemsets_1'] ?? []) as $idx => $it1): ?>
                                    <tr data-search="<?= strtolower(esc($it1['nama'] ?? $it1['item'] ?? '')) ?>"
                                        x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                        class="hover:bg-sky-50/50">
                                        <td class="px-4 py-2.5 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900"><?= esc($it1['nama'] ?? $it1['item'] ?? '') ?></td>
                                        <td class="px-4 py-2.5 text-slate-500"><?= esc($it1['kategori'] ?? 'Sembako') ?></td>
                                        <td class="px-4 py-2.5 text-center font-semibold"><?= $it1['jumlah_transaksi'] ?> struk</td>
                                        <td class="px-4 py-2.5 text-right font-black text-[#0284C7] font-mono">
                                            <?= $it1['jumlah_transaksi'] ?>/<?= $totalTrx ?> = <?= $it1['support'] ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sub-Tab C1 -->
                    <?php if (!empty($c1Lengkap)): ?>
                    <div x-show="subTabTeknis === 'c1'" class="overflow-x-auto max-h-[400px] overflow-y-auto" style="display: none;">
                        <table class="w-full text-xs text-left">
                            <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10 text-[11px]">
                                <tr>
                                    <th class="px-4 py-3 text-center">No</th>
                                    <th class="px-4 py-3">Calon Produk (C1)</th>
                                    <th class="px-4 py-3 text-center">Frekuensi</th>
                                    <th class="px-4 py-3 text-right">Support (%)</th>
                                    <th class="px-4 py-3 text-center">Status Seleksi L1</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                <?php foreach ($c1Lengkap as $idx => $c1): ?>
                                    <?php $isLolos = ($c1['status'] ?? '') === 'Lolos L1'; ?>
                                    <tr data-search="<?= strtolower(esc($c1['nama'] ?? '')) ?>"
                                        x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                        class="hover:bg-sky-50/50">
                                        <td class="px-4 py-2.5 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-2.5 font-medium text-slate-900"><?= esc($c1['nama'] ?? '') ?></td>
                                        <td class="px-4 py-2.5 text-center"><?= $c1['jumlah_transaksi'] ?> struk</td>
                                        <td class="px-4 py-2.5 text-right font-bold"><?= $c1['support'] ?>%</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $isLolos ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                                                <?= esc($c1['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- SUB-VIEW 2: SIMULASI MANUAL 10 SAMPEL PERTAMA (BAB 4 SKRIPSI)  -->
            <!-- =============================================================== -->
            <div x-show="subPerhitunganMode === 'sampel10'" style="display: none;">
                
                <!-- Box Penjelasan Bab 4 Sampel 10 -->
                <div class="bg-gradient-to-br from-sky-50 to-blue-50/50 p-6 rounded-3xl border border-sky-200 shadow-sm mb-6">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-2xl bg-[#0284C7] text-white flex items-center justify-center shrink-0 shadow-md shadow-sky-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-slate-900">Perhitungan Manual 10 Sampel Transaksi Pertama (Bab 4 Skripsi)</h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Sesuai penjelasan pada Bab 4 Skripsi, untuk memperlihatkan proses perhitungan secara manual langkah demi langkah, digunakan <strong>10 transaksi pertama</strong> berdasarkan urutan kemunculan nomor transaksi pada dataset. Sampel ini hanya digunakan sebagai ilustrasi perhitungan, sedangkan hasil utama penelitian diperoleh dari pengolahan seluruh 788 transaksi.
                            </p>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Pada sampel 10 transaksi, batas minimum frekuensi untuk minimum support 3% dihitung sebesar <strong>3% &times; 10 = 0,3 transaksi</strong>. Karena frekuensi transaksi harus bilangan bulat, itemset harus muncul sekurang-kurangnya <strong>1 kali</strong> agar memenuhi batas tersebut (sehingga support itemset minimal 10% dan tetap memenuhi minimum support 3%).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Parameter Sampel (Tabel 4.2) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                        <span>Tabel 4.2 Parameter Algoritma Apriori</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200">
                            <span class="text-[11px] font-bold text-slate-400 uppercase block">Minimum Support</span>
                            <div class="text-lg font-black text-[#0284C7] mt-0.5">3% <span class="text-xs font-normal text-slate-600">(&ge; 1 transaksi pada sampel)</span></div>
                            <p class="text-[11px] text-slate-500 mt-1">Batas minimum kemunculan itemset dalam seluruh transaksi sampel.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200">
                            <span class="text-[11px] font-bold text-slate-400 uppercase block">Minimum Confidence</span>
                            <div class="text-lg font-black text-indigo-700 mt-0.5">50%</div>
                            <p class="text-[11px] text-slate-500 mt-1">Batas minimum tingkat kepercayaan aturan asosiasi.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200">
                            <span class="text-[11px] font-bold text-slate-400 uppercase block">Minimum Lift Ratio</span>
                            <div class="text-lg font-black text-teal-700 mt-0.5">1,2</div>
                            <p class="text-[11px] text-slate-500 mt-1">Batas minimum kekuatan asosiasi dibandingkan kondisi independen.</p>
                        </div>
                    </div>
                </div>

                <!-- TABEL 4.1: Sampel 10 Transaksi Pertama -->
                <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-6">
                    <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                        <div>
                            <h4 class="text-xs sm:text-sm font-black">Tabel 4.1 Sampel 10 Transaksi Pertama</h4>
                            <p class="text-[11px] text-slate-400">Kode transaksi T1 sampai T10 digunakan untuk memudahkan penelusuran perhitungan manual</p>
                        </div>
                        <span class="text-xs bg-sky-500/20 text-sky-300 border border-sky-500/30 px-2.5 py-1 rounded-lg font-bold">10 Struk Pertama</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 text-[11px]">
                                <tr>
                                    <th class="px-3 py-3 text-center">No</th>
                                    <th class="px-3 py-3 text-center">Kode</th>
                                    <th class="px-3 py-3 text-center">Tanggal</th>
                                    <th class="px-4 py-3">Nomor Transaksi</th>
                                    <th class="px-4 py-3">Produk yang Dibeli</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                <?php foreach (($sampel10['tabel_4_1'] ?? []) as $t1): ?>
                                    <tr class="hover:bg-sky-50/40">
                                        <td class="px-3 py-2.5 text-center font-bold text-slate-400"><?= $t1['no'] ?></td>
                                        <td class="px-3 py-2.5 text-center font-black text-[#0284C7] bg-sky-50/50"><?= $t1['kode'] ?></td>
                                        <td class="px-3 py-2.5 text-center whitespace-nowrap text-slate-600"><?= $t1['tanggal'] ?></td>
                                        <td class="px-4 py-2.5 font-mono text-[11px] text-slate-700"><?= $t1['no_transaksi'] ?></td>
                                        <td class="px-4 py-2.5 font-medium text-slate-900"><?= esc($t1['produk_list']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- DUA KOLOM: TABEL 4.3 (L1) & TABEL 4.4 (L2) PADA SAMPEL -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Kiri: TABEL 4.3 (L1 pada Sampel - 17 Produk) -->
                    <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden">
                        <div class="p-4 bg-[#0284C7] text-white">
                            <h4 class="text-xs sm:text-sm font-black">Tabel 4.3 Hasil Perhitungan Frequent 1-Itemset (L1)</h4>
                            <p class="text-[11px] text-sky-100">17 produk berbeda pada sampel, seluruhnya muncul &ge; 1 kali (Support &ge; 3%)</p>
                        </div>
                        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 text-[10px]">
                                    <tr>
                                        <th class="px-2 py-2.5 text-center">No</th>
                                        <th class="px-3 py-2.5">Nama Produk</th>
                                        <th class="px-2 py-2.5 text-center">Transaksi</th>
                                        <th class="px-2 py-2.5 text-center">Freq</th>
                                        <th class="px-3 py-2.5 text-right">Support</th>
                                        <th class="px-2 py-2.5 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-800">
                                    <?php foreach (($sampel10['tabel_4_3'] ?? []) as $it1): ?>
                                        <tr class="hover:bg-sky-50/40">
                                            <td class="px-2 py-2 text-center text-slate-400 font-bold"><?= $it1['no'] ?></td>
                                            <td class="px-3 py-2 font-bold text-slate-900"><?= esc($it1['nama']) ?></td>
                                            <td class="px-2 py-2 text-center font-mono text-[10px] text-slate-600"><?= esc($it1['transaksi']) ?></td>
                                            <td class="px-2 py-2 text-center font-bold text-slate-800"><?= $it1['frekuensi'] ?></td>
                                            <td class="px-3 py-2 text-right font-mono text-[11px] text-[#0284C7] whitespace-nowrap"><?= esc($it1['support']) ?></td>
                                            <td class="px-2 py-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200"><?= $it1['status'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Kanan: TABEL 4.4 (L2 pada Sampel - 27 Kombinasi) -->
                    <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden">
                        <div class="p-4 bg-indigo-700 text-white">
                            <h4 class="text-xs sm:text-sm font-black">Tabel 4.4 Hasil Perhitungan Frequent 2-Itemset (L2)</h4>
                            <p class="text-[11px] text-indigo-100">Dari C(17,2)=136 kandidat, terdapat 27 pasangan yang muncul &ge; 1 kali</p>
                        </div>
                        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 text-[10px]">
                                    <tr>
                                        <th class="px-2 py-2.5 text-center">No</th>
                                        <th class="px-3 py-2.5">Kombinasi Produk (L2)</th>
                                        <th class="px-2 py-2.5 text-center">Transaksi</th>
                                        <th class="px-2 py-2.5 text-center">Freq</th>
                                        <th class="px-3 py-2.5 text-right">Support</th>
                                        <th class="px-2 py-2.5 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-800">
                                    <?php foreach (($sampel10['tabel_4_4'] ?? []) as $it2): ?>
                                        <tr class="hover:bg-sky-50/40">
                                            <td class="px-2 py-2 text-center text-slate-400 font-bold"><?= $it2['no'] ?></td>
                                            <td class="px-3 py-2 font-bold text-slate-900"><?= esc($it2['kombinasi']) ?></td>
                                            <td class="px-2 py-2 text-center font-mono text-[10px] text-slate-600"><?= esc($it2['transaksi']) ?></td>
                                            <td class="px-2 py-2 text-center font-bold text-slate-800"><?= $it2['frekuensi'] ?></td>
                                            <td class="px-3 py-2 text-right font-mono text-[11px] text-indigo-700 whitespace-nowrap"><?= esc($it2['support']) ?></td>
                                            <td class="px-2 py-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200"><?= $it2['status'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TABEL 4.8: Ringkasan Pembentukan Frequent Itemset pada Sampel (C1 s.d. C6) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#0284C7]"></span>
                        <span>Tabel 4.8 Ringkasan Pembentukan Frequent Itemset pada Sampel</span>
                    </h4>
                    <div class="overflow-x-auto border border-sky-100 rounded-2xl">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-sky-50/60 text-slate-700 font-bold uppercase text-[11px]">
                                <tr>
                                    <th class="px-4 py-3">Tahap</th>
                                    <th class="px-4 py-3 text-center">Jumlah Kandidat</th>
                                    <th class="px-4 py-3 text-center">Frequent Itemset (Lolos)</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-100 text-slate-800 bg-white">
                                <?php foreach (($sampel10['tabel_4_8'] ?? []) as $r48): ?>
                                    <tr class="hover:bg-sky-50/40">
                                        <td class="px-4 py-2.5 font-bold text-slate-900"><?= esc($r48['tahap']) ?></td>
                                        <td class="px-4 py-2.5 text-center font-semibold"><?= esc($r48['kandidat']) ?></td>
                                        <td class="px-4 py-2.5 text-center font-black text-[#0284C7]"><?= esc($r48['frequent']) ?></td>
                                        <td class="px-4 py-2.5 text-slate-600"><?= esc($r48['keterangan']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABEL 4.9: Contoh Rincian & Perhitungan Aturan Asosiasi Sampel -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-sky-100 pb-3 mb-4">
                        <div>
                            <h4 class="text-xs sm:text-sm font-black text-slate-900">Tabel 4.9 Rincian Transaksi untuk Perhitungan Aturan Asosiasi pada Sampel</h4>
                            <p class="text-xs text-slate-500">Studi Kasus: <strong>Margarin Blue Band 200g &rarr; Teh Celup SariWangi 25 sachet</strong></p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold">Lolos Semua Syarat &bull; Lift 2,50</span>
                    </div>

                    <?php $t49 = $sampel10['tabel_4_9'] ?? []; ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <!-- Tabel Kiri: Rincian Frekuensi -->
                        <div class="overflow-x-auto border border-sky-100 rounded-2xl">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px]">
                                    <tr>
                                        <th class="px-3 py-2.5">Keterangan</th>
                                        <th class="px-3 py-2.5 text-center">Kode Transaksi</th>
                                        <th class="px-3 py-2.5 text-center">Frekuensi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-800 bg-white">
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-slate-900">Margarin Blue Band 200g (A)</td>
                                        <td class="px-3 py-2 text-center font-mono text-[11px] text-slate-600"><?= $t49['trx_a'] ?? 'T8, T9, T10' ?></td>
                                        <td class="px-3 py-2 text-center font-bold text-[#0284C7]"><?= $t49['freq_a'] ?? 3 ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-slate-900">Teh Celup SariWangi 25 sachet (B)</td>
                                        <td class="px-3 py-2 text-center font-mono text-[11px] text-slate-600"><?= $t49['trx_b'] ?? 'T4, T8, T9, T10' ?></td>
                                        <td class="px-3 py-2 text-center font-bold text-indigo-700"><?= $t49['freq_b'] ?? 4 ?></td>
                                    </tr>
                                    <tr class="bg-sky-50/30">
                                        <td class="px-3 py-2 font-black text-slate-900">A dan B muncul bersama</td>
                                        <td class="px-3 py-2 text-center font-mono text-[11px] font-bold text-[#0284C7]"><?= $t49['trx_ab'] ?? 'T8, T9, T10' ?></td>
                                        <td class="px-3 py-2 text-center font-black text-[#0284C7]"><?= $t49['freq_ab'] ?? 3 ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-slate-600">Jumlah seluruh transaksi sampel</td>
                                        <td class="px-3 py-2 text-center font-mono text-[11px] text-slate-500">T1&ndash;T10</td>
                                        <td class="px-3 py-2 text-center font-bold text-slate-700"><?= $t49['total_sampel'] ?? 10 ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Kanan: 3 Langkah Rumus & Perhitungan -->
                        <div class="space-y-2.5 font-mono text-[11px] text-slate-800">
                            <div class="p-3 rounded-2xl bg-sky-50/70 border border-sky-200">
                                <span class="font-sans font-bold text-[#0284C7] block mb-0.5">1) Perhitungan Support:</span>
                                <div>Support(A &rarr; B) = (3 / 10) &times; 100% = <strong>30%</strong></div>
                                <span class="font-sans text-[10px] text-teal-700 font-semibold block mt-0.5">&bull; Memenuhi minimum support sebesar 3%</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-indigo-50/70 border border-indigo-200">
                                <span class="font-sans font-bold text-indigo-700 block mb-0.5">2) Perhitungan Confidence:</span>
                                <div>Confidence(A &rarr; B) = (3 / 3) &times; 100% = <strong>100%</strong></div>
                                <span class="font-sans text-[10px] text-teal-700 font-semibold block mt-0.5">&bull; Memenuhi minimum confidence sebesar 50%</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-teal-50/70 border border-teal-200">
                                <span class="font-sans font-bold text-teal-800 block mb-0.5">3) Perhitungan Lift Ratio:</span>
                                <div>Lift(A &rarr; B) = 1,00 / (4 / 10) = <strong>2,50</strong></div>
                                <span class="font-sans text-[10px] text-teal-700 font-semibold block mt-0.5">&bull; Memenuhi minimum lift 1,2 (Korelasi Kuat)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<?= $this->endSection(); ?>
