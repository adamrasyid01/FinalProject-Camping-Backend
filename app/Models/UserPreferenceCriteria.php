<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class UserPreferenceCriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_preference_id', 'criteria_id', 'weight', 'normalized_weight'];
    public function userPreference()
    {
        return $this->belongsTo(UserPreference::class, 'user_preference_id');
    }
    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }

    // BUAT MATRIKS AHP nya
    public static function buildAHPMatrix($user_preference_id)
    {
        // Ambil semua preferensi user
        $preferences = self::where('user_preference_id', $user_preference_id)
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
        arsort($priorityMap);
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
        dd($panjangVektor);

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




    public static function normalizeWeights($user_preference_id)
    {
        $kriteria = self::where('user_preference_id', $user_preference_id)->get();
        $totalWeight = $kriteria->sum('weight');

        foreach ($kriteria as $item) {
            $normalizedWeight = ($totalWeight > 0) ? $item->weight / $totalWeight : 0;
            $item->update(['normalized_weight' => $normalizedWeight]);
        }
    }
}
