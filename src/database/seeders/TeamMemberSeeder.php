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
        $date = now();
        DB::table('team_members')->insert([
            [
                'team_id' => 1,
                'user_id' => 1,
                'status' => 'active',
                'active_at' => $date,
                'expires_at' => $date,
            ],
            [
                'team_id' => 2,
                'user_id' => 2,
                'status' => 'inactive',
                'active_at' => $date,
                'expires_at' => $date,
            ],
            [
                'team_id' => 3,
                'user_id' => 3,
                'status' => 'active',
                'active_at' => $date,
                'expires_at' => $date,
            ],
            [
                'team_id' => 4,
                'user_id' => 4,
                'status' => 'active',
                'active_at' => $date,
                'expires_at' => $date,
            ],
            [
                'team_id' => 5,
                'user_id' => 5,
                'status' => 'inactive',
                'active_at' => $date,
                'expires_at' => $date,
            ]

        ]);
    }
}
