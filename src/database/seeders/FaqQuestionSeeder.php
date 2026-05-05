<?php

namespace Database\Seeders;

use App\Models\FaqQuestion;
use Illuminate\Database\Seeder;

class FaqQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question' => 'Ako si vytvorím nový účet?',
                'answer' => 'Kliknite na tlačidlo "Registrácia" v pravom hornom rohu a vyplňte formulár.',
                'type' => 'A',
            ],
            [
                'question' => 'Je používanie služby bezplatné?',
                'answer' => 'Základná verzia je zdarma, prémiové funkcie sú spoplatnené podľa cenníka.',
                'type' => 'A',
            ],
            [
                'question' => 'Zabudol som heslo, čo mám robiť?',
                'answer' => 'Na prihlasovacej stránke kliknite na "Zabudnuté heslo" a postupujte podľa inštrukcií v e-maile.',
                'type' => 'A',
            ],
            [
                'question' => 'Kde nájdem svoje faktúry?',
                'answer' => 'Všetky faktúry sú dostupné v sekcii Moje nastavenia -> Fakturácia.',
                'type' => 'B',
            ],
            [
                'question' => 'Aké platobné metódy podporujete?',
                'answer' => 'Prijímame platobné karty Visa, Mastercard a prevod na bankový účet.',
                'type' => 'B',
            ],
            [
                'question' => 'Je možné predplatné kedykoľvek zrušiť?',
                'answer' => 'Áno, predplatné môžete zrušiť v nastaveniach profilu bez akýchkoľvek sankcií.',
                'type' => 'B',
            ],
            [
                'question' => 'Môžem zmeniť svoju e-mailovú adresu?',
                'answer' => 'Áno, e-mail si môžete zmeniť v sekcii Úprava profilu po overení pôvodného hesla.',
                'type' => 'A',
            ],
            [
                'question' => 'Ponúkate množstevné zľavy?',
                'answer' => 'Áno, pre tímy nad 10 osôb ponúkame individuálne zľavy. Kontaktujte našu podporu.',
                'type' => 'B',
            ],
            [
                'question' => 'Ako kontaktovať technickú podporu?',
                'answer' => 'Sme vám k dispozícii na e-maile support@priklad.sk alebo cez live chat.',
                'type' => 'A',
            ],
            [
                'question' => 'Kde sa ukladajú moje dáta?',
                'answer' => 'Vaše údaje sú bezpečne uložené v datacentrách v rámci EÚ v súlade s GDPR.',
                'type' => 'B',
            ],
        ];

        foreach ($questions as $q) {
            FaqQuestion::create($q);
        }
    }
}