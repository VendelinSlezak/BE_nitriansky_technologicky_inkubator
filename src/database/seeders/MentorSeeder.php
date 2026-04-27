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
                'user_id' => 1,
                'description' => 'Expert na softverove inzinierstvo',
                'expertise' => 'Softvérová architektúra, Cloud computing',
                'experience' => '15+ rokov v IT'
            ],
            [
                'user_id' => 2,
                'description' => 'Expert na AI',
                'expertise' => 'AI a Machine Learning',
                'experience' => '10 rokov v data analytics'
            ],
            [
                'user_id' => 3,
                'description' => 'Expert na mobilne aplikacie',
                'expertise' => 'Mobilne aplikacie',
                'experience' => '10 rokov v mobile dev'
            ],
            [
                'user_id' => 4,
                'description' => 'Expert na web development',
                'expertise' => 'Web development',
                'experience' => '20 rokov vo web dev'
            ],
            [
                'user_id' => 5,
                'description' => 'Expert na kybernetiku',
                'expertise' => 'Kybernetika',
                'experience' => '8 rokov vo kybernetike'
            ]
        ]);
    }
}
