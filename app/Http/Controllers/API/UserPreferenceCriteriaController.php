<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UserPreferenceCriteria;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceCriteriaController extends Controller
{
    //
    public function getUserPreferenceCriteria()
    {
        $user = Auth::user();

        if (!$user->userPreference) {
            return ResponseFormatter::error(null, 'User Preference not found', 404);
        }

        $criteria = $user->userPreference->userPreferenceCriterias;

        return ResponseFormatter::success($criteria, 'User Preference Criteria Retrieved');
    }
    public function saveUserPreferenceCriteria(Request $request)
    {
        $request->validate([
            'preference_criteria' => 'required|array',
            'preference_criteria.*.criteria_id' => 'required|integer',
            'preference_criteria.*.weight' => 'required|numeric'
        ]);

        $user = Auth::user();

        if (!$user->userPreference) {
            return ResponseFormatter::error(null, 'User Preference not found', 404);
        }

        foreach ($request->preference_criteria as $criteria) {
            UserPreferenceCriteria::updateOrCreate(
                [
                    'user_preference_id' => $user->userPreference->id,
                    'criteria_id' => $criteria['criteria_id'],
                ],
                [
                    'weight' => $criteria['weight']
                ]
            );
        }

        // Bangun matriks dan ambil hasilnya
        $this->buildAHPMatrix($user->userPreference->id);

        // Panggil AHPController untuk menghitung hasil AHP
        $ahpResultController = new AhpResultController();
        $ahpResultController->calculateAHPFinalScore($user->id);

        // BALIKKAN RESPONSE KE FE
        return ResponseFormatter::success($user->userPreference->userPreferenceCriterias, 'User Preference Criteria Updated');
    }

    // BUAT MATRIKS AHP nya
    public function buildAHPMatrix($user_preference_id)
    {
        // Ambil semua preferensi user
        $preferences = UserPreferenceCriteria::where('user_preference_id', $user_preference_id)
            ->with('criteria') // pastikan relasi ke tabel criteria tersedia
            ->get();


        // dd($preferences->toArray());

        // Buat array [nama_kriteria => bobot]
        $priorityMap = [];
        // Ambil nama kriteria dan bobotnya
        foreach ($preferences as $pref) {
            $priorityMap[$pref->criteria->name] = $pref->weight;
        }

        // Urutkan dari bobot terbesar ke terkecil
        // arsort($priorityMap);
        // array:4 [
        // "Kemudahan Transportasi" => 9.0
        // "Kebersihan" => 7.0
        // "Kenyamanan" => 5.0
        // "Keamanan" => 3.0
        // ]
        
        // Bangun matriks perbandingan berpasangan
        $matrix = [];
        // Looping untuk setiap keys dari priorityMap
        foreach (array_keys($priorityMap) as $i) {
            $matrix[$i] = [];
            //  dump("Outer loop - iName: " . $i);
            foreach (array_keys($priorityMap) as $j) {
                // dump("Inner loop - jName: " . $j);
                if ($i === $j) {
                    $matrix[$i][$j] = 1;
                } else {
                    $matrix[$i][$j] = $priorityMap[$i] / $priorityMap[$j]; // Perbandingan bobot
                }
            }
        }

        // dd($matrix);


        // Hitung total tiap kolom (i sebagai kesamping dan j sebagai ke bawah)
        $columnSums = [];
        foreach (array_keys($priorityMap) as $i) {
            // dump($i);
            $sum = 0;
            foreach (array_keys($priorityMap) as $j) {
                // dump($j);
                $sum += $matrix[$j][$i];
            }
            $columnSums[$i] = $sum;
        }
        // dd($columnSums);

        // Normalisasi (tiap elemen di variabel $matrix dibagi variabel yang menampung total -> columnSums)
        $normalisasiMatrix = [];

        foreach (array_keys($priorityMap) as $iName) {
            $normalisasiMatrix[$iName] = [];
            foreach (array_keys($priorityMap) as $jName) {
               $normalisasiMatrix[$iName][$jName] = $matrix[$iName][$jName] / $columnSums[$jName];
            }
        }
        // dd($normalisasiMatrix);

        // Hitung Panjang Vektor untuk setiap kriteria
        $panjangVektor = [];
        foreach (array_keys($priorityMap) as $iName) {
            $sum = 0;
            foreach (array_keys($priorityMap) as $jName) {
                $sum += $normalisasiMatrix[$iName][$jName];
            }
            $panjangVektor[$iName] = $sum / count($priorityMap);
        }
        // dd($panjangVektor);

        // Hitung  Eigen Value ATAU lamdaMax
        $lamdaMax = [];
        $sumLamdaMax = 0;
        foreach (array_keys($priorityMap) as $iName) {
            $lamdaMax[$iName] = $panjangVektor[$iName] * $columnSums[$iName];
            $sumLamdaMax += $lamdaMax[$iName];
        }

        // Hitung CI (Consistency Index)
        $CI = ($sumLamdaMax - count($priorityMap)) / (count($priorityMap) - 1);
        // dd($CI);

        // Hitung CR (Consistency Ratio)
        $CR = $CI / 0.9;
        
        // Jika CR > 0.1, maka matriks tidak konsisten
        if($CR <= 0.1) {
            Log::info("Matriks konsisten");
        } else {
            Log::warning("Matriks tidak konsisten");
        }

        // Simpan normalized_weight ke DB
        foreach ($preferences as $pref) {
            if (!$pref->criteria) continue;

            $name = $pref->criteria->name;
            if (isset($panjangVektor[$name])) {
                $pref->normalized_weight = $panjangVektor[$name];
                $pref->save();
            }
        }

        // Return matrix jika ingin debug
        return $matrix;
    }

}
