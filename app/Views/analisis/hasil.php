<?php
// File: app/Views/analisis/hasil.php
helper('form');

if (!function_exists('find_feature_data')) {
    function find_feature_data($analisis_array, $feature_name)
    {
        if (!is_array($analisis_array)) {
            return ['rata_rata_ya_turun' => 0, 'rata_rata_tidak_turun' => 0];
        }
        foreach ($analisis_array as $item) {
            if (isset($item['fitur']) && $item['fitur'] == $feature_name) {
                return $item;
            }
        }
        return ['rata_rata_ya_turun' => 0, 'rata_rata_tidak_turun' => 0];
    }
}

if (isset($hasil) && $hasil['status'] == 'success') {
    $analisis = $hasil['analisis'];
    $evaluasi = $hasil['evaluasi'];

    // ASUMSI: Data detail prediksi ada di $hasil['detail_prediksi'] 
    // Jika key di controller berbeda, silakan sesuaikan namanya
    $detail_prediksi = $hasil['detail_prediksi'] ?? [];

    $data_harga = find_feature_data($analisis, 'harga_produk_rp');
    $data_jumlah = find_feature_data($analisis, 'jumlah_terjual_unit');
    $data_bulan = find_feature_data($analisis, 'bulan');

    $interpretasi_harga = ($data_harga['rata_rata_ya_turun'] > $data_harga['rata_rata_tidak_turun'])
        ? "Harga Cenderung LEBIH MAHAL"
        : "Harga Cenderung LEBIH MURAH";
    $warna_harga = ($interpretasi_harga == "Harga Cenderung LEBIH MAHAL") ? "text-[#B77466]" : "text-green-600";

    $interpretasi_jumlah = ($data_jumlah['rata_rata_ya_turun'] < $data_jumlah['rata_rata_tidak_turun'])
        ? "Penjualan Cenderung LEBIH SEDIKIT"
        : "Penjualan Cenderung LEBIH BANYAK";
    $warna_jumlah = ($interpretasi_jumlah == "Penjualan Cenderung LEBIH SEDIKIT") ? "text-[#B77466]" : "text-green-600";

    $nama_bulan_ya = date('F', mktime(0, 0, 0, (int)$data_bulan['rata_rata_ya_turun'], 10));
    $interpretasi_bulan = "Cenderung terjadi di sekitar bulan " . $nama_bulan_ya;
}
?>

<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#FFE1AF] p-4 lg:p-8 text-[#957C62]">
    <div class="container mx-auto">

        <div class="flex justify-between items-center mb-6">
            <a href="<?= base_url('analisis') ?>" class="text-[#957C62] font-semibold hover:text-[#B77466] transition duration-300 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Analisis
            </a>

            <?php if (session()->has('hasil_analisis_terakhir')) : ?>
                <?= form_open('analisis/simpan') ?>
                <button type="submit" class="bg-[#957C62] hover:bg-opacity-80 text-white font-bold py-2 px-5 rounded-lg transition duration-300 shadow-lg transform hover:scale-105 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Hasil Ini
                </button>
                <?= form_close() ?>
            <?php endif; ?>
        </div>

        <?php if (isset($hasil) && $hasil['status'] == 'success') : ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-8 space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200 text-center">
                            <h3 class="text-lg font-medium text-[#957C62]">Akurasi Prediksi Model</h3>
                            <p class="text-7xl font-bold text-[#B77466] my-3">
                                <?= number_format($evaluasi['accuracy'], 2) ?><span class="text-5xl">%</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                Berdasarkan data testing sebanyak <?= $evaluasi['total_testing'] ?> baris.
                            </p>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                            <h3 class="text-xl font-semibold text-[#957C62] mb-4 border-b border-[#E2B59A] pb-2 text-center">
                                Karakteristik Produk Turun
                            </h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <span class="bg-[#FFE1AF] p-2 rounded-lg mr-3">💰</span>
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm">Harga</span>
                                        <p class="text-xs <?= $warna_harga ?>"><?= $interpretasi_harga ?></p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="bg-[#FFE1AF] p-2 rounded-lg mr-3">📉</span>
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm">Penjualan</span>
                                        <p class="text-xs <?= $warna_jumlah ?>"><?= $interpretasi_jumlah ?></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-1.5 flex space-x-2">
                        <button data-tab-target="tab-daftar" class="tab-button active flex-1 text-center font-semibold py-2.5 px-4 rounded-lg bg-[#B77466] text-white shadow transition-all">
                            Produk Diprediksi Turun
                        </button>
                        <button data-tab-target="tab-analisis" class="tab-button flex-1 text-center font-semibold py-2.5 px-4 rounded-lg text-gray-500 hover:bg-[#FFE1AF]/40">
                            Analisis Statistik
                        </button>
                        <button data-tab-target="tab-evaluasi" class="tab-button flex-1 text-center font-semibold py-2.5 px-4 rounded-lg text-gray-500 hover:bg-[#FFE1AF]/40">
                            Detail Performa
                        </button>
                    </div>

                    <div id="tab-daftar" class="tab-content">
                        <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-semibold text-[#957C62]">Daftar Prediksi Penurunan Penjualan Produk </h2>
                                <span class="bg-[#B77466] text-white text-xs px-3 py-1 rounded-full">
                                    Total: <?= count(array_filter($detail_prediksi, fn($x) => $x['prediksi'] == 'Ya')) ?> Produk
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-[#FFE1AF]/50 text-[#957C62]">
                                            <th class="p-3 border-b border-[#E2B59A] font-bold">Produk</th>
                                            <th class="p-3 border-b border-[#E2B59A] font-bold">Harga</th>
                                            <th class="p-3 border-b border-[#E2B59A] font-bold">Terjual</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ada_turun = false;
                                        foreach ($detail_prediksi as $row) :
                                            if ($row['prediksi'] == 'Ya') :
                                                $ada_turun = true;
                                        ?>
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="p-3 border-b border-gray-100 font-medium text-gray-800">
                                                        <?= $row['nama_produk'] ?? 'Produk ID: ' . $row['produk_id'] ?>
                                                    </td>
                                                    <td class="p-3 border-b border-gray-100 text-sm">
                                                        Rp <?= number_format($row['harga_produk_rp'], 0, ',', '.') ?>
                                                    </td>
                                                    <td class="p-3 border-b border-gray-100 text-sm">
                                                        <?= $row['jumlah_terjual_unit'] ?> unit
                                                    </td>

                                                </tr>
                                            <?php
                                            endif;
                                        endforeach;

                                        if (!$ada_turun): ?>
                                            <tr>
                                                <td colspan="4" class="p-8 text-center text-gray-400 italic">
                                                    Tidak ada produk yang diprediksi mengalami penurunan pada data ini.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-4 text-xs text-gray-500 italic">* Tabel di atas menampilkan data uji yang diprediksi akan mengalami penurunan penjualan oleh algoritma Naive Bayes.</p>
                        </div>
                    </div>

                    <div id="tab-analisis" class="tab-content hidden">
                        <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                            <h2 class="text-2xl font-semibold mb-6 text-[#957C62]">Analisis Rata-rata Model</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-[#FFE1AF]/40 p-5 rounded-lg border border-[#E2B59A]">
                                    <h3 class="font-bold text-[#957C62] mb-3 flex items-center">
                                        <span class="mr-2">💰</span> Harga Rata-rata
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span>Kelas "Ya Turun":</span>
                                            <span class="font-bold">Rp <?= number_format($data_harga['rata_rata_ya_turun'], 0, ',', '.') ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Kelas "Tidak Turun":</span>
                                            <span class="font-bold text-green-600">Rp <?= number_format($data_harga['rata_rata_tidak_turun'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-[#FFE1AF]/40 p-5 rounded-lg border border-[#E2B59A]">
                                    <h3 class="font-bold text-[#957C62] mb-3 flex items-center">
                                        <span class="mr-2">📉</span> Unit Terjual Rata-rata
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span>Kelas "Ya Turun":</span>
                                            <span class="font-bold"><?= number_format($data_jumlah['rata_rata_ya_turun'], 0) ?> unit</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Kelas "Tidak Turun":</span>
                                            <span class="font-bold text-green-600"><?= number_format($data_jumlah['rata_rata_tidak_turun'], 0) ?> unit</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-evaluasi" class="tab-content hidden">
                        <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                            <h3 class="text-xl font-semibold text-[#957C62] mb-4">Confusion Matrix</h3>
                            <?php $cm = $evaluasi['confusion_matrix']; ?>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-[#E2B59A] text-sm">
                                    <thead>
                                        <tr>
                                            <th class="p-2 border border-[#E2B59A] bg-gray-50" colspan="2" rowspan="2"></th>
                                            <th class="p-2 border border-[#E2B59A] bg-gray-100 text-center" colspan="2">Prediksi Model</th>
                                        </tr>
                                        <tr>
                                            <th class="p-2 border border-[#E2B59A] bg-[#FFE1AF]/40 text-center uppercase">Tidak</th>
                                            <th class="p-2 border border-[#E2B59A] bg-[#B77466]/20 text-center uppercase">Ya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <th class="p-2 border border-[#E2B59A] bg-gray-100 w-1/4 uppercase" rowspan="2">Data Aktual</th>
                                            <th class="p-2 border border-[#E2B59A] bg-[#FFE1AF]/40 uppercase">Tidak</th>
                                            <td class="p-4 border border-[#E2B59A] bg-green-50 font-bold text-lg text-green-700"><?= $cm['true_negative'] ?></td>
                                            <td class="p-4 border border-[#E2B59A] bg-red-50 text-red-700"><?= $cm['false_positive'] ?></td>
                                        </tr>
                                        <tr class="text-center">
                                            <th class="p-2 border border-[#E2B59A] bg-[#B77466]/20 uppercase">Ya</th>
                                            <td class="p-4 border border-[#E2B59A] bg-red-50 text-red-700"><?= $cm['false_negative'] ?></td>
                                            <td class="p-4 border border-[#E2B59A] bg-green-50 font-bold text-lg text-green-700"><?= $cm['true_positive'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <?php else : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
                <p class="font-bold">Gagal Memproses Hasil</p>
                <p><?= $hasil['message'] ?? 'Terjadi kesalahan sistem.' ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tabTarget;

                // Reset Tabs
                tabs.forEach(t => {
                    t.classList.remove('active', 'bg-[#B77466]', 'text-white', 'shadow');
                    t.classList.add('text-gray-500');
                });

                // Set Active Tab
                tab.classList.add('active', 'bg-[#B77466]', 'text-white', 'shadow');
                tab.classList.remove('text-gray-500');

                // Hide All Content
                contents.forEach(c => c.classList.add('hidden'));

                // Show Target Content
                document.getElementById(target).classList.remove('hidden');
            });
        });
    });
</script>

<?= $this->endSection(); ?>