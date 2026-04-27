<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_employees')->insert([
            [
                'company_id' => 1,
                'user_id' => 1
            ],
            [
                'company_id' => 2,
                'user_id' => 2
            ],
            [
                'company_id' => 3,
                'user_id' => 3
            ],
            [
                'company_id' => 4,
                'user_id' => 4
            ],
            [
                'company_id' => 5,
                'user_id' => 5
            ],
        ]);
    }
}
