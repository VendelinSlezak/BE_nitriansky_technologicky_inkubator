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

        DB::table('team_members')->insert([
            [
                'team_id' => 1,
                'student_id' => 3,
                'status' => 'invited',
                'active_from' => null,
                'active_to' => null,
                'statuory_declaration_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'team_id' => 1,
                'student_id' => 2,
                'status' => 'teamleader',
                'active_from' => $now,
                'active_to' => null,
                'statuory_declaration_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'team_id' => 2,
                'student_id' => 4,
                'status' => 'teamleader',
                'active_from' => $now,
                'active_to' => null,
                'statuory_declaration_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'team_id' => 4,
                'student_id' => 1,
                'status' => 'teamleader',
                'active_from' => $now,
                'active_to' => null,
                'statuory_declaration_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
