<?php

namespace App\Controllers;

use App\Libraries\AprioriService;

class Home extends BaseController
{
    /**
     * Menampilkan halaman dashboard beranda dengan statistik live toko sembako
     * dan pratinjau rekomendasi tata letak produk Algoritma Apriori.
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Statistik ringkas database
        $totalProduk  = $db->table('produk')->countAllResults();
        $totalItemTrx = $db->table('dataset_produk')->countAllResults();
        $trxRow       = $db->query("SELECT COUNT(DISTINCT no_transaksi) as total_struk FROM dataset_produk")->getRowArray();
        $totalStruk   = (int) ($trxRow['total_struk'] ?? 0);
        $totalRiwayat = $db->table('history_analisis')->countAllResults();

        // Top 5 Kategori Paling Sering Dibeli
        $topKategori = $db->query("
            SELECT kategori_produk, COUNT(*) as jumlah 
            FROM dataset_produk 
            GROUP BY kategori_produk 
            ORDER BY jumlah DESC 
            LIMIT 5
        ")->getResultArray();

        // Top 5 Produk Sembako Paling Laku
        $topProduk = $db->query("
            SELECT nama_produk, kategori_produk, COUNT(*) as jumlah 
            FROM dataset_produk 
            GROUP BY nama_produk, kategori_produk 
            ORDER BY jumlah DESC 
            LIMIT 5
        ")->getResultArray();

        // Pratinjau Rekomendasi Tata Letak (riwayat terbaru atau hitung cepat)
        $topRekomendasi    = [];
        $sumberRekomendasi = 'live';

        $latestHistory = $db->table('history_analisis')
            ->orderBy('tanggal_analisis', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!empty($latestHistory) && !empty($latestHistory['hasil_evaluasi'])) {
            $decoded = json_decode($latestHistory['hasil_evaluasi'], true);
            if (is_array($decoded) && !empty($decoded)) {
                $topRekomendasi    = array_slice($decoded, 0, 3);
                $sumberRekomendasi = 'history';
            }
        }

        if (empty($topRekomendasi) && $totalStruk > 0) {
            try {
                $apriori           = new AprioriService();
                $res               = $apriori->run(2.0, 20.0, 1.0);
                $topRekomendasi    = array_slice($res['rekomendasi_tataletak'] ?? [], 0, 3);
                $sumberRekomendasi = 'live';
            } catch (\Throwable $e) {
                $topRekomendasi = [];
            }
        }

        $data = [
            'title'             => 'Dashboard | Tata Letak Produk Sembako (Apriori)',
            'active_menu'       => 'home',
            'totalProduk'       => $totalProduk,
            'totalStruk'        => $totalStruk,
            'totalItemTrx'      => $totalItemTrx,
            'totalRiwayat'      => $totalRiwayat,
            'topKategori'       => $topKategori,
            'topProduk'         => $topProduk,
            'topRekomendasi'    => $topRekomendasi,
            'sumberRekomendasi' => $sumberRekomendasi
        ];

        return view('home/index', $data);
    }
}
