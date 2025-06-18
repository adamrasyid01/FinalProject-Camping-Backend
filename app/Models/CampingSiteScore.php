<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Log;

class CampingSiteScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'camping_site_id',
        'criterion_id',
        'sentiment_value',
        'ahp_score',
        'normalized_score',
    ];

    public function criterias()
    {
        return $this->belongsTo(Criteria::class, 'criterion_id');
    }
    public function campingSites()
    {
        return $this->belongsTo(CampingSite::class, 'camping_site_id');
    }


   public static function normalizeScores()
    {
        Log::info('Memulai proses normalisasi skor yang dioptimalkan...');

        // === LANGKAH 1: BACA SEMUA DATA DALAM SATU QUERY ===
        // Hanya ambil kolom yang kita butuhkan untuk efisiensi memori.
        $allScores = self::query()->select('id', 'camping_site_id', 'ahp_score')->get();

        if ($allScores->isEmpty()) {
            Log::info('Tidak ada data skor untuk dinormalisasi.');
            return; // Keluar jika tidak ada data sama sekali
        }
        
        // === LANGKAH 2: KELOMPOKKAN DATA DI MEMORI (SANGAT CEPAT) ===
        // Menggunakan metode groupBy() dari Laravel Collection, bukan dari database.
        $groupedBySite = $allScores->groupBy('camping_site_id');
        
        // === LANGKAH 3: LAKUKAN PERHITUNGAN & SIAPKAN DATA UNTUK UPDATE ===
        // Array ini akan menampung semua perubahan yang perlu disimpan ke DB.
        $updates = [];

        // Lakukan perhitungan di memori, bukan dengan query berulang.
        // KEY -> VALUE
        foreach ($groupedBySite as $campingSiteId => $scores) {
            // Hitung total skor untuk grup (lokasi camping) saat ini.
            $totalScore = $scores->sum('ahp_score');

            // Lewati grup ini jika total skornya 0 untuk menghindari pembagian dengan nol.
            if ($totalScore == 0) {
                continue;
            }

            // Hitung skor normalisasi untuk setiap item di grup ini.
            foreach ($scores as $score) {
                $normalizedScore = $score->ahp_score / $totalScore;
                
                // Kumpulkan hasilnya ke dalam array $updates.
                // Key adalah 'id' dari baris, dan value adalah 'normalized_score' yang baru.
                $updates[$score->id] = round($normalizedScore, 5); // 5 angka di belakang koma untuk presisi
            }
        }

        // === LANGKAH 4: LAKUKAN SATU KALI UPDATE MASSAL (BATCH UPDATE) ===
        // Hanya jalankan query jika ada data yang perlu di-update.
        if (!empty($updates)) {
            $ids = array_keys($updates);
            $cases = '';
            $bindings = [];

            // Bangun statement CASE ... WHEN ... THEN untuk update massal
            foreach ($updates as $id => $priority) {
                $cases .= "WHEN ? THEN ? ";
                $bindings[] = $id;
                $bindings[] = $priority;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            // Gabungkan bindings untuk CASE dan untuk WHERE IN
            $bindings = array_merge($bindings, $ids);
            
            // Eksekusi satu query UPDATE untuk semua baris sekaligus
            DB::update(
                "UPDATE camping_site_scores SET normalized_score = CASE id {$cases} END WHERE id IN ({$placeholders})",
                $bindings
            );

            Log::info('Batch update berhasil untuk ' . count($updates) . ' skor.');
        } else {
            Log::info('Tidak ada skor yang perlu dinormalisasi setelah perhitungan.');
        }
    }
}
