<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramACategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('program_a_categories')->insert([
            [
                'title' => 'AI a dátové technológie',
                'description_of_skills' => 'Zaklady AI v pythone, Uvod do strojoveho ucenia, Neuronove siete'
            ],
            [
                'title' => 'Web development',
                'description_of_skills' => 'Zaklady frontendu a backendu, jeden webový framework'
            ],
            [
                'title' => 'IoT',
                'description_of_skills' => 'Zaklady elektroniky, programovanie Arduina'
            ],
            [
                'title' => 'Siete a kybernetika',
                'description_of_skills' => 'Zaklady pocitacovych sieti a Kali Linux'
            ],
            [
                'title' => 'Mobilne aplikacie',
                'description_of_skills' => 'Zaklady Javy a Kotlin'
            ]

        ]);
    }
}
