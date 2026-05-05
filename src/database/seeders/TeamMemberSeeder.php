<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $oneWeekLater = now()->addWeek();

        DB::table('team_member')->insert([
            [
                'team_id' => 1,
                'student_id' => 1,
                'status' => 'active',
                'active_from' => $now,
                'active_to' => $oneWeekLater,
            ],
            [
                'team_id' => 2,
                'student_id' => 2,
                'status' => 'inactive',
                'active_from' => $now,
                'active_to' => $oneWeekLater,
            ],
            [
                'team_id' => 3,
                'student_id' => 3,
                'status' => 'active',
                'active_from' => $now,
                'active_to' => $oneWeekLater,
            ],
            [
                'team_id' => 4,
                'student_id' => 4,
                'status' => 'active',
                'active_from' => $now,
                'active_to' => $oneWeekLater,
            ],
            [
                'team_id' => 5,
                'student_id' => 5,
                'status' => 'inactive',
                'active_from' => $now,
                'active_to' => $oneWeekLater,
            ]
        ]);
    }
}
