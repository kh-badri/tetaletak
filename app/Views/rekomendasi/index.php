<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-8 px-4 sm:px-6 text-slate-800" 
     x-data="{
        support: '<?= old('min_support', '3.0') ?>',
        confidence: '<?= old('min_confidence', '50.0') ?>',
        lift: '<?= old('min_lift', '1.2') ?>',
        preset: 'standar',
        showRumus: false,
        setPreset(type) {
            this.preset = type;
            if (type === 'standar') {
                this.support = '3.0';
                this.confidence = '50.0';
                this.lift = '1.2';
            } else if (type === 'ketat') {
                this.support = '4.0';
                this.confidence = '60.0';
                this.lift = '1.5';
            } else if (type === 'luas') {
                this.support = '2.0';
                this.confidence = '30.0';
                this.lift = '1.0';
            }
        }
     }">
    <div class="container mx-auto max-w-3xl">

        <!-- Notifikasi Error jika ada -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-2xl mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        <?php endif; ?>

        <!-- KARTU UTAMA ANALISIS REKOMENDASI -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-sky-900/5 border border-sky-100">
            
            <!-- Header Sederhana -->
            <div class="text-center mb-7">
                <div class="w-14 h-14 mx-auto bg-gradient-to-br from-[#38BDF8] to-[#0284C7] text-white rounded-2xl shadow-md shadow-sky-500/20 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Cari Rekomendasi Penataan Rak</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-lg mx-auto">
                    Sistem akan mempelajari kebiasaan belanja pembeli dan merekomendasikan barang apa saja yang sebaiknya diletakkan berdampingan di toko.
                </p>

                <!-- Info Dataset Siap Pakai -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                    <span class="px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-xs font-bold text-[#0369A1]">
                        <?= number_format($totalTrx ?? 0, 0, ',', '.') ?> Struk Belanja
                    </span>
                    <span class="px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-xs font-bold text-teal-800">
                        <?= number_format($totalBaris ?? 0, 0, ',', '.') ?> Item Transaksi
                    </span>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                        Data 3 Bulan (Agust–Okt 2026)
                    </span>
                </div>
            </div>

            <!-- Pilihan Cepat (Preset) -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Pilih Mode Pencarian Cepat (Atau Sesuaikan Angka di Bawah)</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <button type="button" @click="setPreset('standar')"
                            :class="preset === 'standar' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">⭐ Standar Toko</span>
                        <span class="text-[11px] opacity-85">Seimbang (Disarankan)</span>
                    </button>

                    <button type="button" @click="setPreset('ketat')"
                            :class="preset === 'ketat' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">🔥 Khusus Terlaris</span>
                        <span class="text-[11px] opacity-85">Hanya yang paling pasti</span>
                    </button>

                    <button type="button" @click="setPreset('luas')"
                            :class="preset === 'luas' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">🔍 Lebih Banyak</span>
                        <span class="text-[11px] opacity-85">Tampilkan banyak opsi</span>
                    </button>
                </div>
            </div>

            <!-- Form Pengaturan Parameter -->
            <?= form_open('rekomendasi/proses', ['class' => 'space-y-5']) ?>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Support -->
                    <div class="bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="min_support" class="text-xs font-bold text-slate-700">Min. Support (%)</label>
                            <span class="text-xs font-black text-[#0284C7]" x-text="support + '%'">3.0%</span>
                        </div>
                        <input type="number" step="any" min="0.1" max="100" name="min_support" id="min_support" 
                               x-model="support" @input="preset = 'custom'"
                               class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-bold text-slate-900" required>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Minimal seberapa sering barang dibeli dari seluruh struk.</p>
                    </div>

                    <!-- Confidence -->
                    <div class="bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="min_confidence" class="text-xs font-bold text-slate-700">Min. Confidence (%)</label>
                            <span class="text-xs font-black text-[#0284C7]" x-text="confidence + '%'">50.0%</span>
                        </div>
                        <input type="number" step="any" min="0.1" max="100" name="min_confidence" id="min_confidence" 
                               x-model="confidence" @input="preset = 'custom'"
                               class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-bold text-slate-900" required>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Minimal peluang pembeli mengambil produk pasangannya.</p>
                    </div>

                    <!-- Lift Ratio -->
                    <div class="bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="min_lift" class="text-xs font-bold text-slate-700">Min. Lift Ratio</label>
                            <span class="text-xs font-black text-[#0284C7]" x-text="lift">1.2</span>
                        </div>
                        <input type="number" step="any" min="0" max="20" name="min_lift" id="min_lift" 
                               x-model="lift" @input="preset = 'custom'"
                               class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-bold text-slate-900" required>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Batas kekuatan hubungan (&gt; 1.0 berarti saling menguatkan).</p>
                    </div>
                </div>

                <!-- Tombol Submit Utama -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white font-black py-4 px-6 rounded-2xl transition duration-200 shadow-lg shadow-sky-500/20 hover:shadow-xl flex items-center justify-center gap-2.5 text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Proses &amp; Lihat Rekomendasi Tata Letak</span>
                </button>

            <?= form_close() ?>

            <!-- Accordion Opsional: Sekilas Cara Kerja & Rumus -->
            <div class="mt-7 pt-5 border-t border-slate-100">
                <button type="button" @click="showRumus = !showRumus" 
                        class="w-full flex items-center justify-between text-xs font-bold text-slate-600 hover:text-[#0284C7] transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#0284C7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Apa arti Support, Confidence, &amp; Lift? (Klik untuk melihat penjelasan singkat)
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="showRumus ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="showRumus" x-collapse class="mt-4 grid sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-100">
                        <strong class="text-[#0284C7] block mb-1">1. Support</strong>
                        <p class="text-slate-600 leading-relaxed">Mengukur seberapa sering suatu barang atau pasangan barang muncul dalam semua struk belanja.</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-100">
                        <strong class="text-[#0284C7] block mb-1">2. Confidence</strong>
                        <p class="text-slate-600 leading-relaxed">Menghitung kepastian: <span class="italic">"Jika pelanggan ambil Barang A, berapa persen peluangnya ia mengambil Barang B?"</span></p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-100">
                        <strong class="text-[#0284C7] block mb-1">3. Lift Ratio</strong>
                        <p class="text-slate-600 leading-relaxed">Menguji kevalidan pasangan. Jika nilai <strong>&gt; 1.0</strong>, kedua barang benar-benar cocok diletakkan bersebelahan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>
