<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('commissions')->insert([
            [
                'challenge_id' => 1,
            ],
            [
                'challenge_id' => 2,
            ],
            [
                'challenge_id' => 3,
            ],
            [
                'challenge_id' => 4,
            ],
            [
                'challenge_id' => 5,
            ]

        ]);
    }
}
