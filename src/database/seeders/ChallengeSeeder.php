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
        $date = now();
        DB::table('challenges')->insert([
            [
                'id' => 1,
                'program' => 'A',
                'name' => 'AI v mestskej mobilite',
                'description' => 'Hľadáme inovatívne projekty využívajúce strojové učenie na optimalizáciu dopravy v Nitre.',
                'proposal_file_id' => 3,
                'reward' => null,
                'status' => 'open',
                'user_id' => 2,
                'mentor_id' => null,
                'product_owner_id' => null,
                'program_a_category_id' => 1,
                'deleted_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'id' => 2,
                'program' => 'B',
                'name' => 'E-commerce analytika pre eBay',
                'description' => 'Vývoj dashboardov pre vizualizáciu trendov v predaji na globálnej platforme.',
                'proposal_file_id' => 3,
                'reward' => 6000.0,
                'status' => 'open',
                'user_id' => 10,
                'mentor_id' => null,
                'product_owner_id' => 11,
                'program_a_category_id' => null,
                'deleted_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ]);
    }
}
