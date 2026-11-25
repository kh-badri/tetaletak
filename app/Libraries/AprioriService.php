<?php

namespace App\Libraries;

/**
 * Layanan Algoritma Apriori (Market Basket Analysis)
 * Disesuaikan dengan metode perhitungan 2 parameter pada Excel:
 * 1. Threshold Support
 * 2. Threshold Support x Confidence
 * (Tanpa Lift Ratio)
 */
class AprioriService
{
    private array $transactions = [];
    private array $itemNames = [];
    private array $itemCategories = [];
    private int $transactionCount = 0;

    /**
     * Menjalankan Algoritma Apriori 2 Parameter (Threshold Support & Threshold Support x Confidence).
     *
     * @param float $thresholdSupport           Skala desimal 0 - 1 (default 0.2)
     * @param float $thresholdSupportConfidence Skala desimal 0 - 1 (default 0.5)
     * @return array
     */
    public function run(float $thresholdSupport = 0.2, float $thresholdSupportConfidence = 0.5): array
    {
        $this->_loadData();

        if ($this->transactionCount === 0) {
            return [
                'status'                       => 'error',
                'message'                      => 'Data transaksi kosong.',
                'total_transaksi'              => 0,
                'threshold_support'            => $thresholdSupport,
                'threshold_support_confidence' => $thresholdSupportConfidence,
                'kombinasi_1'                  => [],
                'kombinasi_2'                  => [],
                'kombinasi_3'                  => [],
                'rules'                        => [],
                'rekomendasi_tataletak'        => [],
                'kesimpulan_excel'             => []
            ];
        }

        $N = $this->transactionCount;

        // ---------------------------------------------------------------------
        // 1. HITUNG FREKUENSI KEMUNCULAN TIAP PRODUK (BINER PER TRANSAKSI)
        // ---------------------------------------------------------------------
        $itemCounts = [];
        foreach ($this->transactions as $trx) {
            foreach ($trx as $itemId) {
                $itemCounts[$itemId] = ($itemCounts[$itemId] ?? 0) + 1;
            }
        }
        arsort($itemCounts);

        // ---------------------------------------------------------------------
        // 2. KOMBINASI 1 PRODUK (If Buy A Then Buy A)
        // ---------------------------------------------------------------------
        $kombinasi1 = [];
        foreach ($itemCounts as $itemId => $cnt) {
            $nama = $this->itemNames[$itemId] ?? (string) $itemId;
            $supp = $cnt / $N;
            $conf = 1.0; // count(A) / count(A)
            $sxc  = $supp * $conf;

            $lolosSupp = ($supp >= $thresholdSupport);
            $lolosSxc  = ($sxc >= $thresholdSupportConfidence);

            $kombinasi1[] = [
                'id_produk'            => $itemId,
                'nama_produk'          => $nama,
                'kategori'             => $this->itemCategories[$itemId] ?? 'Sembako',
                'aturan'               => "If Buy {$nama} Then Buy {$nama}",
                'jumlah_transaksi'     => $cnt,
                'total_transaksi'      => $N,
                'support'              => round($supp, 4),
                'confidence'           => round($conf, 4),
                'support_x_confidence' => round($sxc, 4),
                'memenuhi_support'     => $lolosSupp ? 'Ya' : 'Tidak',
                'memenuhi_sxc'         => $lolosSxc ? 'Ya' : 'Tidak',
                'lolos'                => ($lolosSupp && $lolosSxc)
            ];
        }

        // Urutkan Kombinasi 1 berdasarkan Support x Confidence terbesar
        usort($kombinasi1, function ($a, $b) {
            if ($b['support_x_confidence'] !== $a['support_x_confidence']) {
                return $b['support_x_confidence'] <=> $a['support_x_confidence'];
            }
            return $b['support'] <=> $a['support'];
        });

        // ---------------------------------------------------------------------
        // 3. KOMBINASI 2 PRODUK (If Buy A Then Buy B)
        // Rumus Excel:
        // - Support = count(A & B) / N
        // - Confidence = count(A & B) / count(THEN)
        // - Support x Confidence = Support * Confidence
        // ---------------------------------------------------------------------
        $pairCounts = [];
        foreach ($this->transactions as $trx) {
            $items = array_values($trx);
            $len = count($items);
            if ($len < 2) continue;
            sort($items);
            for ($i = 0; $i < $len; $i++) {
                for ($j = $i + 1; $j < $len; $j++) {
                    $key = $items[$i] . '|' . $items[$j];
                    $pairCounts[$key] = ($pairCounts[$key] ?? 0) + 1;
                }
            }
        }

        $kombinasi2 = [];
        $rulesLolos = [];
        foreach ($pairCounts as $key => $cntAB) {
            [$idA, $idB] = explode('|', $key);
            $namaA = $this->itemNames[$idA] ?? (string) $idA;
            $namaB = $this->itemNames[$idB] ?? (string) $idB;
            $katA  = $this->itemCategories[$idA] ?? 'Sembako';
            $katB  = $this->itemCategories[$idB] ?? 'Sembako';

            $cntA = max(1, $itemCounts[$idA] ?? 1);
            $cntB = max(1, $itemCounts[$idB] ?? 1);
            $supp = $cntAB / $N;

            // A -> B (Confidence = count(A&B) / count(B), sesuai rumus Excel)
            $confAB = $cntAB / $cntB;
            $sxcAB  = $supp * $confAB;
            $lolosSuppAB = ($supp >= $thresholdSupport);
            $lolosSxcAB  = ($sxcAB >= $thresholdSupportConfidence);

            $ruleAB = [
                'id_a'                 => $idA,
                'id_b'                 => $idB,
                'antecedent'           => $namaA,
                'consequent'           => $namaB,
                'kategori_a'           => $katA,
                'kategori_b'           => $katB,
                'aturan'               => "If Buy {$namaA} Then Buy {$namaB}",
                'jumlah_transaksi'     => $cntAB,
                'total_transaksi'      => $N,
                'count_if'             => $cntA,
                'count_then'           => $cntB,
                'support'              => round($supp, 4),
                'confidence'           => round($confAB, 4),
                'support_x_confidence' => round($sxcAB, 4),
                'memenuhi_support'     => $lolosSuppAB ? 'Ya' : 'Tidak',
                'memenuhi_sxc'         => $lolosSxcAB ? 'Ya' : 'Tidak',
                'lolos'                => ($lolosSuppAB && $lolosSxcAB)
            ];
            $kombinasi2[] = $ruleAB;
            if ($ruleAB['lolos']) {
                $rulesLolos[] = $ruleAB;
            }

            // B -> A (Confidence = count(A&B) / count(A), sesuai rumus Excel)
            $confBA = $cntAB / $cntA;
            $sxcBA  = $supp * $confBA;
            $lolosSuppBA = ($supp >= $thresholdSupport);
            $lolosSxcBA  = ($sxcBA >= $thresholdSupportConfidence);

            $ruleBA = [
                'id_a'                 => $idB,
                'id_b'                 => $idA,
                'antecedent'           => $namaB,
                'consequent'           => $namaA,
                'kategori_a'           => $katB,
                'kategori_b'           => $katA,
                'aturan'               => "If Buy {$namaB} Then Buy {$namaA}",
                'jumlah_transaksi'     => $cntAB,
                'total_transaksi'      => $N,
                'count_if'             => $cntB,
                'count_then'           => $cntA,
                'support'              => round($supp, 4),
                'confidence'           => round($confBA, 4),
                'support_x_confidence' => round($sxcBA, 4),
                'memenuhi_support'     => $lolosSuppBA ? 'Ya' : 'Tidak',
                'memenuhi_sxc'         => $lolosSxcBA ? 'Ya' : 'Tidak',
                'lolos'                => ($lolosSuppBA && $lolosSxcBA)
            ];
            $kombinasi2[] = $ruleBA;
            if ($ruleBA['lolos']) {
                $rulesLolos[] = $ruleBA;
            }
        }

        // Urutkan Kombinasi 2 berdasarkan Support x Confidence terbesar (Ranking Excel)
        $sortBySxC = function ($a, $b) {
            if ($b['support_x_confidence'] !== $a['support_x_confidence']) {
                return $b['support_x_confidence'] <=> $a['support_x_confidence'];
            }
            if ($b['support'] !== $a['support']) {
                return $b['support'] <=> $a['support'];
            }
            return $b['jumlah_transaksi'] <=> $a['jumlah_transaksi'];
        };
        usort($kombinasi2, $sortBySxC);
        usort($rulesLolos, $sortBySxC);

        // ---------------------------------------------------------------------
        // 4. KOMBINASI 3 PRODUK (If Buy A And Buy B Then Buy C)
        // ---------------------------------------------------------------------
        $tripleCounts = [];
        foreach ($this->transactions as $trx) {
            $items = array_values($trx);
            $len = count($items);
            if ($len < 3) continue;
            sort($items);
            for ($i = 0; $i < $len; $i++) {
                for ($j = $i + 1; $j < $len; $j++) {
                    for ($k = $j + 1; $k < $len; $k++) {
                        $key = $items[$i] . '|' . $items[$j] . '|' . $items[$k];
                        $tripleCounts[$key] = ($tripleCounts[$key] ?? 0) + 1;
                    }
                }
            }
        }

        // Ambil 3-itemset teratas saja agar ringan
        arsort($tripleCounts);
        $topTripleCounts = array_slice($tripleCounts, 0, 25, true);

        $kombinasi3 = [];
        foreach ($topTripleCounts as $key => $cntABC) {
            [$id1, $id2, $id3] = explode('|', $key);
            $n1 = $this->itemNames[$id1] ?? (string) $id1;
            $n2 = $this->itemNames[$id2] ?? (string) $id2;
            $n3 = $this->itemNames[$id3] ?? (string) $id3;
            $supp = $cntABC / $N;

            $perms = [
                [$n1 . ' & ' . $n2, $n3, max(1, $itemCounts[$id3] ?? 1)],
                [$n1 . ' & ' . $n3, $n2, max(1, $itemCounts[$id2] ?? 1)],
                [$n2 . ' & ' . $n3, $n1, max(1, $itemCounts[$id1] ?? 1)],
            ];

            foreach ($perms as [$ifStr, $thenStr, $cntThen]) {
                $conf = $cntABC / $cntThen;
                $sxc  = $supp * $conf;
                $lolosSupp = ($supp >= $thresholdSupport);
                $lolosSxc  = ($sxc >= $thresholdSupportConfidence);

                $kombinasi3[] = [
                    'antecedent'           => $ifStr,
                    'consequent'           => $thenStr,
                    'aturan'               => "If Buy {$ifStr} Then Buy {$thenStr}",
                    'jumlah_transaksi'     => $cntABC,
                    'total_transaksi'      => $N,
                    'count_then'           => $cntThen,
                    'support'              => round($supp, 4),
                    'confidence'           => round($conf, 4),
                    'support_x_confidence' => round($sxc, 4),
                    'memenuhi_support'     => $lolosSupp ? 'Ya' : 'Tidak',
                    'memenuhi_sxc'         => $lolosSxc ? 'Ya' : 'Tidak',
                    'lolos'                => ($lolosSupp && $lolosSxc)
                ];
            }
        }
        usort($kombinasi3, $sortBySxC);
        $kombinasi3 = array_slice($kombinasi3, 0, 30);

        // ---------------------------------------------------------------------
        // 5. REKOMENDASI TATA LETAK (SEDERHANA & SESUAI KESIMPULAN EXCEL)
        // ---------------------------------------------------------------------
        $isFallback = empty($rulesLolos);
        $sumberRek  = $isFallback ? array_slice($kombinasi2, 0, 15) : array_slice($rulesLolos, 0, 15);
        $rekomendasiTataLetak = $this->_generateRecommendations($sumberRek);

        // Kesimpulan Excel: Support Terbesar & Support x Confidence Terbesar
        $top1Supp  = $kombinasi1[0] ?? null;
        $top2Supp  = $kombinasi2[0] ?? null;
        if (!empty($kombinasi2)) {
            $sortedBySupp = $kombinasi2;
            usort($sortedBySupp, fn($a, $b) => $b['support'] <=> $a['support']);
            $top2Supp = $sortedBySupp[0];
        }
        $top2SxC = $kombinasi2[0] ?? null;

        $kesimpulanExcel = [
            'support_1_produk' => $top1Supp ? [
                'produk' => $top1Supp['nama_produk'],
                'nilai'  => $top1Supp['support'],
                'teks'   => "Jika 1 produk diletakkan dalam satu etalase/rak, produk paling laku adalah {$top1Supp['nama_produk']} (Support {$top1Supp['support']}). Produk sejenis yang kurang laku dapat diletakkan satu rak dengannya."
            ] : null,
            'support_2_produk' => $top2Supp ? [
                'produk_a' => $top2Supp['antecedent'],
                'produk_b' => $top2Supp['consequent'],
                'nilai'    => $top2Supp['support'],
                'teks'     => "Pasangan dengan Support terbesar adalah {$top2Supp['antecedent']} dan {$top2Supp['consequent']} (Support {$top2Supp['support']}). Keduanya disarankan diletakkan dalam satu etalase/rak yang sama."
            ] : null,
            'sxc_2_produk' => $top2SxC ? [
                'produk_a' => $top2SxC['antecedent'],
                'produk_b' => $top2SxC['consequent'],
                'nilai'    => $top2SxC['support_x_confidence'],
                'teks'     => "Nilai (Support x Confidence) terbesar adalah aturan \"If Buy {$top2SxC['antecedent']} Then Buy {$top2SxC['consequent']}\" sebesar {$top2SxC['support_x_confidence']}. Letakkan {$top2SxC['consequent']} bersebelahan dengan {$top2SxC['antecedent']}."
            ] : null,
        ];

        return [
            'status'                       => 'success',
            'total_transaksi'              => $N,
            'total_produk'                 => count($itemCounts),
            'threshold_support'            => $thresholdSupport,
            'threshold_support_confidence' => $thresholdSupportConfidence,
            'is_fallback_ranking'          => $isFallback,
            'total_lolos'                  => count($rulesLolos),
            'kombinasi_1'                  => $kombinasi1,
            'kombinasi_2'                  => array_slice($kombinasi2, 0, 150),
            'kombinasi_3'                  => $kombinasi3,
            'rules'                        => $rulesLolos,
            'rekomendasi_tataletak'        => $rekomendasiTataLetak,
            'kesimpulan_excel'             => $kesimpulanExcel,
        ];
    }

    /**
     * Membangun daftar rekomendasi tata letak produk yang sederhana dan mudah dipahami.
     */
    private function _generateRecommendations(array $rules): array
    {
        $recommendations = [];
        $seenPairs = [];

        foreach ($rules as $rule) {
            $a = $rule['antecedent'];
            $b = $rule['consequent'];

            // Hindari duplikasi bolak-balik (A->B & B->A) di kartu rekomendasi, ambil yang ranking tertingginya
            $pairKey = ($a < $b) ? "{$a}|{$b}" : "{$b}|{$a}";
            if (isset($seenPairs[$pairKey])) {
                continue;
            }
            $seenPairs[$pairKey] = true;

            $katA = $rule['kategori_a'] ?? 'Sembako';
            $katB = $rule['kategori_b'] ?? 'Sembako';
            $isSame = (strtolower(trim($katA)) === strtolower(trim($katB)));

            $jenisPenataan = $isSame ? 'Satu Rak Bersebelahan' : 'Display Silang / Ujung Lorong';
            $posisi = $isSame
                ? "Rak Kategori {$katA} (Bersebelahan)"
                : "Rak Ujung Lorong / Dekat Kasir";

            $saran = $isSame
                ? "Letakkan {$b} tepat di sebelah {$a} pada rak kategori {$katA} agar pembeli langsung mengambil keduanya."
                : "Letakkan {$b} di rak display ujung lorong atau keranjang gantung dekat rak {$a} agar mudah terlihat bersamaan.";

            $recommendations[] = [
                'produk_utama'         => $a,
                'produk_asosiasi'      => $b,
                'kategori_utama'       => $katA,
                'kategori_asosiasi'    => $katB,
                'is_same_category'     => $isSame,
                'jumlah_transaksi'     => $rule['jumlah_transaksi'],
                'support'              => $rule['support'],
                'confidence'           => $rule['confidence'],
                'support_x_confidence' => $rule['support_x_confidence'],
                'lolos'                => $rule['lolos'] ?? false,
                'jenis_penataan'       => $jenisPenataan,
                'posisi_rak'           => $posisi,
                'saran_tata_letak'     => $saran
            ];

            if (count($recommendations) >= 15) {
                break;
            }
        }

        return $recommendations;
    }

    /**
     * Memuat data transaksi dari database (mendukung format 1 baris = 1 transaksi dipisah titik koma ';').
     */
    private function _loadData(): void
    {
        $db = \Config\Database::connect();

        $nameToId = [];
        $codeToId = [];
        $produkRows = $db->table('produk')->select('id, nama_produk, kategori_produk')->get()->getResultArray();
        if (!empty($produkRows)) {
            foreach ($produkRows as $p) {
                $this->itemNames[$p['id']] = $p['nama_produk'];
                $this->itemCategories[$p['id']] = $p['kategori_produk'];
                $nameToId[strtolower(trim($p['nama_produk']))] = $p['id'];
                $codeToId['p' . $p['id']] = $p['id'];
            }
        }

        $rows = $db->table('dataset_produk')
            ->select('no_transaksi, id_produk, nama_produk, kategori_produk')
            ->orderBy('no_transaksi')
            ->get()
            ->getResultArray();

        $tempTrx = [];
        foreach ($rows as $row) {
            $trxCode = trim($row['no_transaksi']);
            if (empty($trxCode)) continue;

            if (!isset($tempTrx[$trxCode])) {
                $tempTrx[$trxCode] = [];
            }

            // Pecah daftar produk/kode dalam 1 baris transaksi (dipisah ',' atau ';')
            $rawItems = preg_split('/[;,]+/', $row['nama_produk']);
            $rawKats  = preg_split('/[;,]+/', $row['kategori_produk'] ?? '');

            foreach ($rawItems as $idx => $itemRaw) {
                $itemName = trim($itemRaw);
                if ($itemName === '') continue;

                $lowerKey = strtolower($itemName);
                if (isset($codeToId[$lowerKey])) {
                    $itemId = $codeToId[$lowerKey];
                } elseif (isset($nameToId[$lowerKey])) {
                    $itemId = $nameToId[$lowerKey];
                } elseif (count($rawItems) === 1 && !empty($row['id_produk'])) {
                    $itemId = (int) $row['id_produk'];
                } else {
                    $itemId = $itemName;
                }

                if (!isset($this->itemNames[$itemId])) {
                    $this->itemNames[$itemId] = $itemName;
                    $this->itemCategories[$itemId] = trim($rawKats[$idx] ?? $rawKats[0] ?? 'Sembako');
                }

                if (!in_array($itemId, $tempTrx[$trxCode])) {
                    $tempTrx[$trxCode][] = $itemId;
                }
            }
        }

        $this->transactions = array_values($tempTrx);
        $this->transactionCount = count($this->transactions);
    }
}
