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

            return ResponseFormatter::success(null, 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    // Calculate normalized score
}
