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
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'user_id' => 1,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'is_invited_to_the_team' => true,
                'is_a_teamleader' => true,
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Mackovic',
                'user_id' => 2,
                'university' => 'UKF',
                'is_accepted_by_admin' => true,
                'is_invited_to_the_team' => false,
                'is_a_teamleader' => true,
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Holeckova',
                'user_id' => 3,
                'university' => 'SPU',
                'is_accepted_by_admin' => false,
                'is_invited_to_the_team' => true,
                'is_a_teamleader' => true,
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Danis',
                'user_id' => 4,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'is_invited_to_the_team' => true,
                'is_a_teamleader' => false,
            ],
            [
                'first_name' => 'Lucia',
                'last_name' => 'Horvathova',
                'user_id' => 5,
                'university' => 'SPU',
                'is_accepted_by_admin' => true,
                'is_invited_to_the_team' => true,
                'is_a_teamleader' => true,
            ]
        ]);
    }
}
