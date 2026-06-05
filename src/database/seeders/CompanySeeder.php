<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'company_name' => 'TechCorp Slovakia',
                'company_address' => 'vymyslena adresa',
                'description' => 'Líder v oblasti vývoja softvéru a cloudových riešení',
                'ico' => '00001111',
                'dic' => '1111110000',
                'category' => 'web',
                'name_of_contact_person' => 'Natasa',
                'is_approved_by_admin' => true,
                'user_id' => 10,
                'logo_id' => 4,
            ],
        ]);
    }
}
