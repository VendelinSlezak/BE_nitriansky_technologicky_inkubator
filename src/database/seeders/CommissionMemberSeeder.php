<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('commission_members')->insert([
            [
                'challenge_id' => 1,
                'user_id' => 8,
                'status' => 'recorder',
            ],
            [
                'challenge_id' => 2,
                'user_id' => 8,
                'status' => 'recorder',
            ],
            [
                'challenge_id' => 3,
                'user_id' => 8,
                'status' => 'recorder',
            ],
            [
                'challenge_id' => 4,
                'user_id' => 8,
                'status' => 'recorder',
            ],
        ]);
    }
}
