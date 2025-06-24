<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateAHPFinalScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:calculate-ahp-final-score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $userPreferenceId = 1; // ganti dengan ID yang valid
        $controller = new \App\Http\Controllers\API\AhpResultController();
        $matrix = $controller->calculateAHPFinalScore($userPreferenceId);
    }
}
