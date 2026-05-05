<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('students')->insert([
            [
                'user_id' => 1,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'team_status' => 'teamleader',
                'curriculum_vitae_id' => 1
            ],
            [
                'user_id' => 2,
                'university' => 'UKF',
                'is_accepted_by_admin' => true,
                'team_status' => 'teamleader',
                'curriculum_vitae_id' => 1
            ],
            [
                'user_id' => 3,
                'university' => 'SPU',
                'is_accepted_by_admin' => false,
                'team_status' => 'not_in_team',
                'curriculum_vitae_id' => 1
            ],
            [
                'user_id' => 4,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'team_status' => 'invited',
                'curriculum_vitae_id' => 1
            ],
            [
                'user_id' => 5,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'team_status' => 'team_member',
                'curriculum_vitae_id' => 1
            ]
        ]);
    }
}
