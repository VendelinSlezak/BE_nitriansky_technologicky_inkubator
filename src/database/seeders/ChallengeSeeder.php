<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('challenges')->insert([
            [
                'program' => 'A',
                'name' => 'AI v mestskej mobilite',
                'automatically_create_team_after_approval' => true,
                'description' => 'Hľadáme inovatívne projekty využívajúce strojové učenie na optimalizáciu dopravy v Nitre.',
                'proposal_file_id' => 1,
                'reward' => null,
                'status' => 'Published',
                'user_id' => 1,
                'mentor_id' => 1,
             ],
            [
                'program' => 'B',
                'name' => 'E-commerce analytika pre eBay',
                'automatically_create_team_after_approval' => false,
                'description' => 'Vývoj dashboardov pre vizualizáciu trendov v predaji na globálnej platforme.',
                'proposal_file_id' => 2,
                'reward' => 6000.0,
                'status' => 'Published',
                'user_id' => 2,
                'mentor_id' => 2,
            ],
            [
                'program' => 'A',
                'name' => 'Zelená energia pre domy',
                'automatically_create_team_after_approval' => true,
                'description' => 'Platforma na zdieľanie prebytočnej energie z fotovoltických panelov v rámci komunity.',
                'proposal_file_id' => 3,
                'reward' => null,
                'status' => 'Created',
                'user_id' => 3,
                'mentor_id' => 2,
            ],
            [
                'program' => 'B',
                'name' => 'AR navigácia v budovách',
                'automatically_create_team_after_approval' => true,
                'description' => 'Vývoj prototypu rozšírenej reality pre navigáciu návštevníkov v komplexných administratívnych budovách.',
                'proposal_file_id' => 4,
                'reward' => 4000.0,
                'status' => 'Published',
                'user_id' => 4,
                'mentor_id' => 3,
            ],
            [
                'program' => 'B',
                'name' => 'Senzorická sieť pre Smart City',
                'automatically_create_team_after_approval' => false,
                'description' => 'Implementácia IoT riešenia pre monitorovanie kvality ovzdušia v priemyselnej zóne pod Zoborom.',
                'proposal_file_id' => 5,
                'reward' => 8600.0,
                'status' => 'Published',
                'user_id' => 5,
                'mentor_id' => 4,
            ]
        ]);
    }
}
