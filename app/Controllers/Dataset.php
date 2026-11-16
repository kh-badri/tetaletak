<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DatasetModel;
use App\Models\ProdukModel;

/**
 * Controller untuk mengelola Dataset Transaksi Sembako & Master 95 Produk Sembako.
 */
class Dataset extends BaseController
{
    protected $datasetModel;
    protected $produkModel;

    public function __construct()
    {
        $this->datasetModel = new DatasetModel();
        $this->produkModel  = new ProdukModel();
    }

    /**
     * Menampilkan halaman dataset (Tab Master Produk & Tab Transaksi Pembelian).
     */
    public function index()
    {
        // 1. Ambil data transaksi (terbaru)
        $dataset = $this->datasetModel->orderBy('id', 'DESC')->findAll(1000);
        $totalData = $this->datasetModel->countAllResults();

        // 2. Ambil master data produk
        $daftarProduk = $this->produkModel->orderBy('id', 'ASC')->findAll();
        $totalMasterProduk = count($daftarProduk);

        // 3. Statistik transaksi unik
        $db = \Config\Database::connect();
        $trxCountRow = $db->query("SELECT COUNT(DISTINCT no_transaksi) as total_trx, COUNT(DISTINCT nama_produk) as total_produk_dibeli FROM dataset_produk")->getRowArray();

        // Kategori produk unik untuk filter dan dropdown form
        $kategoriList = array_unique(array_column($daftarProduk, 'kategori_produk'));
        sort($kategoriList);

        $activeTab = $this->request->getGet('tab') ?? 'produk';

        $data = [
            'title'               => 'Dataset Sembako & Master 95 Produk | Apriori',
            'dataset'             => $dataset,
            'daftarProduk'        => $daftarProduk,
            'kategoriList'        => $kategoriList,
            'totalData'           => $totalData,
            'totalTrx'            => $trxCountRow['total_trx'] ?? 0,
            'totalProdukDibeli'   => $trxCountRow['total_produk_dibeli'] ?? 0,
            'totalMasterProduk'   => $totalMasterProduk,
            'active_menu'         => 'dataset',
            'active_tab'          => $activeTab
        ];

        return view('dataset/index', $data);
    }

    // =========================================================================
    // FITUR CRUD MASTER PRODUK SEMBAKO (95 PRODUK)
    // =========================================================================

    /**
     * Menyimpan produk baru secara manual ke tabel `produk`.
     */
    public function saveProduk()
    {
        $rules = [
            'nama_produk'     => 'required|min_length[2]|max_length[150]',
            'kategori_produk' => 'required|max_length[100]',
            'harga_satuan'    => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/dataset?tab=produk')->withInput()->with('error', 'Validasi gagal: periksa kembali nama, kategori, dan harga produk.');
        }

        $this->produkModel->save([
            'nama_produk'     => trim($this->request->getPost('nama_produk')),
            'kategori_produk' => trim($this->request->getPost('kategori_produk')),
            'harga_satuan'    => (int) $this->request->getPost('harga_satuan'),
            'satuan'          => trim($this->request->getPost('satuan') ?: 'Pcs')
        ]);

        return redirect()->to('/dataset?tab=produk')->with('success', 'Produk sembako baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui data produk sembako berdasarkan ID.
     */
    public function updateProduk($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/dataset?tab=produk')->with('error', 'ID produk tidak valid.');
        }

        $rules = [
            'nama_produk'     => 'required|min_length[2]|max_length[150]',
            'kategori_produk' => 'required|max_length[100]',
            'harga_satuan'    => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/dataset?tab=produk')->withInput()->with('error', 'Validasi gagal: mohon isi seluruh field dengan benar.');
        }

        $this->produkModel->update($id, [
            'nama_produk'     => trim($this->request->getPost('nama_produk')),
            'kategori_produk' => trim($this->request->getPost('kategori_produk')),
            'harga_satuan'    => (int) $this->request->getPost('harga_satuan'),
            'satuan'          => trim($this->request->getPost('satuan') ?: 'Pcs')
        ]);

        return redirect()->to('/dataset?tab=produk')->with('success', 'Data produk sembako berhasil diperbarui!');
    }

    /**
     * Menghapus produk sembako dari daftar master produk.
     */
    public function deleteProduk($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/dataset?tab=produk')->with('error', 'ID produk tidak valid.');
        }

        $prod = $this->produkModel->find($id);
        if ($prod) {
            $this->produkModel->delete($id);
            return redirect()->to('/dataset?tab=produk')->with('success', 'Produk "' . esc($prod['nama_produk']) . '" berhasil dihapus.');
        }

        return redirect()->to('/dataset?tab=produk')->with('error', 'Produk tidak ditemukan.');
    }

    // =========================================================================
    // FITUR TRANSAKSI PEMBELIAN (DATASET APRIORI)
    // =========================================================================

    /**
     * Menyimpan transaksi pembelian baru secara manual.
     */
    public function save()
    {
        $rules = [
            'waktu_penjualan'     => 'required|valid_date[Y-m-d]',
            'nama_produk'         => 'required|string|max_length[150]',
            'kategori_produk'     => 'required|string|max_length[100]',
            'harga_produk_rp'     => 'required|numeric|integer',
            'jumlah_terjual_unit' => 'required|numeric|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/dataset?tab=transaksi')->withInput()->with('error', 'Validasi gagal, mohon periksa kembali input transaksi.');
        }

        $waktu = $this->request->getPost('waktu_penjualan');
        $noTrx = $this->request->getPost('no_transaksi');
        if (empty($noTrx)) {
            $noTrx = 'TRX-' . date('Ymd', strtotime($waktu)) . '-' . rand(1000, 9999);
        }

        $harga = (int) $this->request->getPost('harga_produk_rp');
        $qty = (int) $this->request->getPost('jumlah_terjual_unit');
        $total = $harga * $qty;

        $this->datasetModel->save([
            'no_transaksi'        => $noTrx,
            'waktu_penjualan'     => $waktu,
            'id_produk'           => (int) $this->request->getPost('id_produk') ?: null,
            'nama_produk'         => $this->request->getPost('nama_produk'),
            'kategori_produk'     => $this->request->getPost('kategori_produk'),
            'harga_produk_rp'     => $harga,
            'jumlah_terjual_unit' => $qty,
            'total_harga'         => $total,
        ]);

        return redirect()->to('/dataset?tab=transaksi')->with('success', 'Data item transaksi berhasil ditambahkan!');
    }

    /**
     * Memproses upload file CSV transaksi sekaligus (Format Comma RFC 4180).
     */
    public function upload()
    {
        $file = $this->request->getFile('dataset_csv');

        if ($file === null || !$file->isValid()) {
            return redirect()->to('/dataset?tab=transaksi')->with('error', 'Upload gagal. Pastikan Anda memilih file CSV yang valid.');
        }

        $filePath = $file->getRealPath();
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->to('/dataset?tab=transaksi')->with('error', 'Gagal membaca file CSV.');
        }

        // Baca baris pertama untuk mendeteksi delimiter
        $firstLine = fgets($handle);
        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            return redirect()->to('/dataset?tab=transaksi')->with('error', 'File CSV kosong.');
        }

        // Hapus UTF-8 BOM jika ada
        $header[0] = preg_replace('/[\x{EF}\x{BB}\x{BF}]/u', '', $header[0]);
        $header = array_map('trim', $header);

        $dataToInsert = [];
        $rowCount = 1;
        $batchSize = 500;
        $totalInserted = 0;

        // Kosongkan data transaksi lama saat import massal baru
        $this->datasetModel->emptyTable();

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowCount++;
            if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                continue;
            }

            if (count($row) >= 8) {
                $noTrx = trim($row[0]);
                $tglRaw = trim($row[1]);
                $idProd = (int) trim($row[2]);
                $namaProd = trim($row[3]);
                $katProd = trim($row[4]);
                $harga = (int) trim($row[5]);
                $qty = (int) trim($row[6]);
                $total = (int) trim($row[7]);
            } elseif (count($row) >= 7) {
                $tglRaw = trim($row[0]);
                $namaProd = trim($row[1]);
                $katProd = trim($row[2]);
                $harga = (int) trim($row[3]);
                $qty = (int) trim($row[4]);
                $total = $harga * $qty;
                $noTrx = 'TRX-' . date('Ymd') . '-' . str_pad($rowCount, 4, '0', STR_PAD_LEFT);
                $idProd = null;
            } else {
                continue;
            }

            $dateObj = \DateTime::createFromFormat('Y-m-d', $tglRaw);
            if ($dateObj === false) {
                $dateObj = \DateTime::createFromFormat('d/m/Y', $tglRaw);
            }
            $tglDb = ($dateObj !== false) ? $dateObj->format('Y-m-d') : date('Y-m-d');

            $dataToInsert[] = [
                'no_transaksi'        => $noTrx,
                'waktu_penjualan'     => $tglDb,
                'id_produk'           => $idProd,
                'nama_produk'         => $namaProd,
                'kategori_produk'     => $katProd,
                'harga_produk_rp'     => $harga,
                'jumlah_terjual_unit' => $qty,
                'total_harga'         => $total,
            ];

            if (count($dataToInsert) >= $batchSize) {
                $this->datasetModel->insertBatch($dataToInsert);
                $totalInserted += count($dataToInsert);
                $dataToInsert = [];
            }
        }

        if (!empty($dataToInsert)) {
            $this->datasetModel->insertBatch($dataToInsert);
            $totalInserted += count($dataToInsert);
        }

        fclose($handle);

        return redirect()->to('/dataset?tab=transaksi')->with('success', "Import massal CSV selesai! {$totalInserted} baris data transaksi sembako berhasil diimpor.");
    }

    /**
     * Menghapus seluruh data transaksi dari tabel.
     */
    public function hapusSemua()
    {
        try {
            $countBefore = $this->datasetModel->countAllResults(false);
            if ($countBefore == 0) {
                return redirect()->to('/dataset?tab=transaksi')->with('info', 'Tidak ada data untuk dihapus.');
            }

            $this->datasetModel->emptyTable();
            return redirect()->to('/dataset?tab=transaksi')->with('success', "Semua data transaksi ({$countBefore} baris) berhasil dihapus.");
        } catch (\Exception $e) {
            log_message('error', 'Error hapusSemua: ' . $e->getMessage());
            return redirect()->to('/dataset?tab=transaksi')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus satu item transaksi berdasarkan ID.
     */
    public function delete($id = null)
    {
        if (!is_numeric($id) || $id === null) {
            return redirect()->to('/dataset?tab=transaksi')->with('error', 'ID data tidak valid.');
        }

        $row = $this->datasetModel->find($id);
        if ($row) {
            $this->datasetModel->delete($id);
            return redirect()->to('/dataset?tab=transaksi')->with('success', 'Data item transaksi berhasil dihapus.');
        }

        return redirect()->to('/dataset?tab=transaksi')->with('error', 'Data tidak ditemukan.');
    }

    /**
     * Export dataset ke file CSV berformat Comma (,).
     */
    public function export()
    {
        $data = $this->datasetModel->findAll();
        $filename = 'dataset_transaksi_sembako_2026_' . date('Ymd_His') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=utf-8");

        $file = fopen('php://output', 'w');
        fputs($file, (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        $header = [
            'no_transaksi',
            'tanggal_transaksi',
            'id_produk',
            'nama_produk',
            'kategori_produk',
            'harga_satuan',
            'jumlah',
            'total_harga'
        ];
        fputcsv($file, $header, ',');

        foreach ($data as $row) {
            $line = [
                $row['no_transaksi'] ?? '',
                $row['waktu_penjualan'],
                $row['id_produk'] ?? '',
                $row['nama_produk'],
                $row['kategori_produk'],
                $row['harga_produk_rp'],
                $row['jumlah_terjual_unit'],
                $row['total_harga'] ?? ($row['harga_produk_rp'] * $row['jumlah_terjual_unit'])
            ];
            fputcsv($file, $line, ',');
        }

        fclose($file);
        exit;
    }
}
