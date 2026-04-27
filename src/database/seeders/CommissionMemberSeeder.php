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
                'commission_id' => 1,
                'user_id' => 1,
                'status' => 'Finished',
            ],
            [
                'commission_id' => 2,
                'user_id' => 2,
                'status' => 'In Progress',
            ],
            [
                'commission_id' => 3,
                'user_id' => 3,
                'status' => 'In Progress',
            ],
            [
                'commission_id' => 4,
                'user_id' => 3,
                'status' => 'Finished',
            ],
            [
                'commission_id' => 4,
                'user_id' => 4,
                'status' => 'Finished',
            ]
        ]);
    }
}
