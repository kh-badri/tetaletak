<?php

namespace App\Controllers;

use App\Models\HistoryModel;
use App\Libraries\AprioriService;

/**
 * Controller untuk menghasilkan Rekomendasi Tata Letak Produk Toko Sembako
 * Menggunakan Algoritma Apriori 2 Parameter (Threshold Support & Threshold Support x Confidence).
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
        $totalTrx   = $this->db->query("SELECT COUNT(DISTINCT no_transaksi) as total_trx FROM dataset_produk")->getRowArray()['total_trx'] ?? 0;
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
     * Memproses algoritma Apriori 2 parameter untuk menghasilkan rekomendasi penataan rak produk.
     */
    public function proses()
    {
        try {
            $thresholdSupport   = (float) ($this->request->getPost('threshold_support') ?? 0.2);
            $thresholdSuppConf  = (float) ($this->request->getPost('threshold_support_confidence') ?? 0.5);

            // Validasi batasan input (skala desimal 0 sampai 1)
            if ($thresholdSupport < 0 || $thresholdSupport > 1) {
                return redirect()->back()->withInput()->with('error', 'Nilai Threshold Support harus antara 0 hingga 1 (contoh: 0.2 atau 0.03).');
            }
            if ($thresholdSuppConf < 0 || $thresholdSuppConf > 1) {
                return redirect()->back()->withInput()->with('error', 'Nilai Threshold Support x Confidence harus antara 0 hingga 1 (contoh: 0.5 atau 0.015).');
            }

            // Eksekusi Algoritma Apriori sesuai Excel Dospem
            $hasil = $this->aprioriService->run($thresholdSupport, $thresholdSuppConf);

            if (($hasil['total_transaksi'] ?? 0) === 0) {
                return redirect()->back()->withInput()->with('error', 'Dataset transaksi masih kosong. Silakan impor CSV transaksi terlebih dahulu.');
            }

            // Simpan hasil ke session agar siap disimpan ke riwayat
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
            $sumber = !empty($hasilTerakhir['rules']) ? $hasilTerakhir['rules'] : ($hasilTerakhir['kombinasi_2'] ?? []);
            $avgSxC = 0;
            if (!empty($sumber)) {
                $totalSxC = array_sum(array_column($sumber, 'support_x_confidence'));
                $avgSxC = round(($totalSxC / count($sumber)) * 100, 2);
            }

            $dataToSave = [
                'tanggal_analisis' => date('Y-m-d H:i:s'),
                'accuracy'         => $avgSxC,
                'total_data'       => $hasilTerakhir['total_transaksi'] ?? 0,
                'hasil_analisis'   => json_encode([
                    'total_produk'        => $hasilTerakhir['total_produk'] ?? 60,
                    'total_lolos'         => $hasilTerakhir['total_lolos'] ?? 0,
                    'is_fallback_ranking' => $hasilTerakhir['is_fallback_ranking'] ?? false,
                    'kombinasi_1'         => $hasilTerakhir['kombinasi_1'] ?? [],
                    'kombinasi_2'         => $hasilTerakhir['kombinasi_2'] ?? [],
                    'kombinasi_3'         => $hasilTerakhir['kombinasi_3'] ?? [],
                    'rules'               => $hasilTerakhir['rules'] ?? [],
                    'kesimpulan_excel'    => $hasilTerakhir['kesimpulan_excel'] ?? [],
                ]),
                'hasil_evaluasi'   => json_encode($hasilTerakhir['rekomendasi_tataletak'] ?? []),
                'hasil_prediksi'   => json_encode([
                    'threshold_support'            => $hasilTerakhir['threshold_support'] ?? 0.2,
                    'threshold_support_confidence' => $hasilTerakhir['threshold_support_confidence'] ?? 0.5,
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
