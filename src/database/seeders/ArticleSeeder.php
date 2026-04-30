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
                'published_at' => $date,
                'user_id' => 1,
                'image_id' => 5,
                'image_description' => 'Mladí inovátori počas workshopu v priestoroch NTI.',
                'content' => <<<EOT
Technologický inkubátor NTI s radosťou oznamuje otvorenie jarného kola prihlášok pre rok 2026. Tento program je vlajkovou loďou našej podpory pre začínajúcich podnikateľov a študentské startupy.

Úspešní uchádzači získajú nielen finančný grant na rozvoj svojho projektu, ale aj prístup k exkluzívnym priestorom v našom co-workingu a balík mentoringových hodín s expertmi z praxe.

Proces výberu pozostáva z dvoch kôl – formálneho posúdenia biznis plánu a následného osobného "pitchingu" pred odbornou komisiou zloženou z investorov a technológov.
EOT
             ],
            [
                'title' => 'NTI víta nových partnerov z IT sektora',
                'perex' => 'Tri nové technologické firmy sa pridali k NTI ako partneri. Pripravujeme zaujímavé projekty v oblasti AI a cloud computingu.',
                'published_at' => $date,
                'user_id' => 2,
                'image_id' => 5,
                'image_description' => 'Stretnutie s novými partnermi v konferenčnej miestnosti.',
                'content' => <<<EOT
S hrdosťou oznamujeme, že naše rady rozšírili popredné spoločnosti zo slovenského IT sektora. Táto spolupráca prinesie našim členom ešte viac príležitostí na reálne projekty a stáže.

Zameriame sa predovšetkým na oblasť umelej inteligencie a automatizácie procesov v cloude. Partneri prisľúbili aj dodanie špičkového hardvéru pre naše laboratóriá.

V blízkej dobe plánujeme spoločné hackathony, kde si študenti budú môcť zmerať sily pri riešení reálnych zadaní priamo od týchto firiem.
EOT
            ],
            [
                'title' => 'Absolvent NTI získal investíciu pre svoj startup',
                'perex' => 'Bývalý účastník Programu A úspešne získal seed investíciu vo výške 150 000 € pre svoj AI startup.',
                'published_at' => $date,
                'user_id' => 3,
                'image_id' => 5,
                'image_description' => 'Zakladateľ startupu pri podpisovaní investičnej zmluvy.',
                'content' => <<<EOT
Príbeh úspechu nášho absolventa Michala opäť potvrdzuje, že cesta cez inkubátor má zmysel. Jeho projekt zameraný na analýzu dát v zdravotníctve zaujal zahraničných investorov.

Investícia vo výške 150 000 € bude použitá na rozšírenie tímu vývojárov a expanziu na český a rakúsky trh. Michal začínal v našom inkubátore s jednoduchým prototypom.

„Mentoring v NTI mi otvoril oči v oblasti biznis modelu. Bez toho by som pred investormi neobstál,“ hovorí Michal v krátkom rozhovore, ktorý nájdete v našom archíve.
EOT
            ],
            [
                'title' => 'NTI rozširuje portfólio mentorov',
                'perex' => 'Naša sieť odborníkov sa rozrastá o ďalšie mená z oblasti marketingu a práva pre technologické firmy.',
                'published_at' => $date,
                'user_id' => 3,
                'image_id' => 5,
                'image_description' => 'Noví mentori počas úvodného školenia.',
                'content' => <<<EOT
Kvalitný mentoring je základom úspechu každého začínajúceho projektu. Preto sme sa rozhodli osloviť ďalších expertov, ktorí majú bohaté skúsenosti s budovaním značiek.

Okrem technických zručností teraz vieme našim startupom ponúknuť aj hĺbkové konzultácie v oblasti GDPR, duševného vlastníctva a medzinárodného marketingu.

Zoznam všetkých mentorov je dostupný v našej internej aplikácii. Členovia inkubátora si môžu rezervovať termíny na konzultácie už od nasledujúceho pondelka.
EOT
            ],
            [
                'title' => 'Úspešný projekt od tímu CodeMasters pre firmu TechCorp',
                'perex' => 'Študentský tím CodeMasters úspešne dokončil komplexnú e-commerce platformu pre partnerskú firmu TechCorp Slovakia.',
                'published_at' => $date,
                'user_id' => 2,
                'image_id' => 5,
                'image_description' => 'Tím CodeMasters predvádza demo svojej aplikácie.',
                'content' => <<<EOT
Tím zložený zo študentov tretieho ročníka dokázal za štyri mesiace vyvinúť riešenie, ktoré spĺňa najvyššie priemyselné štandardy. Projekt bol odovzdaný bez jedinej chyby.

TechCorp Slovakia si pochvaľuje najmä agilný prístup tímu a inovatívne riešenie správy skladu, ktoré študenti navrhli nad rámec pôvodného zadania.

Tento úspech otvára dvere ďalším študentským tímom, ktoré majú záujem o prácu na reálnych komerčných projektoch pod záštitou NTI.
EOT
            ]
        ]);
    }
}