<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Palvelut-sivun käännökset
    |--------------------------------------------------------------------------
    */

    // Sivun otsikko
    'title' => 'Palvelut',

    // ===== OSIO 1: DIAKARUSELLI =====
    'carousel' => [
        'slide1_title' => 'Tilauksesta tuoreeksi — Me saamme sen loistamaan',
        'slide1_subtitle' => 'Mitä tarjoamme · Palvelumme',
        'slide1_description' => 'Tutustu ammattimaisiin siivouspalveluihimme, jotka on suunniteltu saamaan kotisi tai yrityksesi loistamaan',
        'slide1_button' => 'Tutustu palveluihin',

        'slide2_title' => 'Perusteellinen siivous',
        'slide2_price' => '€ 120 - € 200',
        'slide2_description' => 'Perusteellinen siivous ylhäältä alas, joka puuttuu likaan ja pölyyn vaikeapääsyisissä paikoissa...',
        'slide2_badges' => ['Liinavaatteiden pesu', 'WC:n puhdistus', 'Ikkunoiden pesu', 'Sisustuksen järjestely'],
        'slide2_button' => 'Varaa palvelu',

        'slide3_title' => 'Täysi päivittäissiivous',
        'slide3_price' => '€ 80 - € 150',
        'slide3_description' => 'Täydellinen huoneen virkistys räätälöity vierasmajoitukseen...',
        'slide3_badges' => ['Kevyt siivous', 'Roskien tyhjennys', 'Lakaiseminen ja pölyttäminen', 'Tarvikkeiden vaihto', 'Tarvikkeiden täydennys'],
        'slide3_button' => 'Varaa palvelu',

        'slide4_title' => 'Lähtösiivous',
        'slide4_price' => '€ 150 - € 250',
        'slide4_description' => 'Kokonaisvaltainen siivouspalvelu loma-asuntojen vierasvaihdosten välillä...',
        'slide4_badges' => ['Perusteellinen siivous', 'Täysi desinfiointi', 'Liinavaatteiden vaihto', 'Täydellinen nollaus'],
        'slide4_button' => 'Varaa palvelu',

        'slide5_title' => 'Täysi päivittäissiivous',
        'slide5_price' => '€ 200 - € 300',
        'slide5_description' => 'Kokonaisvaltainen päivittäissiivouspalvelu kattaen kaikki alueet...',
        'slide5_badges' => ['Täysi palvelu', 'Kaikki huoneet', 'Täysi ylläpito', 'Premium-hoito'],
        'slide5_button' => 'Varaa palvelu',
    ],

    // ===== OSIO 2: LAAJENEVAT KORTIT =====
    'expanding' => [
        'card1_title' => 'Palvelemme koteja ja yrityksiä näillä alueilla',
        'card1_description' => 'Kokonaisvaltainen siivouspalvelu loma-asuntojen vierasvaihdosten välillä.',
        'card1_badges' => ['Inari', 'Lappi', 'Saariselkä'],

        'card2_title' => 'Inarin kunta',
        'card2_description' => 'Tämä on yksi Lapin kunnista, pohjoisimmalla alueella, joka kattaa noin kolmanneksen maan pohjoisosasta napapiirin yläpuolella.',
        'card2_badges' => ['Saariselkä', 'Nellim', 'Lemmenjoki', 'Ivalo'],

        'card3_title' => 'Saariselkä',
        'card3_description' => 'Lapissa, missä hohtavat revontulet tanssivat lumisten tunturien yllä, porot vaeltavat vapaasti ja seikkailu odottaa ympäri vuoden hiihtäen, vaeltaen ja arktisen erämaan kauneutta ihaillen.',
        'card3_badges' => ['Utsjoki', 'Inari', 'Saariselkä', 'Kaamanen'],

        'card4_title' => 'Lapin alue',
        'card4_description' => 'Lapin pääkaupunki ja joulupukin virallinen kotikaupunki, tunnettu vilkkaasta kaupunkielämästään ja revontulimaisemistaan.',
        'card4_badges' => ['Utsjoki', 'Inari', 'Saariselkä', 'Kaamanen'],
    ],

    // Osio 2 otsikko
    'section2' => [
        'subtitle' => 'Tutustu palveluihimme',
        'title_before' => 'Vain',
        'title_highlight' => 'klikkauksen päässä',
        'footer_text' => 'Palvelemme ylpeästi koteja ja yrityksiä kaikkialla Suomessa, tuomme ammattimaisen ja luotettavan siivouksen ovellesi',
    ],

    // ===== OSIO 3: HINNOITTELU =====
    'section3' => [
        'subtitle' => 'Hinnoittelu',
        'title_before' => 'Löydä',
        'title_highlight' => 'paras suunnitelma',
        'title_after' => 'siivoustarpeisiisi.',
        'description' => 'Joustavat siivoussuunnitelmat, jotka pitävät tilasi moitteettomana — omalla aikataulullasi, omalla tavallasi.',
    ],

    'pricing' => [
        // Kortti 1: Sopimuspohjainen päivittäissiivous
        'card1_title' => 'Sopimuspohjainen päivittäissiivous',
        'card1_price' => '$29',
        'card1_period' => '/vuosi',
        'card1_description' => 'Ihanteellinen satunnaiseen kotisiivoukseen ja pyyntöihin.',
        'card1_features' => [
            'Päivittäinen huoneiden siivous',
            'Perussiivoustarvikkeet',
            'Sopimusperusteinen hinnoittelu',
        ],
        'card1_button' => 'Osta',

        // Kortti 2: Jaksottainen siivous
        'card2_title' => 'Jaksottainen siivous',
        'card2_price' => '$59',
        'card2_period' => '/kuukausi',
        'card2_description' => 'Erinomainen säännölliseen koti- tai toimistosiivoukseen.',
        'card2_features' => [
            'Viikoittainen/kahden viikon välein -palvelu',
            'Premium-siivoustarvikkeet',
            'Joustava aikataulutus',
        ],
        'card2_button' => 'Osta',

        // Kortti 3: Päivystyssiivous
        'card3_title' => 'Päivystyssiivous',
        'card3_price' => '$99',
        'card3_period' => '/viikko',
        'card3_description' => 'Kokonaisvaltainen siivouspalvelu 24/7-tuella ja räätälöinnillä.',
        'card3_features' => [
            '24/7 tilauspalvelu',
            'Ensisijainen tuki',
            'Räätälöidyt siivoussuunnitelmat',
        ],
        'card3_button' => 'Osta',
    ],

];
