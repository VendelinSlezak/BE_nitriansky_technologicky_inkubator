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
        $date = now();
        DB::table('teams')->insert([
            [
                'challenge_id' => 1,
                'name' => 'HTTP Error 404 team',
                'active_from' => $date,
                'active_to' => $date,
            ],
            [
                'challenge_id' => 2,
                'name' => 'HTTP Error 500 team',
                'active_from' => $date,
                'active_to' => $date,
            ],
            [
                'challenge_id' => 3,
                'name' => 'Request timed out team',
                'active_from' => $date,
                'active_to' => $date,
            ],
            [
                'challenge_id' => 4,
                'name' => 'Destination host unreachable team',
                'active_from' => $date,
                'active_to' => $date,
            ],
            [
                'challenge_id' => 5,
                'name' => 'HTTP Error 400 team',
                'active_from' => $date,
                'active_to' => $date,
            ]

        ]);
    }
}
