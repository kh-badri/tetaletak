<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-8 px-4 sm:px-6 text-slate-800" 
     x-data="{
        support: '<?= old('threshold_support', '0.2') ?>',
        suppConf: '<?= old('threshold_support_confidence', '0.5') ?>',
        preset: 'dospem',
        showRumus: false,
        setPreset(type) {
            this.preset = type;
            if (type === 'dospem') {
                this.support = '0.2';
                this.suppConf = '0.5';
            } else if (type === 'optimal') {
                this.support = '0.03';
                this.suppConf = '0.015';
            } else if (type === 'luas') {
                this.support = '0.02';
                this.suppConf = '0.01';
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
            
            <!-- Header -->
            <div class="text-center mb-7">
                <div class="w-14 h-14 mx-auto bg-gradient-to-br from-[#38BDF8] to-[#0284C7] text-white rounded-2xl shadow-md shadow-sky-500/20 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Cari Rekomendasi Penataan Rak</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-lg mx-auto">
                    Analisis Market Basket (Algoritma Apriori) dengan 2 parameter uji: <strong>Threshold Support</strong> dan <strong>Threshold Support x Confidence</strong>.
                </p>

                <!-- Info Dataset -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                    <span class="px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-xs font-bold text-[#0369A1]">
                        <?= number_format($totalTrx ?? 0, 0, ',', '.') ?> Transaksi
                    </span>
                    <span class="px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-xs font-bold text-teal-800">
                        <?= number_format($totalBaris ?? 0, 0, ',', '.') ?> Item Tercatat
                    </span>
                </div>
            </div>

            <!-- Pilihan Cepat (Preset) -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Pilih Preset Parameter (Atau Ubah Langsung Angka di Bawah)</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <button type="button" @click="setPreset('dospem')"
                            :class="preset === 'dospem' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">Default (0.2 / 0.5)</span>
                        <span class="text-[11px] opacity-85">Standar Ambang Awal</span>
                    </button>

                    <button type="button" @click="setPreset('optimal')"
                            :class="preset === 'optimal' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">Optimal 60 Produk</span>
                        <span class="text-[11px] opacity-85">Supp 0.03 &bull; S×C 0.015</span>
                    </button>

                    <button type="button" @click="setPreset('luas')"
                            :class="preset === 'luas' ? 'bg-[#0284C7] text-white border-[#0284C7] shadow-md shadow-sky-500/20' : 'bg-sky-50/40 text-slate-700 border-sky-200 hover:bg-sky-50'"
                            class="p-3 rounded-2xl border text-left transition">
                        <span class="font-black text-xs block">Lebih Banyak Opsi</span>
                        <span class="text-[11px] opacity-85">Supp 0.02 &bull; S×C 0.01</span>
                    </button>
                </div>
            </div>

            <!-- Form Pengaturan 2 Parameter -->
            <?= form_open('rekomendasi/proses', ['class' => 'space-y-5']) ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Threshold Support -->
                    <div class="bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="threshold_support" class="text-xs font-bold text-slate-700">Threshold Support</label>
                            <span class="text-xs font-black text-[#0284C7]" x-text="support">0.2</span>
                        </div>
                        <input type="number" step="any" min="0" max="1" name="threshold_support" id="threshold_support" 
                               x-model="support" @input="preset = 'custom'"
                               class="w-full px-3.5 py-2.5 bg-white border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-bold text-slate-900" required>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Skala desimal 0 – 1 (contoh: 0.2 = 20%, 0.03 = 3%).</p>
                    </div>

                    <!-- Threshold Support x Confidence -->
                    <div class="bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="threshold_support_confidence" class="text-xs font-bold text-slate-700">Threshold Support x Confidence</label>
                            <span class="text-xs font-black text-[#0284C7]" x-text="suppConf">0.5</span>
                        </div>
                        <input type="number" step="any" min="0" max="1" name="threshold_support_confidence" id="threshold_support_confidence" 
                               x-model="suppConf" @input="preset = 'custom'"
                               class="w-full px-3.5 py-2.5 bg-white border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-bold text-slate-900" required>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Batas minimum perkalian Support &times; Confidence (skala 0 – 1).</p>
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

            <!-- Accordion Penjelasan Rumus -->
            <div class="mt-7 pt-5 border-t border-slate-100">
                <button type="button" @click="showRumus = !showRumus" 
                        class="w-full flex items-center justify-between text-xs font-bold text-slate-600 hover:text-[#0284C7] transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#0284C7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Bagaimana cara kerja perhitungan 2 parameter ini?
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="showRumus ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="showRumus" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs" x-cloak>
                    <div class="bg-sky-50/50 p-3.5 rounded-xl border border-sky-100">
                        <span class="font-black text-[#0284C7] block mb-1">1. Support</span>
                        <p class="text-slate-600 leading-relaxed">Jumlah transaksi mengandung produk IF &amp; THEN dibagi Total Transaksi.</p>
                    </div>
                    <div class="bg-sky-50/50 p-3.5 rounded-xl border border-sky-100">
                        <span class="font-black text-indigo-600 block mb-1">2. Confidence</span>
                        <p class="text-slate-600 leading-relaxed">Jumlah transaksi mengandung produk IF &amp; THEN dibagi jumlah transaksi produk THEN.</p>
                    </div>
                    <div class="bg-sky-50/50 p-3.5 rounded-xl border border-sky-100">
                        <span class="font-black text-teal-600 block mb-1">3. Support &times; Confidence</span>
                        <p class="text-slate-600 leading-relaxed">Perkalian Support dan Confidence untuk menentukan ranking rekomendasi terkuat.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>
