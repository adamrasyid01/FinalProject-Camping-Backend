<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UserPreferenceCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceCriteriaController extends Controller
{
    //
    public function saveUserPreferenceCriteria(Request $request)
    {
        $request->validate([
            'preference_criteria' => 'required|array',
            'preference_criteria.*.criteria_id' => 'required|integer',
            'preference_criteria.*.weight' => 'required|numeric'
        ]);

        $user = Auth::user();

        // Pastikan user memiliki userPreference
        if (!$user->userPreference) {
            return ResponseFormatter::error(null, 'User Preference not found', 404);
        }

        // Hapus preferensi lama untuk user ini
        $user->userPreference->userPreferenceCriterias()->delete();

        // Simpan preferensi baru
        foreach ($request->preference_criteria as $criteria) {
            UserPreferenceCriteria::create([
                'user_preference_id' => $user->userPreference->id,
                'criteria_id' => $criteria['criteria_id'],
                'weight' => $criteria['weight']
            ]);
        }

        return ResponseFormatter::success($user->userPreference->userPreferenceCriterias, 'User Preference Criteria Updated');
    }
}
