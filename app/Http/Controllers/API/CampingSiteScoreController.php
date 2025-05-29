<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\CampingSiteScore;
use Illuminate\Http\Request;

class CampingSiteScoreController extends Controller
{
    //insert into Database
    public function insertCampingSiteScore(Request $request)
    {
        $data = $request->all();

        try {
            foreach ($data as $item) {
                CampingSiteScore::create([
                    'camping_site_id' => $item['camping_site_id'],
                    'criterion_id' => $item['criterion_id'],
                    'sentiment_percentage' => $item['sentiment_percentage'],
                    'ahp_score' => $item['ahp_score'],
                ]);
            }

            // Normalisasi skor setelah data disimpan
            $this->calculateNormalizedScores();
            return ResponseFormatter::success(null, 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    // Calculate normalized score
    public function calculateNormalizedScores()
    {
        // Ambil semua criterion_id unik
        $criterionIds = CampingSiteScore::select('criterion_id')->distinct()->pluck('criterion_id');

        foreach ($criterionIds as $criterionId) {
            // Ambil semua entri untuk criterion ini
            $rows = CampingSiteScore::where('criterion_id', $criterionId)
                ->select('id', 'camping_site_id', 'ahp_score')
                ->get();

            $count = $rows->count();
            if ($count == 0) continue;

            // Buat pairwise comparison matrix
            $matrix = [];
            foreach ($rows as $i => $rowI) {
                foreach ($rows as $j => $rowJ) {
                    $matrix[$i][$j] = $rowI->ahp_score / $rowJ->ahp_score;
                }
            }

            // Hitung jumlah setiap kolom
            $columnSums = array_fill(0, $count, 0);
            for ($j = 0; $j < $count; $j++) {
                for ($i = 0; $i < $count; $i++) {
                    $columnSums[$j] += $matrix[$i][$j];
                }
            }

            // Hitung normalized matrix dan eigenvector (rata-rata baris)
            for ($i = 0; $i < $count; $i++) {
                $sumRow = 0;
                for ($j = 0; $j < $count; $j++) {
                    $normalized = $matrix[$i][$j] / $columnSums[$j];
                    $sumRow += $normalized;
                }
                $priority = round($sumRow / $count, 4);

                // Update nilai normalized_score di model
                $campingSiteScore = $rows[$i];
                $campingSiteScore->normalized_score = $priority;
                $campingSiteScore->save();
            }
        }
    }
}
