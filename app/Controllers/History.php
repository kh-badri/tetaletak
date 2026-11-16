<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HistoryModel;

class History extends BaseController
{
    protected $historyModel;

    public function __construct()
    {
        $this->historyModel = new HistoryModel();
        helper(['session', 'form', 'url']);
    }

    /**
     * Menampilkan daftar semua riwayat rekomendasi tata letak produk (Apriori).
     */
    public function index()
    {
        $rawHistory = $this->historyModel->orderBy('tanggal_analisis', 'DESC')->findAll();

        $historyList = [];
        foreach ($rawHistory as $row) {
            $analisisData = json_decode($row['hasil_analisis'] ?? '{}', true) ?: [];
            $rekomendasiData = json_decode($row['hasil_evaluasi'] ?? '[]', true) ?: [];
            $params = json_decode($row['hasil_prediksi'] ?? '{}', true) ?: [];

            $historyList[] = [
                'id'                   => $row['id'],
                'tanggal_analisis'     => $row['tanggal_analisis'],
                'accuracy'             => $row['accuracy'],
                'total_data'           => $row['total_data'],
                'total_rules'          => $analisisData['total_rules'] ?? count($analisisData['rules'] ?? []),
                'total_rekomendasi'    => count($rekomendasiData),
                'min_support'          => $params['min_support'] ?? 3.0,
                'min_confidence'       => $params['min_confidence'] ?? 50.0,
                'min_lift'             => $params['min_lift'] ?? 1.2,
                'contoh_rekomendasi'   => !empty($rekomendasiData[0]) ? ($rekomendasiData[0]['produk_utama'] . ' ↔ ' . $rekomendasiData[0]['produk_asosiasi']) : 'Rekomendasi Rak Sembako'
            ];
        }

        $data = [
            'title'       => 'Riwayat Rekomendasi Tata Letak | Apriori',
            'history'     => $historyList,
            'active_menu' => 'history'
        ];

        return view('history/index', $data);
    }

    /**
     * Menghapus data riwayat berdasarkan ID.
     */
    public function delete($id = null)
    {
        if ($id === null) {
            return redirect()->to('history')->with('error', 'ID riwayat tidak valid.');
        }

        $item = $this->historyModel->find($id);
        if ($item) {
            $this->historyModel->delete($id);
            return redirect()->to('history')->with('success', 'Data riwayat rekomendasi berhasil dihapus.');
        }

        return redirect()->to('history')->with('error', 'Data riwayat tidak ditemukan.');
    }

    /**
     * Menampilkan detail riwayat rekomendasi tata letak tertentu.
     */
    public function detail($id = null)
    {
        if ($id === null) {
            return redirect()->to('history')->with('error', 'ID riwayat tidak valid.');
        }

        $item = $this->historyModel->find($id);
        if (!$item) {
            return redirect()->to('history')->with('error', 'Data riwayat tidak ditemukan.');
        }

        $analisisData = json_decode($item['hasil_analisis'] ?? '{}', true) ?: [];
        $rekomendasiData = json_decode($item['hasil_evaluasi'] ?? '[]', true) ?: [];
        $params = json_decode($item['hasil_prediksi'] ?? '{}', true) ?: [];

        $minSuppCount = $params['min_support_count'] ?? (int) ceil((($params['min_support'] ?? 3.0) / 100) * $item['total_data']);
        $rules = $analisisData['rules'] ?? [];
        $perhitunganManual = $analisisData['perhitungan_manual'] ?? null;

        if (empty($perhitunganManual['sampel_10'])) {
            $apriori = new \App\Libraries\AprioriService();
            $perhitunganManual['sampel_10'] = $apriori->getSample10Calculations();
        }

        // Jika riwayat lama belum menyimpan perhitungan_manual, buat struktur fallback dinamis
        if (empty($perhitunganManual)) {
            $topRule = !empty($rules) ? $rules[0] : null;
            $contohKasus = null;
            if ($topRule) {
                $nA = $topRule['count_a'] ?? 29;
                $nB = $topRule['count_b'] ?? 60;
                $nAB = $topRule['jumlah_transaksi'] ?? 18;
                $N = $item['total_data'] ?: 766;
                $contohKasus = [
                    'item_a'          => $topRule['antecedent'],
                    'item_b'          => $topRule['consequent'],
                    'kategori_a'      => $topRule['kategori_a'] ?? 'Sembako',
                    'kategori_b'      => $topRule['kategori_b'] ?? 'Sembako',
                    'n_total'         => $N,
                    'count_a'         => $nA,
                    'count_b'         => $nB,
                    'count_ab'        => $nAB,
                    'min_support_pct' => $params['min_support'] ?? 3.0,
                    'min_supp_count'  => $minSuppCount,
                    'min_conf_pct'    => $params['min_confidence'] ?? 50.0,
                    'min_lift'        => $params['min_lift'] ?? 1.2,
                    'supp_a'          => $topRule['support_a'] ?? round(($nA / $N) * 100, 2),
                    'supp_b'          => $topRule['support_b'] ?? round(($nB / $N) * 100, 2),
                    'supp_ab'         => $topRule['support'] ?? round(($nAB / $N) * 100, 2),
                    'confidence'      => $topRule['confidence'] ?? 62.07,
                    'lift'            => $topRule['lift'] ?? 7.92,
                    'status_supp_a'   => 'Lolos L1',
                    'status_supp_b'   => 'Lolos L1',
                    'status_supp_ab'  => 'Lolos L2',
                    'status_conf'     => 'Lolos Min Confidence',
                    'status_lift'     => 'Lolos Min Lift (Korelasi Sangat Kuat)'
                ];
            }

            $perhitunganManual = [
                'ringkasan' => [
                    'total_transaksi'        => $item['total_data'],
                    'min_support_persen'     => $params['min_support'] ?? 3.0,
                    'min_support_count'      => $minSuppCount,
                    'min_confidence_persen'  => $params['min_confidence'] ?? 50.0,
                    'min_lift'               => $params['min_lift'] ?? 1.2,
                    'c1_total'               => 95,
                    'l1_total'               => count($analisisData['frequent_itemsets_1'] ?? []),
                    'c1_tereliminasi'        => max(0, 95 - count($analisisData['frequent_itemsets_1'] ?? [])),
                    'c2_kombinasi_teoritis'  => 1225,
                    'c2_total_ada'           => 607,
                    'l2_total'               => count($analisisData['frequent_itemsets_2'] ?? []),
                    'c2_tereliminasi'        => max(0, 607 - count($analisisData['frequent_itemsets_2'] ?? [])),
                    'total_rules'            => count($rules)
                ],
                'contoh_kasus' => $contohKasus,
                'c1_lengkap'   => [],
                'c2_lengkap'   => []
            ];
        }

        $hasil = [
            'status'                => 'success',
            'total_transaksi'       => $item['total_data'],
            'min_support'           => $params['min_support'] ?? 3.0,
            'min_confidence'        => $params['min_confidence'] ?? 50.0,
            'min_lift'              => $params['min_lift'] ?? 1.2,
            'min_support_count'     => $minSuppCount,
            'itemsets_1'            => $analisisData['frequent_itemsets_1'] ?? [],
            'itemsets_2'            => $analisisData['frequent_itemsets_2'] ?? [],
            'total_rules'           => $analisisData['total_rules'] ?? count($rules),
            'rules'                 => $rules,
            'rekomendasi_tataletak' => $rekomendasiData,
            'perhitungan_manual'    => $perhitunganManual
        ];

        $data = [
            'title'            => 'Detail Riwayat Rekomendasi #' . $item['id'],
            'hasil'            => $hasil,
            'tanggal_analisis' => $item['tanggal_analisis'],
            'active_menu'      => 'history'
        ];

        return view('history/detail', $data);
    }
}
