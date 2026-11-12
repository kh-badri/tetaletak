<?php helper('form'); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#FFE1AF] p-4 lg:p-8 text-[#957C62]">
    <div class="container mx-auto max-w-7xl">

        <!-- Header Riwayat -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-[#B77466] text-white rounded-3xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/60 text-xs font-semibold text-[#957C62] mb-1">
                        <span>Riwayat Penataan Produk Sembako</span>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-black text-[#957C62] tracking-tight">
                        Riwayat Rekomendasi Tata Letak
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-700 mt-0.5">Tinjau kembali hasil aturan asosiasi Apriori dan simulasi penataan rak yang telah disimpan</p>
                </div>
            </div>
            
            <!-- Counter -->
            <div class="bg-white rounded-2xl px-6 py-4 border border-[#E2B59A]/50 shadow-md text-center">
                <p class="text-3xl font-black text-[#B77466]"><?= count($history) ?></p>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Riwayat</p>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-2xl p-5 mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-xs sm:text-sm text-emerald-800 font-medium"><?= session()->getFlashdata('success') ?></p>
            </div>
        <?php elseif (session()->getFlashdata('error')) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 rounded-2xl p-5 mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <p class="text-xs sm:text-sm text-red-800 font-medium"><?= session()->getFlashdata('error') ?></p>
            </div>
        <?php endif; ?>

        <!-- List Riwayat -->
        <div class="space-y-4">
            <?php if (empty($history)) : ?>
                <div class="bg-white rounded-3xl shadow-xl border border-gray-200 p-12 text-center max-w-lg mx-auto">
                    <div class="w-20 h-20 bg-[#FFE1AF] rounded-3xl flex items-center justify-center mx-auto mb-4 text-[#B77466]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#957C62] mb-2">Belum Ada Riwayat Tersimpan</h3>
                    <p class="text-xs text-gray-500 mb-6">Jalankan proses rekomendasi tata letak produk dengan algoritma Apriori dan simpan hasilnya di sini.</p>
                    <a href="<?= base_url('rekomendasi') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-[#B77466] hover:bg-[#957C62] text-white font-bold rounded-xl transition shadow-md text-xs sm:text-sm">
                        <span>Mulai Rekomendasi Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            <?php else : ?>
                <?php foreach ($history as $index => $row) : ?>
                    <div class="bg-white rounded-3xl p-6 shadow-md border border-[#E2B59A]/40 hover:border-[#B77466] transition flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFE1AF] flex items-center justify-center text-[#B77466] flex-shrink-0 mt-1">
                                <span class="font-black text-sm">#<?= $row['id'] ?></span>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="font-bold text-base text-gray-900">
                                        Rekomendasi Tata Letak Sembako
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                        <?= $row['total_rules'] ?> Aturan Asosiasi
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[11px] font-bold">
                                        <?= $row['total_rekomendasi'] ?> Rekomendasi Rak
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">
                                    Tanggal Eksekusi: <strong class="text-gray-700"><?= date('d F Y, H:i', strtotime($row['tanggal_analisis'])) ?> WIB</strong>
                                    &bull; Transaksi: <strong class="text-gray-700"><?= number_format($row['total_data'], 0, ',', '.') ?> struk</strong>
                                </p>
                                <div class="inline-flex items-center gap-3 text-xs bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200">
                                    <span>Min. Support: <strong class="text-[#B77466]"><?= $row['min_support'] ?>%</strong></span>
                                    <span>&bull;</span>
                                    <span>Min. Confidence: <strong class="text-[#B77466]"><?= $row['min_confidence'] ?>%</strong></span>
                                    <span>&bull;</span>
                                    <span>Min. Lift: <strong class="text-[#B77466]"><?= $row['min_lift'] ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center gap-2 self-end md:self-center">
                            <a href="<?= base_url('history/detail/' . $row['id']) ?>" 
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#B77466] hover:bg-[#957C62] text-white rounded-xl text-xs font-bold shadow transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Lihat Detail Planogram</span>
                            </a>
                            <a href="<?= base_url('history/delete/' . $row['id']) ?>" 
                               onclick="return confirm('Hapus data riwayat ini secara permanen?');"
                               class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus Riwayat">
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
