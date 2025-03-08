<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CampingLocation;
use App\Models\Criteria;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        CampingLocation::factory()->count(10)->create();

        // Criteria::factory()->count(4)->create();

        $this->command->info('Seeder berhasil! 10 data camping locations telah dimasukkan.');
    }
}
