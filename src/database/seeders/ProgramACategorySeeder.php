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
                'title' => 'Vývoj softvéru',
                'description_of_skills' => 'objektové technológie, úvod do softvérového inžinierstva, mobilné aplikácie, senzory, manažment projektov, testovanie',
                'status' => 'visible'
            ],
            [
                'title' => 'AI a dátové technológie',
                'description_of_skills' => 'databázové systémy, počítačová analýza dát, AI, úvod do strojového učenia, neurónové siete, hĺbková analýza dát',
                'status' => 'visible'
            ],
            [
                'title' => 'Webové aplikácie',
                'description_of_skills' => 'jazyky webu, FE/BE technológie, webové aplikácie na platforme Java',
                'status' => 'visible'
            ],
            [
                'title' => 'Herný vývoj',
                'description_of_skills' => 'herné vývojové prostredia, vývoj 3D aplikácií, virtuálna a rozšírená realita',
                'status' => 'visible'
            ],
            [
                'title' => 'IoT a embedded systémy',
                'description_of_skills' => 'programovanie v jazyku C, internet vecí, inteligentné systémy, robotické a priemyselné systémy',
                'status' => 'visible'
            ]
        ]);
    }
}
