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
            $analisisData    = json_decode($row['hasil_analisis'] ?? '{}', true) ?: [];
            $rekomendasiData = json_decode($row['hasil_evaluasi'] ?? '[]', true) ?: [];
            $params          = json_decode($row['hasil_prediksi'] ?? '{}', true) ?: [];

            $historyList[] = [
                'id'                           => $row['id'],
                'tanggal_analisis'             => $row['tanggal_analisis'],
                'accuracy'                     => $row['accuracy'],
                'total_data'                   => $row['total_data'],
                'total_rules'                  => $analisisData['total_lolos'] ?? count($analisisData['rules'] ?? []),
                'total_rekomendasi'            => count($rekomendasiData),
                'threshold_support'            => $params['threshold_support'] ?? ($params['min_support'] ?? 0.2),
                'threshold_support_confidence' => $params['threshold_support_confidence'] ?? ($params['min_confidence'] ?? 0.5),
                'contoh_rekomendasi'           => !empty($rekomendasiData[0]) ? ($rekomendasiData[0]['produk_utama'] . ' ↔ ' . $rekomendasiData[0]['produk_asosiasi']) : 'Rekomendasi Rak Sembako'
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

        $analisisData    = json_decode($item['hasil_analisis'] ?? '{}', true) ?: [];
        $rekomendasiData = json_decode($item['hasil_evaluasi'] ?? '[]', true) ?: [];
        $params          = json_decode($item['hasil_prediksi'] ?? '{}', true) ?: [];

        $hasil = [
            'status'                       => 'success',
            'total_transaksi'              => $item['total_data'],
            'total_produk'                 => $analisisData['total_produk'] ?? 60,
            'threshold_support'            => $params['threshold_support'] ?? ($params['min_support'] ?? 0.2),
            'threshold_support_confidence' => $params['threshold_support_confidence'] ?? ($params['min_confidence'] ?? 0.5),
            'is_fallback_ranking'          => $analisisData['is_fallback_ranking'] ?? false,
            'total_lolos'                  => $analisisData['total_lolos'] ?? count($analisisData['rules'] ?? []),
            'kombinasi_1'                  => $analisisData['kombinasi_1'] ?? [],
            'kombinasi_2'                  => $analisisData['kombinasi_2'] ?? ($analisisData['rules'] ?? []),
            'kombinasi_3'                  => $analisisData['kombinasi_3'] ?? [],
            'rules'                        => $analisisData['rules'] ?? [],
            'rekomendasi_tataletak'        => $rekomendasiData,
            'kesimpulan_excel'             => $analisisData['kesimpulan_excel'] ?? []
        ];

        $data = [
            'title'            => 'Detail Riwayat Tata Letak #' . $item['id'] . ' | Apriori',
            'hasil'            => $hasil,
            'tanggal_analisis' => $item['tanggal_analisis'],
            'active_menu'      => 'history'
        ];

        return view('history/detail', $data);
    }
}
