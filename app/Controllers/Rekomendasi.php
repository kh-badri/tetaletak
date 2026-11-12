<?php

namespace App\Controllers;

use App\Models\HistoryModel;
use App\Libraries\AprioriService;

/**
 * Controller untuk menghasilkan Rekomendasi Tata Letak Produk Toko Sembako
 * Menggunakan Algoritma Apriori (Market Basket Analysis).
 */
class Rekomendasi extends BaseController
{
    protected $session;
    protected $db;
    protected $historyModel;
    protected $aprioriService;

    public function __construct()
    {
        $this->session        = session();
        $this->db             = \Config\Database::connect();
        $this->historyModel   = new HistoryModel();
        $this->aprioriService = new AprioriService();
    }

    /**
     * Halaman form parameter rekomendasi tata letak produk (Apriori).
     */
    public function index()
    {
        $totalTrx = $this->db->query("SELECT COUNT(DISTINCT no_transaksi) as total_trx FROM dataset_produk")->getRowArray()['total_trx'] ?? 0;
        $totalBaris = $this->db->table('dataset_produk')->countAllResults();

        $data = [
            'title'       => 'Rekomendasi Tata Letak Produk (Algoritma Apriori)',
            'error'       => $this->session->getFlashdata('error'),
            'totalTrx'    => $totalTrx,
            'totalBaris'  => $totalBaris,
            'active_menu' => 'rekomendasi'
        ];

        return view('rekomendasi/index', $data);
    }

    /**
     * Memproses algoritma Apriori untuk menghasilkan rekomendasi penataan rak produk.
     */
    public function proses()
    {
        try {
            $minSupport    = (float) ($this->request->getPost('min_support') ?? 2.0);
            $minConfidence = (float) ($this->request->getPost('min_confidence') ?? 20.0);
            $minLift       = (float) ($this->request->getPost('min_lift') ?? 1.0);

            // Validasi batasan input
            if ($minSupport <= 0 || $minSupport > 100) {
                return redirect()->back()->withInput()->with('error', 'Nilai Minimum Support harus antara 0.1% hingga 100%.');
            }
            if ($minConfidence <= 0 || $minConfidence > 100) {
                return redirect()->back()->withInput()->with('error', 'Nilai Minimum Confidence harus antara 0.1% hingga 100%.');
            }
            if ($minLift < 0) {
                return redirect()->back()->withInput()->with('error', 'Nilai Minimum Lift Ratio minimal 0.0.');
            }

            // Eksekusi Algoritma Apriori murni
            $hasil = $this->aprioriService->run($minSupport, $minConfidence, $minLift);

            if (empty($hasil['rules']) && empty($hasil['itemsets_1'])) {
                return redirect()->back()->withInput()->with('error', 'Tidak ditemukan pola kombinasi produk dengan parameter tersebut. Cobalah turunkan nilai Minimum Support (misal: 1.5% atau 2%) atau Minimum Confidence (misal: 20%).');
            }

            // Simpan hasil ke session agar siap disimpan ke riwayat jika diperlukan
            $this->session->set('hasil_rekomendasi_terakhir', $hasil);

            return view('rekomendasi/hasil', [
                'title'       => 'Hasil Rekomendasi Tata Letak Produk (Apriori)',
                'hasil'       => $hasil,
                'active_menu' => 'rekomendasi'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error Rekomendasi Apriori: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memproses rekomendasi: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan hasil rekomendasi ke database riwayat (History).
     */
    public function simpan()
    {
        $hasilTerakhir = $this->session->get('hasil_rekomendasi_terakhir');

        if (empty($hasilTerakhir)) {
            return redirect()->to('rekomendasi')->with('error', 'Tidak ada hasil rekomendasi untuk disimpan.');
        }

        try {
            // Hitung rata-rata confidence untuk metrik kualitas asosiasi
            $avgConfidence = 0;
            if (!empty($hasilTerakhir['rules'])) {
                $totalConf = array_sum(array_column($hasilTerakhir['rules'], 'confidence'));
                $avgConfidence = round($totalConf / count($hasilTerakhir['rules']), 2);
            }

            $dataToSave = [
                'tanggal_analisis' => date('Y-m-d H:i:s'),
                'accuracy'         => $avgConfidence,
                'total_data'       => $hasilTerakhir['total_transaksi'] ?? 0,
                'hasil_analisis'   => json_encode([
                    'frequent_itemsets_1' => $hasilTerakhir['itemsets_1'] ?? [],
                    'frequent_itemsets_2' => $hasilTerakhir['itemsets_2'] ?? [],
                    'total_rules'         => $hasilTerakhir['total_rules'] ?? 0,
                    'rules'               => $hasilTerakhir['rules'] ?? [],
                    'perhitungan_manual'  => $hasilTerakhir['perhitungan_manual'] ?? []
                ]),
                'hasil_evaluasi'   => json_encode($hasilTerakhir['rekomendasi_tataletak'] ?? []),
                'hasil_prediksi'   => json_encode([
                    'min_support'       => $hasilTerakhir['min_support'],
                    'min_confidence'    => $hasilTerakhir['min_confidence'],
                    'min_lift'          => $hasilTerakhir['min_lift'],
                    'min_support_count' => $hasilTerakhir['min_support_count'] ?? 0
                ])
            ];

            $this->historyModel->save($dataToSave);
            $this->session->remove('hasil_rekomendasi_terakhir');

            return redirect()->to('history')->with('success', 'Hasil rekomendasi tata letak berhasil disimpan ke riwayat.');
        } catch (\Exception $e) {
            log_message('error', 'Gagal simpan history: ' . $e->getMessage());
            return redirect()->to('rekomendasi')->with('error', 'Terjadi kesalahan saat menyimpan ke riwayat: ' . $e->getMessage());
        }
    }
}

