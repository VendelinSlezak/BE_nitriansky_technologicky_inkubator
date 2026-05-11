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
                'student_id' => 2,
                'status' => 'invited',
                'active_from' => null,
                'active_to' => null,
                'statuory_declaration_id' => null,
            ],
            [
                'team_id' => 1,
                'student_id' => 3,
                'status' => 'teamleader',
                'active_from' => $now,
                'active_to' => null,
                'statuory_declaration_id' => 1,
            ],
        ]);
    }
}
