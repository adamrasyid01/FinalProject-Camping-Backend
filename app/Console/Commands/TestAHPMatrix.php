<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAHPMatrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test-ahp-matrix';

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
        $userPreferenceId = 2; // ganti dengan ID yang valid
        $matrix = \App\Models\UserPreferenceCriteria::buildAHPMatrix($userPreferenceId);
        // dd($matrix);
    }
}
