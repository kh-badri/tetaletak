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

        // Statistik ringkas database (mendukung 1 baris = 1 transaksi dengan produk dipisah ';')
        $totalProduk  = $db->table('produk')->countAllResults();
        $trxRow       = $db->query("SELECT COUNT(DISTINCT no_transaksi) as total_struk FROM dataset_produk")->getRowArray();
        $totalStruk   = (int) ($trxRow['total_struk'] ?? 0);
        $totalRiwayat = $db->table('history_analisis')->countAllResults();

        $produkCatMap  = [];
        $produkCodeMap = [];
        foreach ($db->table('produk')->select('id, nama_produk, kategori_produk')->get()->getResultArray() as $pr) {
            $produkCatMap[strtolower(trim($pr['nama_produk']))] = $pr['kategori_produk'];
            $produkCodeMap['p' . $pr['id']] = ['nama' => $pr['nama_produk'], 'kat' => $pr['kategori_produk']];
        }

        $prodFreq = [];
        $katFreq  = [];
        $totalItemTrx = 0;
        $dsRows = $db->table('dataset_produk')->select('nama_produk, kategori_produk')->get()->getResultArray();
        foreach ($dsRows as $dr) {
            $items = preg_split('/[;,]+/', $dr['nama_produk']);
            $kats  = preg_split('/[;,]+/', $dr['kategori_produk'] ?? '');
            foreach ($items as $idx => $it) {
                $rawToken = trim($it);
                if ($rawToken === '') continue;
                $totalItemTrx++;
                $lower = strtolower($rawToken);
                if (isset($produkCodeMap[$lower])) {
                    $nama = $produkCodeMap[$lower]['nama'];
                    $kat  = $produkCodeMap[$lower]['kat'];
                } else {
                    $nama = $rawToken;
                    $kat  = $produkCatMap[$lower] ?? trim($kats[$idx] ?? $kats[0] ?? 'Sembako');
                }
                if (!isset($prodFreq[$nama])) {
                    $prodFreq[$nama] = ['nama_produk' => $nama, 'kategori_produk' => $kat, 'jumlah' => 0];
                }
                $prodFreq[$nama]['jumlah']++;
                $katFreq[$kat] = ($katFreq[$kat] ?? 0) + 1;
            }
        }

        usort($prodFreq, fn($a, $b) => $b['jumlah'] <=> $a['jumlah']);
        $topProduk = array_slice($prodFreq, 0, 5);

        arsort($katFreq);
        $topKategori = [];
        foreach (array_slice($katFreq, 0, 5, true) as $katNama => $jml) {
            $topKategori[] = ['kategori_produk' => $katNama, 'jumlah' => $jml];
        }

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
                $res               = $apriori->run(0.2, 0.5);
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
