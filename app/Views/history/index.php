<?php helper(['form', 'url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#F0F7FF] py-6 px-4 sm:px-6 lg:px-8 text-slate-800">
    <div class="container mx-auto max-w-7xl">

        <!-- Header Riwayat -->
        <div class="bg-white rounded-3xl p-6 mb-6 shadow-md shadow-sky-900/5 border border-sky-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-[#38BDF8] to-[#0284C7] text-white rounded-2xl flex items-center justify-center shadow-md shadow-sky-500/20 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-[11px] font-bold text-[#0369A1] mb-1">
                        <span>Riwayat Penataan Produk Sembako</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Riwayat Rekomendasi Tata Letak
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tinjau kembali hasil aturan asosiasi Apriori dan panduan penataan rak yang telah disimpan</p>
                </div>
            </div>
            
            <!-- Counter -->
            <div class="bg-sky-50/70 rounded-2xl px-6 py-3.5 border border-sky-200 text-center shrink-0">
                <p class="text-2xl sm:text-3xl font-black text-[#0284C7]"><?= count($history) ?></p>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Total Riwayat</p>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-teal-50 border-l-4 border-teal-500 rounded-2xl p-4 mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-xs sm:text-sm text-teal-800 font-medium"><?= session()->getFlashdata('success') ?></p>
            </div>
        <?php elseif (session()->getFlashdata('error')) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 rounded-2xl p-4 mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <p class="text-xs sm:text-sm text-red-800 font-medium"><?= session()->getFlashdata('error') ?></p>
            </div>
        <?php endif; ?>

        <!-- List Riwayat -->
        <div class="space-y-4">
            <?php if (empty($history)) : ?>
                <div class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 p-12 text-center max-w-lg mx-auto">
                    <div class="w-20 h-20 bg-sky-50 border border-sky-200 rounded-3xl flex items-center justify-center mx-auto mb-4 text-[#0284C7]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Riwayat Tersimpan</h3>
                    <p class="text-xs text-slate-500 mb-6">Jalankan proses rekomendasi tata letak produk dengan algoritma Apriori dan simpan hasilnya di sini.</p>
                    <a href="<?= base_url('rekomendasi') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0284C7] hover:bg-[#0369A1] text-white font-bold rounded-xl transition shadow-md text-xs sm:text-sm">
                        <span>Mulai Rekomendasi Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            <?php else : ?>
                <?php foreach ($history as $index => $row) : ?>
                    <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md shadow-sky-900/5 border border-sky-100 hover:border-sky-300 transition flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-200 flex items-center justify-center text-[#0284C7] flex-shrink-0 mt-1">
                                <span class="font-black text-sm">#<?= $row['id'] ?></span>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="font-bold text-base text-slate-900">
                                        Rekomendasi Tata Letak Sembako
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-[11px] font-bold">
                                        <?= $row['total_rules'] ?> Aturan Asosiasi
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-sky-50 text-[#0369A1] border border-sky-200 text-[11px] font-bold">
                                        <?= $row['total_rekomendasi'] ?> Rekomendasi Rak
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mb-2">
                                    Tanggal Eksekusi: <strong class="text-slate-700"><?= date('d F Y, H:i', strtotime($row['tanggal_analisis'])) ?> WIB</strong>
                                    &bull; Transaksi: <strong class="text-slate-700"><?= number_format($row['total_data'], 0, ',', '.') ?> struk</strong>
                                </p>
                                <div class="inline-flex flex-wrap items-center gap-3 text-xs bg-sky-50/50 px-3 py-1.5 rounded-xl border border-sky-100">
                                    <span>Threshold Support: <strong class="text-[#0284C7]"><?= $row['threshold_support'] ?? 0.2 ?></strong></span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span>Threshold Supp×Conf: <strong class="text-[#0284C7]"><?= $row['threshold_support_confidence'] ?? 0.5 ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center gap-2 self-end md:self-center">
                            <a href="<?= base_url('history/detail/' . $row['id']) ?>" 
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#0284C7] hover:bg-[#0369A1] text-white rounded-xl text-xs font-bold shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Lihat Detail Planogram</span>
                            </a>
                            <a href="<?= base_url('history/delete/' . $row['id']) ?>" 
                               onclick="return confirm('Hapus data riwayat ini secara permanen?');"
                               class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus Riwayat">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
