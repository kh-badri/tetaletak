<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<?php
    $rekomendasi = $hasil['rekomendasi_tataletak'] ?? [];
    $top3        = array_slice($rekomendasi, 0, 3);
    $totalTrx    = $hasil['total_transaksi'] ?? 200;
    $kesimpulan  = $hasil['kesimpulan_excel'] ?? [];
    $kombinasi1  = $hasil['kombinasi_1'] ?? [];
    $kombinasi2  = $hasil['kombinasi_2'] ?? [];
    $kombinasi3  = $hasil['kombinasi_3'] ?? [];
    $isFallback  = $hasil['is_fallback_ranking'] ?? false;
?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-6 px-4 sm:px-6 text-slate-800" 
     x-data="{ 
        activeTab: 'rekomendasi', 
        subTabKomb: 'k2',
        searchRek: '',
        filterTipe: '',
        searchTeknis: ''
     }">
    <div class="container mx-auto max-w-7xl">

        <!-- BARIS ATAS: KEMBALI KE RIWAYAT & TANGGAL -->
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

        <!-- RINGKASAN SINGKAT (4 KARTU) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 mb-6">
            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Total Transaksi</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">
                    <?= number_format($totalTrx, 0, ',', '.') ?> <span class="text-xs font-normal text-slate-500">struk</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Rekomendasi Rak</span>
                <div class="text-xl sm:text-2xl font-black text-[#0284C7] mt-0.5">
                    <?= count($rekomendasi) ?> <span class="text-xs font-normal text-slate-500">pasang</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Lolos Threshold (Ya/Ya)</span>
                <div class="text-xl sm:text-2xl font-black text-teal-600 mt-0.5">
                    <?= number_format($hasil['total_lolos'] ?? 0, 0, ',', '.') ?> <span class="text-xs font-normal text-slate-500">aturan</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm shadow-sky-900/5 border border-sky-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase block">Parameter Uji</span>
                <div class="text-xs sm:text-sm font-bold text-slate-800 mt-1">
                    Supp: <?= $hasil['threshold_support'] ?? 0.2 ?>
                </div>
                <span class="text-[11px] text-[#0284C7] font-bold">Supp×Conf: <?= $hasil['threshold_support_confidence'] ?? 0.5 ?></span>
            </div>
        </div>

        <!-- NAVIGASI 2 TAB UTAMA -->
        <div class="flex flex-col sm:flex-row gap-2 p-1.5 bg-white rounded-2xl border border-sky-100 shadow-sm mb-6">
            <button type="button" @click="activeTab = 'rekomendasi'" 
                    :class="activeTab === 'rekomendasi' ? 'bg-[#0284C7] text-white shadow-sm' : 'text-slate-600 hover:bg-sky-50'"
                    class="flex-1 py-3 px-5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Rekomendasi Penataan Rak</span>
            </button>

            <button type="button" @click="activeTab = 'perhitungan'" 
                    :class="activeTab === 'perhitungan' ? 'bg-[#0284C7] text-white shadow-sm' : 'text-slate-600 hover:bg-sky-50'"
                    class="flex-1 py-3 px-5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>Perhitungan &amp; Ranking (Metode Excel)</span>
            </button>
        </div>

        <!-- TAB 1: REKOMENDASI PENATAAN RAK -->
        <div x-show="activeTab === 'rekomendasi'" x-transition.opacity>

            <?php if (!empty($kesimpulan)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <?php if (!empty($kesimpulan['support_2_produk'])): ?>
                        <div class="bg-white rounded-3xl p-5 border border-sky-100 shadow-sm">
                            <span class="inline-block px-2.5 py-1 rounded-full bg-sky-50 text-[#0284C7] border border-sky-200 text-[11px] font-bold mb-2">
                                Berdasarkan Nilai Support Terbesar
                            </span>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                <?= esc($kesimpulan['support_2_produk']['teks']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($kesimpulan['sxc_2_produk'])): ?>
                        <div class="bg-white rounded-3xl p-5 border border-sky-100 shadow-sm">
                            <span class="inline-block px-2.5 py-1 rounded-full bg-teal-50 text-teal-700 border border-teal-200 text-[11px] font-bold mb-2">
                                Berdasarkan Nilai (Support &times; Confidence) Terbesar
                            </span>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                <?= esc($kesimpulan['sxc_2_produk']['teks']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 mb-6">
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-lg sm:text-xl font-black text-slate-900">3 Pasangan Produk Prioritas Utama</h2>
                    <p class="text-xs text-slate-500">Diurutkan berdasarkan nilai Support &times; Confidence tertinggi.</p>
                </div>

                <?php if (!empty($top3)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <?php foreach ($top3 as $idx => $rek): ?>
                            <div class="bg-sky-50/30 rounded-2xl p-4 border-2 border-sky-200 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-black text-[#0284C7]">PRIORITAS #<?= $idx + 1 ?></span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200 text-[10px] font-bold">
                                            S×C: <?= number_format((float)($rek['support_x_confidence'] ?? 0), 4, ',', '.') ?>
                                        </span>
                                    </div>

                                    <div class="bg-sky-100/60 p-3 rounded-xl border border-sky-200 mb-3">
                                        <div class="grid grid-cols-2 gap-2 text-center items-stretch">
                                            <div class="bg-white p-2.5 rounded-lg border-2 border-[#0284C7] flex flex-col justify-center">
                                                <span class="text-[9px] font-bold text-[#0284C7] uppercase">Produk IF</span>
                                                <div class="text-xs font-bold text-slate-900 mt-0.5 leading-tight"><?= esc($rek['produk_utama']) ?></div>
                                            </div>
                                            <div class="bg-white p-2.5 rounded-lg border-2 border-indigo-400 flex flex-col justify-center relative">
                                                <span class="text-[9px] font-bold text-indigo-600 uppercase">Produk THEN</span>
                                                <div class="text-xs font-bold text-slate-900 mt-0.5 leading-tight"><?= esc($rek['produk_asosiasi']) ?></div>
                                                <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-[#0284C7] text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow">&rarr;</div>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-700 h-1.5 rounded-full mt-2.5"></div>
                                        <div class="text-center text-[10px] font-bold text-[#0369A1] mt-1.5">
                                            <?= esc($rek['posisi_rak']) ?>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px] text-slate-600 mb-2 px-1">
                                        <span>Support: <strong class="text-slate-900"><?= number_format((float)($rek['support'] ?? 0), 4, ',', '.') ?></strong></span>
                                        <span>Confidence: <strong class="text-slate-900"><?= number_format((float)($rek['confidence'] ?? 0), 4, ',', '.') ?></strong></span>
                                    </div>

                                    <p class="text-[11px] text-slate-600 bg-white p-2.5 rounded-xl border border-sky-100 leading-relaxed">
                                        <?= esc($rek['saran_tata_letak']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-slate-500 py-8 text-xs">Tidak ada rekomendasi tata letak tersimpan.</p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-6">
                <div class="p-5 border-b border-sky-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-sky-50/30">
                    <div>
                        <h3 class="font-black text-slate-900 text-base sm:text-lg">Daftar Rekomendasi Penataan Rak</h3>
                        <p class="text-xs text-slate-500">Menampilkan <?= count($rekomendasi) ?> pasangan rekomendasi</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" x-model="searchRek" placeholder="Cari nama produk..." 
                               class="px-3.5 py-2 rounded-xl bg-white border border-sky-200 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <select x-model="filterTipe" class="px-3 py-2 rounded-xl bg-white border border-sky-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">Semua Penataan</option>
                            <option value="Satu Rak Bersebelahan">Satu Rak Bersebelahan</option>
                            <option value="Display Silang / Ujung Lorong">Display Silang / Ujung Lorong</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase font-bold border-b border-sky-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Pasangan Produk (IF &rarr; THEN)</th>
                                <th class="px-4 py-3">Rekomendasi Posisi</th>
                                <th class="px-4 py-3 text-center">Support</th>
                                <th class="px-4 py-3 text-center">Confidence</th>
                                <th class="px-4 py-3 text-center">Supp &times; Conf</th>
                                <th class="px-4 py-3">Saran Penataan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            <?php foreach ($rekomendasi as $idx => $r): ?>
                                <?php $isSame = $r['is_same_category'] ?? false; ?>
                                <tr data-search="<?= strtolower(esc($r['produk_utama'] . ' ' . $r['produk_asosiasi'])) ?>"
                                    data-tipe="<?= esc($r['jenis_penataan']) ?>"
                                    x-show="(searchRek === '' || $el.dataset.search.includes(searchRek.toLowerCase())) && (filterTipe === '' || $el.dataset.tipe === filterTipe)"
                                    class="hover:bg-sky-50/50 transition">
                                    <td class="px-4 py-3 text-center font-bold text-slate-400"><?= $idx + 1 ?></td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900"><?= esc($r['produk_utama']) ?></div>
                                        <div class="text-xs text-[#0284C7] font-semibold">&rarr; <?= esc($r['produk_asosiasi']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold <?= $isSame ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' ?>">
                                            <?= esc($r['jenis_penataan']) ?>
                                        </span>
                                        <div class="text-[11px] text-slate-500 mt-0.5"><?= esc($r['posisi_rak']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-xs"><?= number_format((float)($r['support'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-center font-mono text-xs"><?= number_format((float)($r['confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-center font-mono text-xs font-black text-[#0284C7]"><?= number_format((float)($r['support_x_confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-xs text-slate-600 leading-relaxed max-w-sm"><?= esc($r['saran_tata_letak']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: PERHITUNGAN & RANKING (METODE EXCEL) -->
        <div x-show="activeTab === 'perhitungan'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden mb-8">
                <div class="p-5 bg-slate-800 text-white flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-1.5 bg-black/30 p-1 rounded-xl text-xs">
                        <button type="button" @click="subTabKomb = 'k2'" 
                                :class="subTabKomb === 'k2' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                class="px-3.5 py-2 rounded-lg transition">
                            Kombinasi 2 Produk (<?= count($kombinasi2) ?>)
                        </button>
                        <button type="button" @click="subTabKomb = 'k1'" 
                                :class="subTabKomb === 'k1' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                class="px-3.5 py-2 rounded-lg transition">
                            Kombinasi 1 Produk (<?= count($kombinasi1) ?>)
                        </button>
                        <button type="button" @click="subTabKomb = 'k3'" 
                                :class="subTabKomb === 'k3' ? 'bg-[#0284C7] text-white font-bold shadow' : 'text-slate-300 hover:text-white'"
                                class="px-3.5 py-2 rounded-lg transition">
                            Kombinasi 3 Produk (<?= count($kombinasi3) ?>)
                        </button>
                    </div>
                    <input type="text" x-model="searchTeknis" placeholder="Cari produk dalam tabel..." 
                           class="px-3.5 py-2 rounded-xl bg-slate-700 border border-slate-600 text-xs text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-400">
                </div>

                <!-- KOMBINASI 2 PRODUK -->
                <div x-show="subTabKomb === 'k2'" class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-sky-50 text-slate-700 text-[11px] uppercase font-bold border-b border-sky-100 z-10">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">Rank</th>
                                <th class="px-4 py-3">Aturan (If Buy ... Then Buy ...)</th>
                                <th class="px-3 py-3 text-center">Support</th>
                                <th class="px-3 py-3 text-center">Confidence</th>
                                <th class="px-3 py-3 text-center">Supp &times; Conf</th>
                                <th class="px-3 py-3 text-center">T. Support</th>
                                <th class="px-3 py-3 text-center">T. Supp×Conf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($kombinasi2 as $i => $row): ?>
                                <tr data-search="<?= strtolower(esc($row['aturan'] ?? '')) ?>"
                                    x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                    class="hover:bg-sky-50/40">
                                    <td class="px-3 py-2.5 text-center font-bold text-slate-400"><?= $i + 1 ?></td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-800">
                                        If Buy <span class="text-[#0284C7]"><?= esc($row['antecedent']) ?></span> Then Buy <span class="text-indigo-700"><?= esc($row['consequent']) ?></span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-mono"><?= number_format((float)($row['support'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono"><?= number_format((float)($row['confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono font-black text-[#0284C7]"><?= number_format((float)($row['support_x_confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_support'] ?? 'Ya') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_sxc'] ?? 'Ya') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- KOMBINASI 1 PRODUK -->
                <div x-show="subTabKomb === 'k1'" class="overflow-x-auto max-h-[600px] overflow-y-auto" style="display: none;">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-sky-50 text-slate-700 text-[11px] uppercase font-bold border-b border-sky-100 z-10">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">Rank</th>
                                <th class="px-4 py-3">Aturan (Kombinasi 1 Produk)</th>
                                <th class="px-3 py-3 text-center">Support</th>
                                <th class="px-3 py-3 text-center">Confidence</th>
                                <th class="px-3 py-3 text-center">Supp &times; Conf</th>
                                <th class="px-3 py-3 text-center">T. Support</th>
                                <th class="px-3 py-3 text-center">T. Supp×Conf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($kombinasi1 as $i => $row): ?>
                                <tr data-search="<?= strtolower(esc($row['nama_produk'] ?? '')) ?>"
                                    x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                    class="hover:bg-sky-50/40">
                                    <td class="px-3 py-2.5 text-center font-bold text-slate-400"><?= $i + 1 ?></td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-800"><?= esc($row['aturan'] ?? '') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono"><?= number_format((float)($row['support'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono">1,0000</td>
                                    <td class="px-3 py-2.5 text-center font-mono font-black text-[#0284C7]"><?= number_format((float)($row['support_x_confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_support'] ?? 'Tidak') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_sxc'] ?? 'Tidak') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- KOMBINASI 3 PRODUK -->
                <div x-show="subTabKomb === 'k3'" class="overflow-x-auto max-h-[600px] overflow-y-auto" style="display: none;">
                    <table class="w-full text-xs text-left">
                        <thead class="sticky top-0 bg-sky-50 text-slate-700 text-[11px] uppercase font-bold border-b border-sky-100 z-10">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">Rank</th>
                                <th class="px-4 py-3">Aturan (If Buy A And B Then Buy C)</th>
                                <th class="px-3 py-3 text-center">Support</th>
                                <th class="px-3 py-3 text-center">Confidence</th>
                                <th class="px-3 py-3 text-center">Supp &times; Conf</th>
                                <th class="px-3 py-3 text-center">T. Support</th>
                                <th class="px-3 py-3 text-center">T. Supp×Conf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($kombinasi3 as $i => $row): ?>
                                <tr data-search="<?= strtolower(esc($row['aturan'] ?? '')) ?>"
                                    x-show="searchTeknis === '' || $el.dataset.search.includes(searchTeknis.toLowerCase())"
                                    class="hover:bg-sky-50/40">
                                    <td class="px-3 py-2.5 text-center font-bold text-slate-400"><?= $i + 1 ?></td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-800">
                                        If Buy <span class="text-[#0284C7]"><?= esc($row['antecedent']) ?></span> Then Buy <span class="text-indigo-700"><?= esc($row['consequent']) ?></span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-mono"><?= number_format((float)($row['support'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono"><?= number_format((float)($row['confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center font-mono font-black text-[#0284C7]"><?= number_format((float)($row['support_x_confidence'] ?? 0), 4, ',', '.') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_support'] ?? 'Tidak') ?></td>
                                    <td class="px-3 py-2.5 text-center"><?= esc($row['memenuhi_sxc'] ?? 'Tidak') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
