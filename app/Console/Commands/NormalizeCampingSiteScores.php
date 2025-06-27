<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CampingSiteScore;

class NormalizeCampingSiteScores extends Command
{
    protected $signature = 'run:normalize-scores';
    protected $description = 'Normalize camping site scores';

    public function handle()
    {
         // Ambil semua ID kriteria yang unik
        $criterionIds = CampingSiteScore::select('criterion_id')->distinct()->pluck('criterion_id');

        foreach ($criterionIds as $criterionId) {
            
            // --- MULAI BAGIAN DEBUG ---
            
            // GANTI '1' DENGAN ID ASLI DARI KRITERIA 'KEAMANAN' DI DATABASE ANDA
            $targetCriterionIdForDebug = 1; 

            // Kita hanya akan debug untuk satu kriteria ini untuk melihat masalahnya
            if ($criterionId == $targetCriterionIdForDebug) { 

                $this->info("=============================================");
                $this->info("MEMULAI DEBUG UNTUK CRITERION_ID: " . $criterionId);
                $this->line("");

                $rows = CampingSiteScore::where('criterion_id', $criterionId)->get();
                
                $this->info("Data mentah yang diambil dari database untuk kriteria ini:");
                // Tampilkan data yang ditarik agar kita bisa lihat skor mentahnya
                $this->table(
                    ['ID Database', 'Camping Site ID', 'AHP Score'],
                    $rows->map(function ($row) {
                        return [$row->id, $row->camping_site_id, $row->ahp_score];
                    })
                );

                // Hitung dan tampilkan totalnya
                $totalScore = $rows->sum('ahp_score');
                $this->line("");
                $this->info("Hasil dari ->sum('ahp_score') adalah: " . $totalScore);
                $this->line("");

                if ($rows->isNotEmpty()) {
                    $firstRow = $rows->first();
                    $this->info("Contoh perhitungan untuk baris pertama (ID: {$firstRow->id}):");
                    $this->info("Rumus: {$firstRow->ahp_score} / {$totalScore}");
                    $this->info("Hasil: " . ($firstRow->ahp_score / $totalScore));
                }

                $this->line("");
                $this->info("Debug selesai. Eksekusi dihentikan.");
                $this->info("=============================================");
                
                // dd() akan menghentikan seluruh eksekusi script di sini.
                dd("Silakan periksa output di terminal di atas untuk menemukan sumber masalah."); 
            }
            // --- AKHIR BAGIAN DEBUG ---


            // Kode normalisasi asli Anda tidak akan berjalan karena ada dd() di atas
            $rows = CampingSiteScore::where('criterion_id', $criterionId)->get();
            if ($rows->isEmpty()) continue;
            $totalScore = $rows->sum('ahp_score');
            if ($totalScore == 0) continue;
            foreach ($rows as $row) {
                $normalized = $row->ahp_score / $totalScore;
                CampingSiteScore::where('id', $row->id)->update([
                    'normalized_score' => round($normalized, 4) 
                ]);
            }
        }
        
        $this->info('Proses normalisasi skor telah selesai.');
    }
}

