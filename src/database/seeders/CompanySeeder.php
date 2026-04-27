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
                'organization_identification_number' => '0000000000000b',
                'tax_identification_number' => '11111111111a',
                'category' => 'web',
                'name_of_contact_person' => 'Natasa',
                'is_approved_by_admin' => true,
                'show_logo_image' => true,
                'user_id' => 1
            ],
            [
                'company_name' => 'DataVision Analytics',
                'company_address' => 'vymyslena adresa',
                'description' => 'Špecialista na dátovú analytiku a AI aplikácie',
                'organization_identification_number' => '0000000000000b',
                'tax_identification_number' => '11111111111a',
                'category' => 'Data & AI',
                'name_of_contact_person' => 'Peter',
                'is_approved_by_admin' => true,
                'show_logo_image' => true,
                'user_id' => 2
            ],
            [
                'company_name' => 'MobileDev Studio',
                'company_address' => 'vymyslena adresa',
                'description' => 'Tvorba mobilných aplikácií pre iOS a Android',
                'organization_identification_number' => '0000000000000b',
                'tax_identification_number' => '11111111111a',
                'category' => 'Mobilné aplikácie',
                'name_of_contact_person' => 'Natasa',
                'is_approved_by_admin' => true,
                'show_logo_image' => true,
                'user_id' => 3
            ],
            [
                'company_name' => 'GameForge Studios',
                'company_address' => 'vymyslena adresa',
                'description' => 'Vývoj hier a interaktívnych aplikácií',
                'organization_identification_number' => '0000000000000b',
                'tax_identification_number' => '11111111111a',
                'category' => 'Gaming',
                'name_of_contact_person' => 'Maria',
                'is_approved_by_admin' => true,
                'show_logo_image' => true,
                'user_id' => 4
            ],
            [
                'company_name' => 'InnoSoft Solutions',
                'company_address' => 'vymyslena adresa',
                'description' => 'Poskytovateľ komplexných IT riešení pre firmy',
                'organization_identification_number' => '0000000000000b',
                'tax_identification_number' => '11111111111a',
                'category' => 'IoT',
                'name_of_contact_person' => 'Milan',
                'is_approved_by_admin' => true,
                'show_logo_image' => true,
                'user_id' => 5
            ],

        ]);
    }
}
