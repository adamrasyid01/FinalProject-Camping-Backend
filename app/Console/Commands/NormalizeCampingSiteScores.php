<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CampingSiteScore;

class NormalizeCampingSiteScores extends Command
{
    protected $signature = 'normalize:scores';
    protected $description = 'Normalize camping site scores';

    public function handle()
    {
        CampingSiteScore::normalizeScores();
        $this->info('Camping site scores normalized successfully.');
    }
}

