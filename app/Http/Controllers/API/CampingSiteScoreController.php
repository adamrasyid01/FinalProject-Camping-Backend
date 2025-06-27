<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampingSiteScore; // Pastikan model di-import
use App\Helpers\ResponseFormatter; // Pastikan helper di-import
use Illuminate\Support\Facades\DB; // <-- Mungkin diperlukan jika menggunakan transaksi

class CampingSiteScoreController extends Controller
{
    public function insertCampingSiteScore(Request $request)
    {
        $data = $request->all();

        // Anda bisa menggunakan transaksi untuk memastikan semua proses berhasil atau semua gagal
        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                // Panggil fungsi private untuk menghitung skor AHP
                $ahpScore = $this->calculateAhpScore($item['sentiment_value']);

                // Gunakan updateOrCreate untuk menghindari duplikasi data
                CampingSiteScore::updateOrCreate(
                    [
                        // Kondisi untuk mencari data yang sudah ada
                        'camping_site_id' => $item['camping_site_id'],
                        'criterion_id' => $item['criterion_id'],
                    ],
                    [
                        // Data yang akan diisi atau diperbarui
                        'sentiment_value' => $item['sentiment_value'],
                        'ahp_score' => $ahpScore,
                    ]
                );
            }

            // Panggil fungsi normalisasi setelah semua data dimasukkan/diperbarui
            $this->calculateNormalizedScores();

            DB::commit(); // Simpan semua perubahan jika tidak ada error

            return ResponseFormatter::success(null, 'Data berhasil disimpan/diperbarui dan dinormalisasi');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua operasi jika terjadi error
            // Kirim pesan error yang detail (termasuk pesan asli dari Exception)
            return ResponseFormatter::error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Fungsi private untuk menghitung skor normalisasi.
     * Tidak ada output $this->info() di sini.
     */
    private function calculateNormalizedScores()
    {
        $criterionIds = CampingSiteScore::select('criterion_id')->distinct()->pluck('criterion_id');

        foreach ($criterionIds as $criterionId) {
            $rows = CampingSiteScore::where('criterion_id', $criterionId)->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $totalAHP = $rows->sum('ahp_score');

            if ($totalAHP == 0) {
                continue;
            }

            foreach ($rows as $row) {
                $normalized = $row->ahp_score / $totalAHP;

                CampingSiteScore::where('id', $row->id)->update([
                    'normalized_score' => round($normalized, 4)
                ]);
            }
        }
    }

    /**
     * Fungsi private untuk mengubah nilai sentimen menjadi skor AHP.
     * Logikanya sudah benar.
     */
    private function calculateAhpScore($sentiment)
    {
        if ($sentiment >= 80) return 10; // Disederhanakan
        elseif ($sentiment >= 60) return 9;
        elseif ($sentiment >= 40) return 8;
        elseif ($sentiment >= 20) return 7;
        elseif ($sentiment >= 0) return 6;
        elseif ($sentiment >= -20) return 5;
        elseif ($sentiment >= -40) return 4;
        elseif ($sentiment >= -60) return 3;
        elseif ($sentiment >= -80) return 2;
        elseif ($sentiment >= -100) return 1;
        else return 0;
    }
}