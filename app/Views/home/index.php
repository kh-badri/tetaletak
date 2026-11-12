<?php helper(['url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<?php
    $username    = session()->get('username') ?: 'Pemilik Toko';
    $maxProdJml  = !empty($topProduk) ? max(1, (int)$topProduk[0]['jumlah']) : 1;
    $maxKatJml   = !empty($topKategori) ? max(1, (int)$topKategori[0]['jumlah']) : 1;
?>

<!-- CSS Kustom Animasi Dashboard Modern -->
<style>
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .animate-float {
        animation: floatSlow 4.5s ease-in-out infinite;
    }
    @keyframes dashMove {
        to { stroke-dashoffset: -12; }
    }
    .animate-dash {
        animation: dashMove 1.2s linear infinite;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeInUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .delay-100 { animation-delay: 0.08s; }
    .delay-200 { animation-delay: 0.16s; }
    .delay-300 { animation-delay: 0.24s; }
    @keyframes growBar {
        from { width: 0; }
    }
    .animate-bar {
        animation: growBar 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>

<div class="w-full min-h-screen bg-[#F0F7FF] py-6 px-4 sm:px-6 lg:px-8 text-slate-800" 
     x-data="{ chartTab: 'produk' }">
    <div class="container mx-auto max-w-7xl space-y-6">

        <!-- =================================================================== -->
        <!-- 1. HERO BANNER DASHBOARD MODERN + SVG RAK TATA LETAK ANIMASI -->
        <!-- =================================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:px-10 shadow-md shadow-sky-900/5 border border-sky-100 relative overflow-hidden animate-fade-up">
            <!-- Dekorasi Latar Belakang Halus -->
            <div class="hidden lg:block absolute -right-12 -top-12 w-72 h-72 rounded-full bg-sky-100/70 pointer-events-none"></div>
            <div class="hidden lg:block absolute right-36 -bottom-16 w-44 h-44 rounded-full bg-indigo-100/50 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col-reverse lg:flex-row lg:items-center justify-between gap-8">
                <!-- Kolom Kiri: Sapaan, Deskripsi & Tombol Aksi -->
                <div class="flex-1 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-[11px] font-bold text-sky-700 mb-3">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span>
                        <span>Sistem Tata Letak Toko Sembako &bull; Algoritma Apriori</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight">
                        Selamat Datang, <span class="text-[#0284C7]"><?= esc(ucfirst($username)) ?></span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2.5 leading-relaxed">
                        Implementasi Algoritma Apriori untuk Rekomendasi Tata Letak Produk Berdasarkan Pola Pembelian Konsumen. Pantau data belanja, produk terlaris, dan saran posisi rak secara langsung di sini.
                    </p>

                    <!-- Aksi Cepat Utama -->
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="<?= base_url('/rekomendasi') ?>" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white font-bold px-5 py-3 rounded-2xl shadow-md shadow-sky-500/20 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                            </svg>
                            <span>Cari Rekomendasi Rak</span>
                        </a>

                        <a href="<?= base_url('/dataset') ?>" 
                           class="inline-flex items-center gap-2 bg-sky-50 hover:bg-sky-100 text-[#0369A1] border border-sky-200 font-bold px-5 py-3 rounded-2xl transition-all duration-300 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                            <svg class="w-4 h-4 text-[#0284C7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Kelola Data &amp; CSV</span>
                        </a>
                    </div>
                </div>

                <!-- Kolom Kanan: Ilustrasi SVG Rak Display Tata Letak dengan Animasi (Tema Biru Muda Modern) -->
                <div class="flex justify-center shrink-0">
                    <div class="w-40 h-40 sm:w-48 sm:h-48 lg:w-52 lg:h-52 flex items-center justify-center animate-float">
                        <svg class="w-full h-full drop-shadow-xl" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="56" fill="#FFFFFF" stroke="#BAE6FD" stroke-width="2"/>
                            <circle cx="60" cy="60" r="48" fill="#E0F2FE" fill-opacity="0.65"/>
                            
                            <!-- Tiang Rak Toko -->
                            <rect x="25" y="27" width="4.5" height="66" rx="2.25" fill="#334155"/>
                            <rect x="90.5" y="27" width="4.5" height="66" rx="2.25" fill="#334155"/>
                            <rect x="22" y="24" width="76" height="6" rx="3" fill="#0284C7"/>
                            
                            <!-- Tingkat 1 (Atas) -->
                            <rect x="27" y="45" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="34" y="33" width="9" height="12" rx="2" fill="#0EA5E9"/>
                            <rect x="46" y="35" width="8" height="10" rx="1.5" fill="#7DD3FC"/>
                            <rect x="57" y="32" width="10" height="13" rx="2" fill="#6366F1"/>
                            <rect x="70" y="34" width="8" height="11" rx="1.5" fill="#BAE6FD"/>
                            
                            <!-- Tingkat 2 (Tengah) - Produk Asosiasi Berdampingan -->
                            <rect x="27" y="67" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="33" y="54" width="11" height="13" rx="2" fill="#BAE6FD"/>
                            <rect x="47" y="53" width="10" height="14" rx="2" fill="#0284C7"/>
                            <!-- Highlight Rekomendasi Berdampingan -->
                            <rect x="60" y="51" width="12" height="16" rx="2" fill="#38BDF8" stroke="#0369A1" stroke-width="1.5"/>
                            <rect x="75" y="54" width="10" height="13" rx="1.5" fill="#93C5FD"/>
                            
                            <!-- Tingkat 3 (Bawah) -->
                            <rect x="27" y="89" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="33" y="76" width="13" height="13" rx="2" fill="#0284C7"/>
                            <rect x="49" y="78" width="10" height="11" rx="2" fill="#BAE6FD"/>
                            <rect x="62" y="76" width="9" height="13" rx="2" fill="#6366F1"/>
                            
                            <!-- Garis Asosiasi Apriori Animasi -->
                            <path d="M 38 39 C 20 54, 28 64, 58 60" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3" class="animate-dash"/>
                            <polygon points="61,60 55,57 56,63" fill="#0284C7"/>

                            <!-- Badge Keranjang Belanja Konsumen -->
                            <g transform="translate(68, 68)">
                                <circle cx="22" cy="22" r="18" fill="#0284C7" stroke="#ffffff" stroke-width="2.5"/>
                                <path d="M14 15h3l2.2 8h8.8l2-6H19" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="20.5" cy="25.5" r="1.5" fill="#ffffff"/>
                                <circle cx="26.5" cy="25.5" r="1.5" fill="#ffffff"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 2. BARIS KPI METRIK REAL-TIME (4 KARTU BENTO DENGAN ANIMASI HOVER) -->
        <!-- =================================================================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up delay-100">
            <!-- KPI 1: Master Produk -->
            <a href="<?= base_url('/dataset?tab=produk') ?>" class="bg-white p-5 rounded-3xl shadow-sm hover:shadow-md shadow-sky-900/5 border border-sky-100 transition-all duration-300 hover:-translate-y-1 group flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Master Produk</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 group-hover:text-[#0284C7] transition">
                        <?= number_format($totalProduk, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Barang sembako aktif</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-[#0284C7] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </a>

            <!-- KPI 2: Struk Belanja -->
            <a href="<?= base_url('/dataset?tab=transaksi') ?>" class="bg-white p-5 rounded-3xl shadow-sm hover:shadow-md shadow-sky-900/5 border border-sky-100 transition-all duration-300 hover:-translate-y-1 group flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Struk Transaksi</span>
                    <div class="text-2xl sm:text-3xl font-black text-[#0284C7] mt-1">
                        <?= number_format($totalStruk, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Keranjang belanja</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </a>

            <!-- KPI 3: Item Tertransaksi -->
            <a href="<?= base_url('/dataset?tab=transaksi') ?>" class="bg-white p-5 rounded-3xl shadow-sm hover:shadow-md shadow-sky-900/5 border border-sky-100 transition-all duration-300 hover:-translate-y-1 group flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Item Dibeli</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 group-hover:text-indigo-600 transition">
                        <?= number_format($totalItemTrx, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Periode Sept–Nov 2027</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </a>

            <!-- KPI 4: Riwayat Tersimpan -->
            <a href="<?= base_url('/history') ?>" class="bg-white p-5 rounded-3xl shadow-sm hover:shadow-md shadow-sky-900/5 border border-sky-100 transition-all duration-300 hover:-translate-y-1 group flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Riwayat Analisis</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 group-hover:text-teal-600 transition">
                        <?= number_format($totalRiwayat, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Laporan tersimpan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- =================================================================== -->
        <!-- 3. BENTO GRID UTAMA: PRATINJAU RAK (7 KOLOM) & ANALITIK LAKU (5 KOLOM) -->
        <!-- =================================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch animate-fade-up delay-200">

            <!-- KOLOM KIRI (7 KOLOM): SPOTLIGHT REKOMENDASI TATA LETAK RAK -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-black text-slate-900">Pratinjau Penataan Rak Terkuat</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $sumberRekomendasi === 'history' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-teal-50 text-teal-700 border border-teal-200' ?>">
                                    <?= $sumberRekomendasi === 'history' ? 'Dari Riwayat Terbaru' : 'Analisis Langsung' ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Pasangan produk dengan kecenderungan dibeli bersama paling tinggi</p>
                        </div>

                        <a href="<?= base_url('/rekomendasi') ?>" class="text-xs font-bold text-[#0284C7] hover:text-[#0369A1] inline-flex items-center gap-1 shrink-0">
                            <span>Buka Rekomendasi Lengkap</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    <?php if (!empty($topRekomendasi)): ?>
                        <div class="space-y-3.5">
                            <?php foreach ($topRekomendasi as $idx => $rek): ?>
                                <?php $isSame = $rek['is_same_category'] ?? (($rek['kategori_utama'] ?? '') === ($rek['kategori_asosiasi'] ?? '')); ?>
                                <div class="p-4 rounded-2xl bg-sky-50/40 border border-sky-100 hover:border-sky-300 hover:bg-sky-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="w-7 h-7 rounded-xl bg-[#0284C7] text-white flex items-center justify-center text-xs font-black shrink-0 mt-0.5 shadow-sm">
                                            #<?= $idx + 1 ?>
                                        </span>
                                        <div>
                                            <div class="text-xs sm:text-sm font-bold text-slate-900">
                                                <?= esc($rek['produk_utama']) ?> 
                                                <span class="text-[#0284C7] mx-1">&harr;</span> 
                                                <span class="text-[#0284C7]"><?= esc($rek['produk_asosiasi']) ?></span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold <?= $isSame ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' ?>">
                                                    <?= $isSame ? 'Satu Rak Bersebelahan' : 'Ujung Lorong / Display Silang' ?>
                                                </span>
                                                <span class="text-[11px] text-slate-500"><?= esc($rek['posisi_rak'] ?? 'Rak Tengah') ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center shrink-0 pl-10 sm:pl-0">
                                        <span class="text-xs font-black text-[#0284C7]"><?= round($rek['confidence']) ?>% Pembeli Bareng</span>
                                        <span class="text-[10px] font-bold text-teal-700 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-full mt-0.5">
                                            <?= $rek['lift'] ?>x lebih sering
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-sky-200">
                            <p class="text-xs text-slate-500 mb-3">Belum ada data transaksi yang cukup untuk menampilkan pratinjau penataan rak.</p>
                            <a href="<?= base_url('/dataset') ?>" class="inline-block bg-[#0284C7] text-white text-xs font-bold px-4 py-2 rounded-xl">Kelola Dataset Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Tips Singkat -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500">
                    <span>Ingin mengubah batas Support atau melihat rumus perhitungan manual?</span>
                    <a href="<?= base_url('/rekomendasi') ?>" class="font-bold text-[#0284C7] hover:text-[#0369A1] underline">
                        Atur Parameter Analisis
                    </a>
                </div>
            </div>

            <!-- KOLOM KANAN (5 KOLOM): TOP 5 PRODUK & KATEGORI POPULER -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-5">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Statistik Penjualan</h2>
                            <p class="text-xs text-slate-500">Item &amp; kategori paling laris di toko</p>
                        </div>

                        <!-- Switch Toggle -->
                        <div class="flex bg-sky-50 p-1 rounded-xl text-[11px] font-bold shrink-0 border border-sky-100">
                            <button type="button" @click="chartTab = 'produk'"
                                    :class="chartTab === 'produk' ? 'bg-white text-[#0284C7] shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-2.5 py-1 rounded-lg transition">
                                Produk
                            </button>
                            <button type="button" @click="chartTab = 'kategori'"
                                    :class="chartTab === 'kategori' ? 'bg-white text-[#0284C7] shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-2.5 py-1 rounded-lg transition">
                                Kategori
                            </button>
                        </div>
                    </div>

                    <!-- Grafik Batang Produk Terlaris -->
                    <div x-show="chartTab === 'produk'" class="space-y-3.5">
                        <?php if (!empty($topProduk)): ?>
                            <?php foreach ($topProduk as $idx => $prod): ?>
                                <?php $pct = round(((int)$prod['jumlah'] / $maxProdJml) * 100); ?>
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-1">
                                        <span class="font-bold text-slate-800 truncate pr-2"><?= ($idx + 1) . '. ' . esc($prod['nama_produk']) ?></span>
                                        <span class="font-black text-[#0284C7] shrink-0"><?= $prod['jumlah'] ?>x</span>
                                    </div>
                                    <div class="w-full bg-sky-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-[#38BDF8] to-[#0284C7] h-full rounded-full animate-bar" style="width: <?= $pct ?>%"></div>
                                    </div>
                                    <span class="text-[10px] text-slate-400"><?= esc($prod['kategori_produk']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 text-center py-8">Belum ada data produk terjual.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Grafik Batang Kategori Terlaris -->
                    <div x-show="chartTab === 'kategori'" class="space-y-4" style="display: none;">
                        <?php if (!empty($topKategori)): ?>
                            <?php foreach ($topKategori as $idx => $kat): ?>
                                <?php $pctK = round(((int)$kat['jumlah'] / $maxKatJml) * 100); ?>
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-1">
                                        <span class="font-bold text-slate-800"><?= ($idx + 1) . '. ' . esc($kat['kategori_produk']) ?></span>
                                        <span class="font-black text-indigo-600 shrink-0"><?= $kat['jumlah'] ?> item</span>
                                    </div>
                                    <div class="w-full bg-indigo-50 h-3 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-400 to-[#0284C7] h-full rounded-full animate-bar" style="width: <?= $pctK ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 text-center py-8">Belum ada data kategori.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 text-[11px] text-slate-500 flex justify-between items-center">
                    <span>Data diambil dari <?= number_format($totalStruk, 0, ',', '.') ?> struk transaksi</span>
                    <a href="<?= base_url('/dataset') ?>" class="font-bold text-[#0284C7] hover:underline">Lihat Data &rarr;</a>
                </div>
            </div>

        </div>

        <!-- =================================================================== -->
        <!-- 4. ALUR PENGGUNAAN SISTEM 3 LANGKAH (RINGKAS & BERSIH) -->
        <!-- =================================================================== -->
        <div class="bg-white rounded-3xl p-6 shadow-md shadow-sky-900/5 border border-sky-100 animate-fade-up delay-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <h2 class="text-base sm:text-lg font-black text-slate-900">Cara Menggunakan Sistem dalam 3 Langkah</h2>
                <span class="text-xs text-slate-400">Alur Analisis Market Basket Apriori</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="<?= base_url('/dataset') ?>" class="p-4 rounded-2xl bg-sky-50/40 border border-sky-100 hover:border-sky-300 hover:bg-sky-50 transition group">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-7 h-7 rounded-lg bg-[#0284C7] text-white font-black text-xs flex items-center justify-center shadow-sm">01</span>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-[#0284C7] transition">Siapkan Data Toko</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Kelola daftar 95 produk sembako dan impor riwayat struk belanja melalui CSV di halaman Dataset.
                    </p>
                </a>

                <a href="<?= base_url('/rekomendasi') ?>" class="p-4 rounded-2xl bg-sky-50/40 border border-sky-100 hover:border-sky-300 hover:bg-sky-50 transition group">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-7 h-7 rounded-lg bg-[#0284C7] text-white font-black text-xs flex items-center justify-center shadow-sm">02</span>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-[#0284C7] transition">Jalankan Apriori</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pilih preset Standar Toko atau atur batas Support, Confidence, dan Lift Ratio untuk mencari pasangan barang.
                    </p>
                </a>

                <a href="<?= base_url('/history') ?>" class="p-4 rounded-2xl bg-sky-50/40 border border-sky-100 hover:border-sky-300 hover:bg-sky-50 transition group">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-7 h-7 rounded-lg bg-[#0284C7] text-white font-black text-xs flex items-center justify-center shadow-sm">03</span>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-[#0284C7] transition">Terapkan &amp; Simpan</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ikuti panduan penataan rak dalam bahasa awam, tinjau perhitungan manualnya, dan simpan ke Riwayat.
                    </p>
                </a>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
