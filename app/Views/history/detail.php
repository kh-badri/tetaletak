<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<?php
    $manual      = $hasil['perhitungan_manual'] ?? [];
    $ringkasan   = $manual['ringkasan'] ?? [];
    $kasus       = $manual['contoh_kasus'] ?? null;
    $c1Lengkap   = $manual['c1_lengkap'] ?? [];
    $rekomendasi = $hasil['rekomendasi_tataletak'] ?? [];
    $top3        = array_slice($rekomendasi, 0, 3);
?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-6 px-4 sm:px-6 text-slate-800" 
     x-data="{ 
        activeTab: 'rekomendasi', 
        subTabTeknis: 'rules',
        searchRek: '',
        filterTipe: '',
        searchTeknis: ''
     }">
    <div class="container mx-auto max-w-7xl">

        <!-- =================================================================== -->
        <!-- BARIS ATAS: KEMBALI KE RIWAYAT & TANGGAL -->
        <!-- =================================================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
            <a href="<?= base_url('history') ?>" 
               class="inline-flex items-center gap-2 bg-white text-slate-700 hover:text-[#0284C7] border border-sky-200 px-4 py-2 rounded-xl font-bold text-xs sm:text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Riwayat
            </a>

            <div class="text-xs text-slate-600 bg-white px-4 py-2 rounded-xl border border-sky-200 shadow-sm">
                Disimpan pada: <strong class="text-slate-900"><?= date('d F Y, H:i', strtotime($tanggal_analisis ?? 'now')) ?> WIB</strong>
            </div>
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
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-lg sm:text-xl font-black text-slate-900">3 Pasangan Produk Prioritas Utama</h2>
                    <p class="text-xs text-slate-500">Barang-barang paling sering dibeli bersama yang disarankan ditata berdekatan di toko.</p>
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
                    <p class="text-center text-slate-500 py-6 text-xs">Tidak ada rekomendasi tata letak tersimpan.</p>
                <?php endif; ?>
            </div>

            <!-- 2. DAFTAR LENGKAP REKOMENDASI (RINGKAS + BISA DICARI) -->
            <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-6">
                <div class="p-5 border-b border-sky-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-sky-50/30">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900">Daftar Lengkap Saran Penataan Rak</h2>
                        <p class="text-xs text-slate-500">Total <?= count($rekomendasi) ?> pasangan barang</p>
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

            <!-- 1. ALUR KERJA 5 TAHAP -->
            <div class="bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                <h2 class="text-lg font-black text-slate-900 mb-4">Alur Kerja Algoritma Apriori (5 Tahap)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 border border-sky-200">
                        <span class="font-black text-[#0284C7] block mb-1">1. Data Transaksi (N)</span>
                        <p class="text-slate-600">Membaca total <strong><?= $hasil['total_transaksi'] ?> struk</strong> keranjang belanja.</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 border border-sky-200">
                        <span class="font-black text-[#0284C7] block mb-1">2. Seleksi 1-Item (L1)</span>
                        <p class="text-slate-600">Hitung frekuensi per produk. Minimal <?= $hasil['min_support_count'] ?? 16 ?> transaksi (&ge; <?= $hasil['min_support'] ?>%) lolos ke <strong>L1 (<?= count($hasil['itemsets_1'] ?? []) ?> item)</strong>.</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 border border-sky-200">
                        <span class="font-black text-[#0284C7] block mb-1">3. Pasangan 2-Item (L2)</span>
                        <p class="text-slate-600">Gabungkan item L1 menjadi pasangan. Pasangan yang sering muncul lolos ke <strong>L2 (<?= count($hasil['itemsets_2'] ?? []) ?> pasang)</strong>.</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 border border-sky-200">
                        <span class="font-black text-[#0284C7] block mb-1">4. Aturan Asosiasi</span>
                        <p class="text-slate-600">Uji aturan <code class="bg-white px-1 rounded border border-sky-200">A &rarr; B</code>. Hanya yang memiliki Confidence &ge; <strong><?= $hasil['min_confidence'] ?>%</strong> yang lolos.</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 border border-sky-200">
                        <span class="font-black text-[#0284C7] block mb-1">5. Uji Validitas Lift</span>
                        <p class="text-slate-600">Uji kekuatan korelasi. Nilai <strong>Lift &gt; 1.0</strong> menghasilkan <strong><?= $hasil['total_rules'] ?? 0 ?> aturan valid</strong>.</p>
                    </div>
                </div>
            </div>

            <!-- 2. RUMUS MATEMATIKA + STUDI KASUS PERHITUNGAN MANUAL -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <div class="bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 space-y-4">
                    <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">Rumus Matematika Apriori</h3>
                    
                    <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100 text-xs">
                        <strong class="text-[#0284C7] block mb-1">1. Support (Dukungan)</strong>
                        <div class="font-mono text-[11px] bg-white p-2 rounded border border-sky-100 text-slate-800">
                            Supp(A) = (&sum;Trx A / N) &times; 100%<br>
                            Supp(A&cup;B) = (&sum;Trx A&amp;B / N) &times; 100%
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100 text-xs">
                        <strong class="text-[#0284C7] block mb-1">2. Confidence (Kepastian)</strong>
                        <div class="font-mono text-[11px] bg-white p-2 rounded border border-sky-100 text-slate-800">
                            Conf(A&rarr;B) = (&sum;Trx A&amp;B / &sum;Trx A) &times; 100%
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100 text-xs">
                        <strong class="text-[#0284C7] block mb-1">3. Lift Ratio (Korelasi)</strong>
                        <div class="font-mono text-[11px] bg-white p-2 rounded border border-sky-100 text-slate-800">
                            Lift = Supp(A&cup;B) / (Supp(A) &times; Supp(B))
                        </div>
                        <span class="text-[10px] text-teal-700 font-bold block mt-1">Catatan: Lift &gt; 1 = Hubungan Positif (Valid)</span>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100">
                    <?php if (!empty($kasus)): ?>
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-4">
                            <div>
                                <h3 class="text-base font-black text-slate-900">Contoh Perhitungan Manual (Data Nyata #1)</h3>
                                <p class="text-xs text-slate-500">Aturan: <strong class="text-[#0284C7]"><?= esc($kasus['item_a']) ?></strong> &rarr; <strong class="text-indigo-600"><?= esc($kasus['item_b']) ?></strong></p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-700 border border-teal-200 text-xs font-bold">Valid &bull; Lift <?= $kasus['lift'] ?></span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4 text-xs bg-sky-50 p-3 rounded-xl border border-sky-200 text-slate-700">
                            <div>Populasi (N): <strong class="text-slate-900"><?= $kasus['n_total'] ?></strong></div>
                            <div>Frekuensi A: <strong class="text-slate-900"><?= $kasus['count_a'] ?></strong></div>
                            <div>Frekuensi B: <strong class="text-slate-900"><?= $kasus['count_b'] ?></strong></div>
                            <div>Bersama (A&cap;B): <strong class="text-[#0284C7]"><?= $kasus['count_ab'] ?></strong></div>
                        </div>

                        <div class="space-y-2.5 text-xs font-mono text-slate-800">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <span><strong>Langkah 1 (Support A &amp; B):</strong> (<?= $kasus['count_a'] ?>/<?= $kasus['n_total'] ?>)=<strong><?= $kasus['supp_a'] ?>%</strong> | (<?= $kasus['count_b'] ?>/<?= $kasus['n_total'] ?>)=<strong><?= $kasus['supp_b'] ?>%</strong></span>
                                <span class="text-teal-700 font-sans font-bold">Lolos L1</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <span><strong>Langkah 2 (Support Pasangan):</strong> (<?= $kasus['count_ab'] ?> / <?= $kasus['n_total'] ?>) &times; 100% = <strong><?= $kasus['supp_ab'] ?>%</strong></span>
                                <span class="text-teal-700 font-sans font-bold">Lolos L2 (&ge;<?= $kasus['min_support_pct'] ?>%)</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <span><strong>Langkah 3 (Confidence A&rarr;B):</strong> (<?= $kasus['count_ab'] ?> / <?= $kasus['count_a'] ?>) &times; 100% = <strong><?= $kasus['confidence'] ?>%</strong></span>
                                <span class="text-teal-700 font-sans font-bold">Lolos (&ge;<?= $kasus['min_conf_pct'] ?>%)</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <span><strong>Langkah 4 (Lift Ratio):</strong> <?= ($kasus['supp_ab']/100) ?> / (<?= ($kasus['supp_a']/100) ?> &times; <?= ($kasus['supp_b']/100) ?>) = <strong><?= $kasus['lift'] ?></strong></span>
                                <span class="text-teal-700 font-sans font-bold">Korelasi Kuat (&gt;1.0)</span>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-teal-900 bg-teal-50 p-3 rounded-xl border border-teal-200 leading-relaxed">
                            <strong>Kesimpulan:</strong> Karena Support (<?= $kasus['supp_ab'] ?>%), Confidence (<?= $kasus['confidence'] ?>%), dan Lift (<?= $kasus['lift'] ?>) semuanya melampaui ambang batas, aturan ini valid secara matematis.
                        </p>
                    <?php else: ?>
                        <p class="text-center text-slate-500 py-8 text-xs">Data studi kasus belum tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. TABEL DATA RINCIAN TEKNIS -->
            <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-8">
                <div class="p-4 bg-[#0369A1] text-white flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3">
                    <div class="flex flex-wrap items-center gap-1.5 bg-black/15 p-1 rounded-xl text-xs">
                        <button type="button" @click="subTabTeknis = 'rules'" 
                                :class="subTabTeknis === 'rules' ? 'bg-white text-[#0369A1] font-bold shadow' : 'text-white/85 hover:text-white'"
                                class="px-3 py-1.5 rounded-lg transition">
                            Aturan Asosiasi (<?= count($hasil['rules'] ?? []) ?>)
                        </button>
                        <button type="button" @click="subTabTeknis = 'l2'" 
                                :class="subTabTeknis === 'l2' ? 'bg-white text-[#0369A1] font-bold shadow' : 'text-white/85 hover:text-white'"
                                class="px-3 py-1.5 rounded-lg transition">
                            Frequent L2 (<?= count($hasil['itemsets_2'] ?? []) ?>)
                        </button>
                        <button type="button" @click="subTabTeknis = 'l1'" 
                                :class="subTabTeknis === 'l1' ? 'bg-white text-[#0369A1] font-bold shadow' : 'text-white/85 hover:text-white'"
                                class="px-3 py-1.5 rounded-lg transition">
                            Frequent L1 (<?= count($hasil['itemsets_1'] ?? []) ?>)
                        </button>
                        <?php if (!empty($c1Lengkap)): ?>
                        <button type="button" @click="subTabTeknis = 'c1'" 
                                :class="subTabTeknis === 'c1' ? 'bg-white text-[#0369A1] font-bold shadow' : 'text-white/85 hover:text-white'"
                                class="px-3 py-1.5 rounded-lg transition">
                            Seleksi C1 (<?= count($c1Lengkap) ?>)
                        </button>
                        <?php endif; ?>
                    </div>

                    <input type="text" x-model="searchTeknis" placeholder="Cari produk dalam tabel teknis..." 
                           class="px-3 py-1.5 rounded-xl bg-white text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-sky-300 w-full lg:w-64">
                </div>

                <div x-show="subTabTeknis === 'rules'" class="overflow-x-auto max-h-[450px] overflow-y-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3">Aturan (Antecedent &rarr; Consequent)</th>
                                <th class="px-4 py-3 text-center">Support</th>
                                <th class="px-4 py-3 text-center">Confidence</th>
                                <th class="px-4 py-3 text-center">Lift Ratio</th>
                                <th class="px-4 py-3 text-center">Korelasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            <?php foreach (($hasil['rules'] ?? []) as $idx => $rule) : ?>
                                <tr data-search="<?= strtolower(esc($rule['antecedent'] . ' ' . $rule['consequent'])) ?>"
                                    x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                    class="hover:bg-sky-50/50">
                                    <td class="px-4 py-2.5 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                    <td class="px-4 py-2.5">
                                        JIKA <strong class="text-[#0284C7]"><?= esc($rule['antecedent']) ?></strong> &rarr; MAKA <strong class="text-indigo-700"><?= esc($rule['consequent']) ?></strong>
                                    </td>
                                    <td class="px-4 py-2.5 text-center"><?= $rule['support'] ?>% (<?= $rule['jumlah_transaksi'] ?? 0 ?>)</td>
                                    <td class="px-4 py-2.5 text-center font-bold"><?= $rule['confidence'] ?>%</td>
                                    <td class="px-4 py-2.5 text-center font-black text-teal-700"><?= $rule['lift'] ?></td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border <?= $rule['badge_color'] ?? 'bg-emerald-100 text-emerald-800' ?>">
                                            <?= $rule['korelasi'] ?? 'Korelasi Positif' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div x-show="subTabTeknis === 'l2'" class="overflow-x-auto max-h-[450px] overflow-y-auto" style="display: none;">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3">Pasangan Produk (L2)</th>
                                <th class="px-4 py-3 text-center">Transaksi Bersama</th>
                                <th class="px-4 py-3 text-right">Support (%)</th>
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
                                    <td class="px-4 py-2.5 text-right font-black text-[#0284C7]"><?= $it2['support'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div x-show="subTabTeknis === 'l1'" class="overflow-x-auto max-h-[450px] overflow-y-auto" style="display: none;">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10">
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
                                    <td class="px-4 py-2.5 text-right font-black text-[#0284C7]"><?= $it1['support'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($c1Lengkap)): ?>
                <div x-show="subTabTeknis === 'c1'" class="overflow-x-auto max-h-[450px] overflow-y-auto" style="display: none;">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-slate-50 text-slate-600 uppercase font-bold border-b border-sky-100 z-10">
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

    </div>
</div>

<?= $this->endSection(); ?>
