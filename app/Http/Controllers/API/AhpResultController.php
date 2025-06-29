<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\AhpResult;
use App\Models\CampingSiteScore;
use App\Models\UserPreference;
use App\Models\UserPreferenceCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AhpResultController extends Controller
{
    // This method calculates the final score for each camping site based on user preferences and scores.
    public function calculateAHPFinalScore($userId)
    {
        $userPreference = UserPreference::where('user_id', $userId)->first();

        if (!$userPreference) return;

        $criteriaWeights = UserPreferenceCriteria::where('user_preference_id', $userPreference->id)
            ->pluck('normalized_weight', 'criteria_id');

        $scores = CampingSiteScore::all()->groupBy('camping_site_id');

        $results = [];

        // Hitung semua final_score terlebih dahulu
        foreach ($scores as $campingSiteId => $siteScores) {
            $finalScore = 0;

            foreach ($siteScores as $score) {
                $criterionId = $score->criterion_id;
                $normalizedScore = $score->normalized_score;
                $weight = $criteriaWeights[$criterionId] ?? 0;

                $finalScore += $weight * $normalizedScore;
            }

            // Simpan ke array sementara
            $results[] = [
                'user_id' => $userPreference->user_id,
                'camping_site_id' => $campingSiteId,
                'final_score' => $finalScore
            ];
        }

        // Hapus semua data ahp_result lama milik user ini
        AhpResult::withTrashed()->where('user_id', $userId)->forceDelete();

        // Urutkan dan ambil 20 data teratas
        $topResults = collect($results)
            ->sortByDesc('final_score')
            ->take(20);

        // Simpan ulang 20 data teratas
        foreach ($topResults as $result) {
            AhpResult::create([
                'user_id' => $result['user_id'],
                'camping_site_id' => $result['camping_site_id'],
                'final_score' => $result['final_score']
            ]);
        }
    }


    // Ambil data final score AHP untuk user yang sedang login
    public function getAllAHP(Request $request)
    {
        $user = Auth::user();

        // Ambil query params
        $locationId = $request->query('location_id');
        $minRating = $request->query('min_rating');
        $limit = $request->query('limit', 10);

        // Query dasar
        $query = AhpResult::with('campingSite')
            ->where('user_id', $user->id)
            ->orderBy('final_score', 'desc');
        $query->whereHas('campingSite');

        // Filter lokasi jika diberikan
        if (!empty($locationId)) {
            $query->whereHas('campingSite', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        // Filter rating jika diberikan
        if (!empty($minRating)) {
            $query->whereHas('campingSite', function ($q) use ($minRating) {
                $q->where('rating', '>=', $minRating);
            });
        }

        // Eksekusi query dengan menerapkan pagination 
        $results = $query->paginate($limit);

        return ResponseFormatter::success($results, 'Filtered AHP results fetched successfully');
    }
}
