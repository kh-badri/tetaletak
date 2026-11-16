<?php

namespace App\Libraries;

/**
 * Service Algoritma Apriori untuk Analisis Pola Keranjang Belanja (Market Basket Analysis)
 * dan Sistem Rekomendasi Tata Letak Susunan Rak Produk Toko Sembako.
 */
class AprioriService
{
    private float $minSupport;     // desimal (misal 0.02 = 2%)
    private float $minConfidence;  // desimal (misal 0.20 = 20%)
    private float $minLift;        // float (misal 1.0)

    private array $transactions = [];      // array of sets (item IDs / names)
    private array $itemNames = [];         // id => nama_produk
    private array $itemCategories = [];    // id => kategori_produk
    private int $transactionCount = 0;

    /**
     * Menjalankan Algoritma Apriori secara lengkap
     *
     * @param float $minSupport    Dalam persen, contoh: 3.0 (artinya 3%)
     * @param float $minConfidence Dalam persen, contoh: 50.0 (artinya 50%)
     * @param float $minLift       Nilai minimal lift ratio, contoh: 1.2
     * @return array
     */
    public function run(float $minSupport = 3.0, float $minConfidence = 50.0, float $minLift = 1.2): array
    {
        $this->minSupport    = max(0.001, $minSupport / 100);
        $this->minConfidence = max(0.001, $minConfidence / 100);
        $this->minLift       = max(0.0, $minLift);

        $this->_loadData();

        if ($this->transactionCount === 0) {
            throw new \RuntimeException('Tidak ada data transaksi yang ditemukan di tabel dataset_produk.');
        }

        // =====================================================================
        // TAHAP 1: PEMBENTUKAN CALON 1-ITEMSET (C1) & FREQUENT 1-ITEMSET (L1)
        // =====================================================================
        $minSupportCount = (int) ceil($this->minSupport * $this->transactionCount);
        $itemCounts = [];

        foreach ($this->transactions as $trx) {
            foreach ($trx as $item) {
                $itemCounts[$item] = ($itemCounts[$item] ?? 0) + 1;
            }
        }

        $C1_all = [];
        $L1 = [];
        $L1_details = [];

        foreach ($itemCounts as $item => $count) {
            $suppPercent = round(($count / $this->transactionCount) * 100, 2);
            $isLolos = $count >= $minSupportCount;

            $itemInfo = [
                'id_item'          => $item,
                'nama'             => $this->itemNames[$item] ?? "Item #{$item}",
                'kategori'         => $this->itemCategories[$item] ?? 'Sembako',
                'jumlah_transaksi' => $count,
                'support'          => $suppPercent,
                'status'           => $isLolos ? 'Lolos L1' : 'Tereliminasi'
            ];

            $C1_all[] = $itemInfo;

            if ($isLolos) {
                $L1[$item] = $count;
                $L1_details[] = $itemInfo;
            }
        }

        // Urutkan C1 dan L1 berdasarkan jumlah transaksi terbanyak
        usort($C1_all, fn($a, $b) => $b['jumlah_transaksi'] <=> $a['jumlah_transaksi']);
        usort($L1_details, fn($a, $b) => $b['jumlah_transaksi'] <=> $a['jumlah_transaksi']);

        if (empty($L1)) {
            return [
                'status'                 => 'success',
                'total_transaksi'        => $this->transactionCount,
                'min_support'            => $minSupport,
                'min_confidence'         => $minConfidence,
                'min_lift'               => $minLift,
                'min_support_count'      => $minSupportCount,
                'itemsets_1'             => [],
                'itemsets_2'             => [],
                'rules'                  => [],
                'rekomendasi_tataletak'  => [],
                'perhitungan_manual'     => $this->_buildPerhitunganManualEmpty($minSupport, $minConfidence, $minLift, $minSupportCount, $C1_all),
                'pesan'                  => 'Tidak ada produk yang memenuhi Minimum Support. Silakan turunkan nilai Minimum Support.'
            ];
        }

        // =====================================================================
        // TAHAP 2: PEMBENTUKAN CALON 2-ITEMSET (C2) & FREQUENT 2-ITEMSET (L2)
        // =====================================================================
        $frequentItems = array_keys($L1);
        sort($frequentItems);
        $pairCounts = [];

        foreach ($this->transactions as $trx) {
            $filtered = array_values(array_intersect($trx, $frequentItems));
            sort($filtered);
            $cnt = count($filtered);
            if ($cnt < 2) continue;

            for ($i = 0; $i < $cnt; $i++) {
                for ($j = $i + 1; $j < $cnt; $j++) {
                    $pairKey = $filtered[$i] . ',' . $filtered[$j];
                    $pairCounts[$pairKey] = ($pairCounts[$pairKey] ?? 0) + 1;
                }
            }
        }

        $C2_all = [];
        $L2 = [];
        $L2_details = [];

        foreach ($pairCounts as $pairKey => $count) {
            [$iA, $iB] = explode(',', $pairKey);
            $suppPercent = round(($count / $this->transactionCount) * 100, 2);
            $isLolos = $count >= $minSupportCount;

            $pairInfo = [
                'pair_key'         => $pairKey,
                'item_a'           => $this->itemNames[$iA] ?? "Item #{$iA}",
                'item_b'           => $this->itemNames[$iB] ?? "Item #{$iB}",
                'kategori_a'       => $this->itemCategories[$iA] ?? 'Sembako',
                'kategori_b'       => $this->itemCategories[$iB] ?? 'Sembako',
                'jumlah_transaksi' => $count,
                'support'          => $suppPercent,
                'status'           => $isLolos ? 'Lolos L2' : 'Tereliminasi'
            ];

            $C2_all[] = $pairInfo;

            if ($isLolos) {
                $L2[$pairKey] = $count;
                $L2_details[] = $pairInfo;
            }
        }

        usort($C2_all, fn($a, $b) => $b['jumlah_transaksi'] <=> $a['jumlah_transaksi']);
        usort($L2_details, fn($a, $b) => $b['jumlah_transaksi'] <=> $a['jumlah_transaksi']);

        // =====================================================================
        // TAHAP 3: PEMBENTUKAN ATURAN ASOSIASI (ASSOCIATION RULES A => B)
        // =====================================================================
        $rules = [];
        foreach ($L2 as $pairKey => $countAB) {
            [$itemA, $itemB] = explode(',', $pairKey);
            $suppAB = $countAB / $this->transactionCount;

            $countA = $itemCounts[$itemA] ?? 0;
            $countB = $itemCounts[$itemB] ?? 0;

            $suppA = $countA / $this->transactionCount;
            $suppB = $countB / $this->transactionCount;

            // Rule 1: A => B
            if ($suppA > 0) {
                $confAB = $suppAB / $suppA;
                $liftAB = ($suppA * $suppB > 0) ? ($suppAB / ($suppA * $suppB)) : 0;

                if ($confAB >= $this->minConfidence && $liftAB >= $this->minLift) {
                    $rules[] = $this->_buildRule(
                        $itemA, $itemB, $suppAB, $confAB, $liftAB, 
                        $countAB, $countA, $countB, $suppA, $suppB
                    );
                }
            }

            // Rule 2: B => A
            if ($suppB > 0) {
                $confBA = $suppAB / $suppB;
                $liftBA = ($suppA * $suppB > 0) ? ($suppAB / ($suppA * $suppB)) : 0;

                if ($confBA >= $this->minConfidence && $liftBA >= $this->minLift) {
                    $rules[] = $this->_buildRule(
                        $itemB, $itemA, $suppAB, $confBA, $liftBA, 
                        $countAB, $countB, $countA, $suppB, $suppA
                    );
                }
            }
        }

        // Urutkan aturan berdasarkan Lift tertinggi, lalu Confidence, lalu Support
        usort($rules, function ($a, $b) {
            if (abs($a['lift'] - $b['lift']) < 0.0001) {
                if (abs($a['confidence'] - $b['confidence']) < 0.01) {
                    return $b['support'] <=> $a['support'];
                }
                return $b['confidence'] <=> $a['confidence'];
            }
            return $b['lift'] <=> $a['lift'];
        });

        // =====================================================================
        // TAHAP 4: GENERATE REKOMENDASI TATA LETAK RAK (BAHASA AWAM & PRAKTIS)
        // =====================================================================
        $rekomendasiTataLetak = $this->_generateLayoutRecommendations($rules);

        // =====================================================================
        // TAHAP 5: SUSUN DATA PERHITUNGAN MANUAL LENGKAP & STUDI KASUS NYATA
        // =====================================================================
        $perhitunganManual = $this->_buildPerhitunganManual(
            $minSupport, $minConfidence, $minLift, $minSupportCount,
            $C1_all, $L1_details, $C2_all, $L2_details, $rules
        );

        return [
            'status'                 => 'success',
            'total_transaksi'        => $this->transactionCount,
            'total_produk'           => count($this->itemNames),
            'min_support'            => $minSupport,
            'min_confidence'         => $minConfidence,
            'min_lift'               => $minLift,
            'min_support_count'      => $minSupportCount,
            'itemsets_1'             => $L1_details,
            'itemsets_2'             => $L2_details,
            'total_rules'            => count($rules),
            'rules'                  => $rules,
            'rekomendasi_tataletak'  => $rekomendasiTataLetak,
            'perhitungan_manual'     => $perhitunganManual
        ];
    }

    /**
     * Membangun entri aturan asosiasi beserta data frekuensi pendukung
     */
    private function _buildRule(
        $antecedentId, $consequentId, 
        float $suppAB, float $confAB, float $liftAB, 
        int $countAB, int $countA, int $countB, float $suppA, float $suppB
    ): array {
        $nameA = $this->itemNames[$antecedentId] ?? "Item #{$antecedentId}";
        $nameB = $this->itemNames[$consequentId] ?? "Item #{$consequentId}";
        $katA  = $this->itemCategories[$antecedentId] ?? 'Sembako';
        $katB  = $this->itemCategories[$consequentId] ?? 'Sembako';

        if ($liftAB > 3.0) {
            $korelasi = 'Korelasi Sangat Kuat';
            $badgeColor = 'bg-teal-50 text-teal-800 border-teal-200';
            $keeratanAwam = 'Sangat Erat (Paling Sering Dibeli Bareng)';
        } elseif ($liftAB > 1.5) {
            $korelasi = 'Korelasi Kuat';
            $badgeColor = 'bg-sky-50 text-[#0369A1] border-sky-200';
            $keeratanAwam = 'Erat (Sering Dibeli Bareng)';
        } elseif ($liftAB > 1.0) {
            $korelasi = 'Korelasi Positif';
            $badgeColor = 'bg-indigo-50 text-indigo-800 border-indigo-200';
            $keeratanAwam = 'Saling Melengkapi';
        } else {
            $korelasi = 'Korelasi Netral/Negatif';
            $badgeColor = 'bg-slate-100 text-slate-600 border-slate-200';
            $keeratanAwam = 'Biasa (Tidak Terikat)';
        }

        return [
            'antecedent'       => $nameA,
            'consequent'       => $nameB,
            'kategori_a'       => $katA,
            'kategori_b'       => $katB,
            'support'          => round($suppAB * 100, 2),
            'confidence'       => round($confAB * 100, 2),
            'lift'             => round($liftAB, 2),
            'jumlah_transaksi' => $countAB,
            'count_a'          => $countA,
            'count_b'          => $countB,
            'support_a'        => round($suppA * 100, 2),
            'support_b'        => round($suppB * 100, 2),
            'korelasi'         => $korelasi,
            'badge_color'      => $badgeColor,
            'keeratan_awam'    => $keeratanAwam,
            'rule_text'        => "JIKA membeli [{$nameA}], MAKA cenderung membeli [{$nameB}]"
        ];
    }

    /**
     * Menerjemahkan aturan asosiasi Apriori ke Rekomendasi Tata Letak Rak Toko
     * dengan bahasa yang sangat sederhana, praktis, dan mudah dimengerti orang awam / pemilik toko.
     */
    private function _generateLayoutRecommendations(array $rules): array
    {
        $recommendations = [];
        $seenPairs = [];

        foreach ($rules as $rule) {
            $pairKey = strcmp($rule['antecedent'], $rule['consequent']) < 0 
                ? ($rule['antecedent'] . '|' . $rule['consequent'])
                : ($rule['consequent'] . '|' . $rule['antecedent']);

            if (isset($seenPairs[$pairKey])) {
                continue; // Hindari duplikasi penataan bolak-balik
            }
            $seenPairs[$pairKey] = true;

            $katA = $rule['kategori_a'];
            $katB = $rule['kategori_b'];
            $isSameCategory = ($katA === $katB);

            // Bahasa Awam untuk Lokasi Penataan
            if ($isSameCategory) {
                $jenisPenataan = 'Satu Rak Bersanding (Kategori Sama)';
                $posisi = 'Rak Tengah (Setinggi Dada - Pandangan Mata)';
                $saranTataLetak = "Letakkan '{$rule['antecedent']}' tepat di sebelah '{$rule['consequent']}' tanpa pembatas di rak kategori {$katA}. Posisikan di ketinggian 120-140 cm agar langsung terlihat saat pembeli melintas di lorong.";
                $alasanAwam = "Dari setiap 100 orang yang membeli {$rule['antecedent']}, sekitar " . round($rule['confidence']) . " orang pasti sekalian membeli {$rule['consequent']}. Menaruhnya berdampingan membuat pembeli tidak perlu mencari ke rak lain dan langsung mengambil keduanya sekaligus.";
                $tipsToko = "Jajarkan berdampingan secara horizontal. Jika stok menipis, segera isi ulang bersamaan agar rak terlihat penuh dan rapi.";
            } else {
                $jenisPenataan = 'Penataan Silang / Display Gandeng (Lintas Kategori)';
                $posisi = 'Ujung Lorong (Endcap) atau Meja Kasir / Etalase Depan';
                $saranTataLetak = "Meskipun berbeda kategori ({$katA} & {$katB}), letakkan keranjang gantung atau rak susun kecil '{$rule['consequent']}' persis di samping rak '{$rule['antecedent']}', atau tempatkan keduanya di display ujung lorong (dekat kasir).";
                $alasanAwam = "Kedua barang ini dibeli bersamaan {$rule['lift']}x lebih sering dibanding biasanya. Menempatkan {$rule['consequent']} dekat dengan {$rule['antecedent']} memicu pembeli teringat membeli barang pelengkapnya secara spontan (belanja impulsif).";
                $tipsToko = "Gunakan gantungan kawat (clip strip) atau keranjang promo di pinggir rak utama agar tidak memakan banyak tempat.";
            }

            $recommendations[] = [
                'produk_utama'      => $rule['antecedent'],
                'produk_asosiasi'   => $rule['consequent'],
                'kategori_utama'    => $katA,
                'kategori_asosiasi' => $katB,
                'is_same_category'  => $isSameCategory,
                'support'           => $rule['support'],
                'confidence'        => $rule['confidence'],
                'lift'              => $rule['lift'],
                'keeratan_label'    => ($rule['lift'] >= 3.0) ? '🔥 Sangat Sering Dibeli Bareng' : '⭐ Sering Dibeli Bareng',
                'peluang_beli_teks' => round($rule['confidence']) . " dari 100 pembeli membeli bersamaan",
                'lift_efek_teks'    => "Peluang beli bareng {$rule['lift']}x lipat lebih tinggi dari biasanya",
                'jenis_penataan'    => $jenisPenataan,
                'posisi_rak'        => $posisi,
                'saran_tata_letak'  => $saranTataLetak,
                'alasan_bisnis'     => $alasanAwam,
                'tips_toko'         => $tipsToko
            ];

            // Ambil maksimal 15 rekomendasi tata letak terbaik
            if (count($recommendations) >= 15) {
                break;
            }
        }

        return $recommendations;
    }

    /**
     * Membangun data komprehensif untuk Tab Perhitungan Manual & Alur Kerja Apriori
     */
    private function _buildPerhitunganManual(
        float $minSupport, float $minConfidence, float $minLift, int $minSupportCount,
        array $C1, array $L1, array $C2, array $L2, array $rules
    ): array {
        $totalC1 = count($C1);
        $totalL1 = count($L1);
        $totalC1Pruned = $totalC1 - $totalL1;

        $totalKombinasiTeoritis = ($totalL1 > 1) ? (int)(($totalL1 * ($totalL1 - 1)) / 2) : 0;
        $totalC2Ada = count($C2);
        $totalL2 = count($L2);
        $totalC2Pruned = $totalC2Ada - $totalL2;
        $totalRules = count($rules);

        // Ambil aturan terbaik untuk dijadikan contoh studi kasus perhitungan manual
        $contohKasus = null;
        if (!empty($rules)) {
            $topRule = $rules[0];
            $nA = $topRule['count_a'] ?? 1;
            $nB = $topRule['count_b'] ?? 1;
            $nAB = $topRule['jumlah_transaksi'] ?? 1;
            $N = $this->transactionCount;

            $suppA_exact = ($nA / $N) * 100;
            $suppB_exact = ($nB / $N) * 100;
            $suppAB_exact = ($nAB / $N) * 100;
            $conf_exact = ($nAB / $nA) * 100;
            $lift_exact = ($suppA_exact * $suppB_exact > 0) ? ($suppAB_exact / (($suppA_exact / 100) * ($suppB_exact / 100) * 100)) : 0;

            $contohKasus = [
                'item_a'          => $topRule['antecedent'],
                'item_b'          => $topRule['consequent'],
                'kategori_a'      => $topRule['kategori_a'],
                'kategori_b'      => $topRule['kategori_b'],
                'n_total'         => $N,
                'count_a'         => $nA,
                'count_b'         => $nB,
                'count_ab'        => $nAB,
                'min_support_pct' => $minSupport,
                'min_supp_count'  => $minSupportCount,
                'min_conf_pct'    => $minConfidence,
                'min_lift'        => $minLift,
                'supp_a'          => round($suppA_exact, 2),
                'supp_b'          => round($suppB_exact, 2),
                'supp_ab'         => round($suppAB_exact, 2),
                'confidence'      => round($conf_exact, 2),
                'lift'            => round($topRule['lift'], 2),
                'status_supp_a'   => ($nA >= $minSupportCount) ? 'Lolos L1 (>= ' . $minSupport . '%)' : 'Tidak Lolos',
                'status_supp_b'   => ($nB >= $minSupportCount) ? 'Lolos L1 (>= ' . $minSupport . '%)' : 'Tidak Lolos',
                'status_supp_ab'  => ($nAB >= $minSupportCount) ? 'Lolos L2 (>= ' . $minSupport . '%)' : 'Tidak Lolos',
                'status_conf'     => ($conf_exact >= $minConfidence) ? 'Lolos Min Confidence (>= ' . $minConfidence . '%)' : 'Tidak Lolos',
                'status_lift'     => ($topRule['lift'] >= $minLift) ? 'Lolos Min Lift (Korelasi Sangat Kuat)' : 'Tidak Lolos',
            ];
        }

        return [
            'ringkasan' => [
                'total_transaksi'        => $this->transactionCount,
                'min_support_persen'     => $minSupport,
                'min_support_count'      => $minSupportCount,
                'min_confidence_persen'  => $minConfidence,
                'min_lift'               => $minLift,
                'c1_total'               => $totalC1,
                'l1_total'               => $totalL1,
                'c1_tereliminasi'        => $totalC1Pruned,
                'c2_kombinasi_teoritis'  => $totalKombinasiTeoritis,
                'c2_total_ada'           => $totalC2Ada,
                'l2_total'               => $totalL2,
                'c2_tereliminasi'        => $totalC2Pruned,
                'total_rules'            => $totalRules
            ],
            'contoh_kasus' => $contohKasus,
            'c1_lengkap'   => $C1,
            'c2_lengkap'   => array_slice($C2, 0, 100) // Ambil hingga 100 pasangan teratas untuk tabel
        ];
    }

    /**
     * Membangun placeholder perhitungan manual jika tidak ada data yang lolos
     */
    private function _buildPerhitunganManualEmpty(
        float $minSupport, float $minConfidence, float $minLift, int $minSupportCount, array $C1
    ): array {
        return [
            'ringkasan' => [
                'total_transaksi'        => $this->transactionCount,
                'min_support_persen'     => $minSupport,
                'min_support_count'      => $minSupportCount,
                'min_confidence_persen'  => $minConfidence,
                'min_lift'               => $minLift,
                'c1_total'               => count($C1),
                'l1_total'               => 0,
                'c1_tereliminasi'        => count($C1),
                'c2_kombinasi_teoritis'  => 0,
                'c2_total_ada'           => 0,
                'l2_total'               => 0,
                'c2_tereliminasi'        => 0,
                'total_rules'            => 0
            ],
            'contoh_kasus' => null,
            'c1_lengkap'   => $C1,
            'c2_lengkap'   => []
        ];
    }

    /**
     * Memuat data transaksi dari database
     */
    private function _loadData(): void
    {
        $db = \Config\Database::connect();

        // 1. Ambil nama & kategori produk dari master produk atau dataset
        $produkRows = $db->table('produk')->select('id, nama_produk, kategori_produk')->get()->getResultArray();
        if (!empty($produkRows)) {
            foreach ($produkRows as $p) {
                $this->itemNames[$p['id']] = $p['nama_produk'];
                $this->itemCategories[$p['id']] = $p['kategori_produk'];
            }
        }

        // 2. Ambil transaksi dari `dataset_produk`
        $query = $db->table('dataset_produk')
            ->select('no_transaksi, id_produk, nama_produk, kategori_produk')
            ->orderBy('no_transaksi')
            ->get();

        $rows = $query->getResultArray();

        $tempTrx = [];
        foreach ($rows as $row) {
            $trxCode = trim($row['no_transaksi']);
            if (empty($trxCode)) continue;

            $itemId = !empty($row['id_produk']) ? (int)$row['id_produk'] : $row['nama_produk'];

            if (!isset($this->itemNames[$itemId])) {
                $this->itemNames[$itemId] = $row['nama_produk'];
                $this->itemCategories[$itemId] = $row['kategori_produk'];
            }

            if (!isset($tempTrx[$trxCode])) {
                $tempTrx[$trxCode] = [];
            }

            if (!in_array($itemId, $tempTrx[$trxCode])) {
                $tempTrx[$trxCode][] = $itemId;
            }
        }

        $this->transactions = array_values($tempTrx);
        $this->transactionCount = count($this->transactions);
    }
}
