<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = now();
        DB::table('articles')->insert([
            [
                'title' => 'NTI spúšťa nové kolo Programu A pre jesenný semester 2026',
                'perex' => 'Otvárame nové hodnotiace kolo pre grantový inkubačný program. Prihlášky je možné podávať do 31. marca 2026.',
                'text' => 'Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok...

                            Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom...

                            Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu".',
                'published_at' => $date,
                'user_id' => 1,
                'image_id' => 5,
                'image_description' => 'NTI s radosťou oznamuje otvorenie jarného kola prihlášok...'
             ],
            [
                'title' => 'NTI víta nových partnerov z IT sektora',
                'perex' => 'Tri nové technologické firmy sa pridali k NTI ako partneri. Pripravujeme zaujímavé projekty v oblasti AI a cloud computingu.',
                'text' => 'Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok...

                            Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom...

                            Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu."',
                'published_at' => $date,
                'user_id' => 2,
                'image_id' => 5,
                'image_description' => 'NTI s radosťou oznamuje otvorenie jarného kola prihlášok...'
            ],
            [
                'title' => 'Absolvent NTI získal investíciu pre svoj startup',
                'perex' => 'Bývalý účastník Programu A úspešne získal seed investíciu vo výške 150 000 € pre svoj AI startup..',
                'text' => 'Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok...

                            Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom...

                            Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu."',
                'published_at' => $date,
                'user_id' => 3,
                'image_id' => 5,
                'image_description' => 'NTI s radosťou oznamuje otvorenie jarného kola prihlášok...'
            ],
            [
                'title' => 'NTI rozširuje portfólio mentorov',
                'perex' => 'Otvárame nové hodnotiace kolo pre grantový inkubačný program. Prihlášky je možné podávať do 31. marca 2026.',
                'text' => 'Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok...

                            Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom...

                            Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu."',
                'published_at' => $date,
                'user_id' => 3,
                'image_id' => 5,
                'image_description' => 'NTI s radosťou oznamuje otvorenie jarného kola prihlášok...'
            ],
            [
                'title' => 'Úspešný projekt od tímu CodeMasters pre firmu TechCorp',
                'perex' => 'Študentský tím CodeMasters úspešne dokončil komplexnú e-commerce platformu pre partnerskú firmu TechCorp Slovakia.',
                'text' => 'Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok...

                            Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom...

                            Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu."',
                'published_at' => $date,
                'user_id' => 2,
                'image_id' => 5,
                'image_description' => 'NTI s radosťou oznamuje otvorenie jarného kola prihlášok...'
            ]
        ]);
    }
}
