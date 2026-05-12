<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mentors')->insert([
            [
                'user_id' => 6,
                'description' => 'Expert na softverove inzinierstvo',
                'expertise' => 'Softvérová architektúra, Cloud computing',
                'experience' => '15+ rokov v IT'
            ],
            [
                'user_id' => 7,
                'description' => 'Expert na AI',
                'expertise' => 'AI a Machine Learning',
                'experience' => '10 rokov v data analytics'
            ],
        ]);
    }
}
