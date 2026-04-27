<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = date('Y-m-d H:i:s');
        DB::table('milestones')->insert([
            [
                'challenge_id' => 1,
                'title' => 'Prázdna obrazovka',
                'description' => 'Spojte frontend s databázou cez API tak, aby sa na obrazovke zobrazili reálne dáta, nielen statický text.',
                'comment' => 'ziadny komentar',
                'date_of_reasisation' => $date,
                'is_finished' => false
            ],
            [
                'challenge_id' => 2,
                'title' => 'Záťažový test 100',
                'description' => 'Optimalizujte kód tak, aby systém zvládol 100 súbežných požiadaviek za sekundu bez zvýšenia odozvy nad 500ms.',
                'comment' => 'ziadny komentar',
                'date_of_reasisation' => $date,
                'is_finished' => false
            ],
            [
                'challenge_id' => 3,
                'title' => 'Nepriestrelný kód',
                'description' => 'Napíšte automatizované testy pre všetky kľúčové funkcie modulu tak, aby pokrytie kódu (test coverage) bolo minimálne 80 %.',
                'comment' => 'ziadny komentar',
                'date_of_reasisation' => $date,
                'is_finished' => true
            ],
            [
                'challenge_id' => 4,
                'title' => 'Bezpečný prístup',
                'description' => 'mplementujte kompletné prihlasovanie, obnovu hesla a overovanie právomocí (kto môže čo vidieť).',
                'comment' => 'ziadny komentar',
                'date_of_reasisation' => $date,
                'is_finished' => false
            ],
            [
                'challenge_id' => 5,
                'title' => 'Čistý stôl',
                'description' => 'Odstráňte všetky "TODO" poznámky z kódu, opravte nahlásené drobné chyby (bugy) a prečistite dokumentáciu k API.',
                'comment' => 'ziadny komentar',
                'date_of_reasisation' => $date,
                'is_finished' => true
            ]
        ]);
    }
}
