<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $oneWeekLater = now()->addWeek(); // Vytvorí novú inštanciu s dátumom o 7 dní

        DB::table('teams')->insert([

        ]);
    }
}
