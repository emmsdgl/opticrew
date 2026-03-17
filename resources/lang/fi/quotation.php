<?php

return [

    // Page
    'title' => 'Tarjouspyyntö',

    // Breadcrumb / Steps Navigation
    'breadcrumb' => [
        'service' => 'Palvelu',
        'property' => 'Kiinteistö',
        'contact' => 'Yhteystiedot',
    ],

    // Hero / Left Panel
    'hero' => [
        'heading_prefix' => 'Saat',
        'heading_highlight' => 'ilmaisen siivous',
        'heading_suffix' => 'tarjouksen heti',
        'subheading' => 'Ei piilokustannuksia. Ei sitoumuksia',
        'description' => 'Saat räätälöidyn tarjouksen ja laske palvelun arvioitu hinta',
        'description_bold' => 'tilasi mukaan',
    ],

    // Scroll Tooltip
    'scroll_tooltip' => 'Vieritä nähdäksesi lisää',

    // Step 1: Service Information
    'step1' => [
        'title' => 'Vaihe 1:',
        'title_highlight' => 'Palvelutiedot',
        'subtitle' => 'Kerro meille siivoistarpeistasi',

        // Booking Type
        'booking_type_label' => 'Varaustyyppi',
        'personal' => 'Henkilökohtainen',
        'personal_description' => 'Etsin yksittäistä henkilöä tai pientä tiimiä asuntooni',
        'company' => 'Yritys',
        'company_description' => 'Jos varaat yrityksen puolesta useamman työntekijän siivoukseen',

        // Cleaning Services (Personal)
        'cleaning_service_label' => 'Siivouspalvelun tyyppi',
        'deep_cleaning' => 'Perusteellinen siivous',
        'deep_cleaning_desc' => 'Huolellinen, perusteellinen siivous, joka puhdistaa lian ja tahrat vaikeapääsyisistä paikoista.',
        'daily_room_cleaning' => 'Päivittäinen huonesiivous',
        'daily_room_cleaning_desc' => 'Täydellinen huoneen virkistys vierasmajoituksiin räätälöitynä.',
        'snowout_cleaning' => 'Lumenpuhdistussiivous',
        'snowout_cleaning_desc' => 'Kausittainen palvelu, joka keskittyy lumen ja jään poistamiseen mökkien kulkuväyliltä turvallisuuden ja esteettömyyden takaamiseksi.',
        'light_daily_cleaning' => 'Kevyt päivittäinen siivous',
        'light_daily_cleaning_desc' => 'Rutiiniylläpito, joka pitää tilat raikkaina ja edustavina.',
        'full_daily_cleaning' => 'Täysi päivittäinen siivous',
        'full_daily_cleaning_desc' => 'Kattava siivouspalvelu, joka kattaa kaikki alueet optimaalisen hygienian ja esitettävyyden takaamiseksi.',

        // Company Service Types
        'service_type_label' => 'Palvelutyyppi',
        'service_type_hint' => 'Valitse kaikki tarvitsemasi palvelut',
        'hotel_rooms_cleaning' => 'Hotellihuoneiden siivous',
        'cabins' => 'Mökit',
        'cottages' => 'Loma-asunnot',
        'igloos' => 'Iglut',
        'restaurant' => 'Ravintola',
        'reception' => 'Vastaanotto',
        'saunas' => 'Saunat',
        'hallway' => 'Käytävä',
        'snowout' => 'Lumenpuhdistus',

        // Date and Duration
        'date_of_service' => 'Palvelun päivämäärä',
        'date_placeholder' => 'Mikä on toivottu päivämäärä?',
        'duration_of_service' => 'Palvelun kesto',

        // Urgency
        'urgency_label' => 'Kiireellisyys',
        'same_day' => 'Samana päivänä (24h sisällä)',
        'same_day_desc' => 'Kiireellinen siivouspalvelu saatavilla seuraavan 24 tunnin sisällä välittömiin tarpeisiin.',
        'tomorrow' => 'Huomenna / Seuraava päivä',
        'tomorrow_desc' => 'Varaa siivouspalvelu seuraavalle päivälle haluamaasi aikaan.',
        'this_week' => 'Tällä viikolla (2–5 päivän sisällä)',
        'this_week_desc' => 'Varaa siivouspalvelu kuluvan viikon aikana sinulle sopivaan aikaan.',
        'next_week' => 'Ensi viikolla (5–10 päivän sisällä)',
        'next_week_desc' => 'Suunnittele eteenpäin ja varaa siivous tulevalle viikolle.',
        'this_month' => 'Tämän kuukauden aikana',
        'this_month_desc' => 'Joustava aikatauluvaihtoehto milloin tahansa kuluvan kuukauden aikana.',
        'recurring' => 'Toistuva siivous',
        'recurring_desc' => 'Aseta säännöllinen siivouspalvelu viikottain, joka toinen viikko tai kuukausittain.',
    ],

    // Step 2: Property Information
    'step2' => [
        'title' => 'Vaihe 2:',
        'title_highlight' => 'Kiinteistötiedot',
        'subtitle' => 'Kerro meille kiinteistösi tiedot',

        // Property Types
        'property_type_label' => 'Kiinteistötyyppi',
        'apartment' => 'Kerrostaloasunto',
        'apartment_desc' => 'Yksikerroksinen asuinyksikkö rakennuksessa',
        'detached_house' => 'Omakotitalo',
        'detached_house_desc' => 'Erillinen talo',
        'semi_detached' => 'Paritalo / Kaksikerroksinen',
        'semi_detached_desc' => 'Kaksi yhteensä rakennettua taloa, joilla on yhteinen seinä',
        'townhouse' => 'Rivitalo',
        'townhouse_desc' => 'Sarja yhteensä rakennettuja koteja',
        'student_apartment' => 'Opiskelija-asunto',
        'student_apartment_desc' => 'Jaettu yksikkö, pienemmät huoneet',
        'summer_cottage' => 'Kesämökki (Mökki)',
        'summer_cottage_desc' => 'Kausiluonteinen tai loma-asunto',
        'studio' => 'Yksiö / Pieni asunto',
        'studio_desc' => 'Yhden huoneen asunnot',
        'office' => 'Toimisto / Työtila',
        'office_desc' => 'Liiketilat',
        'retail' => 'Myymälä / Kauppa',
        'retail_desc' => 'Myymälätilojen siivous',
        'hotel' => 'Hotelli / Airbnb / Majoitus',
        'hotel_desc' => 'Lyhytaikaisen majoituksen siivous',
        'warehouse' => 'Varasto / Säilytystila',
        'warehouse_desc' => 'Suuri säilytysalue, vähemmän materiaaleja ja huonekaluja',
        'clinic' => 'Klinikka / Terveydenhuoltolaitos',
        'clinic_desc' => 'Hygieniastandardit vaaditaan',
        'factory' => 'Tehdas / Teollisuustila',
        'factory_desc' => 'Raskaan käytön alue',
        'school' => 'Koulu / Yliopisto',
        'school_desc' => 'Suuret tilat luokkahuoneilla',
        'public_building' => 'Julkinen rakennus / Kunnanvirasto',
        'public_building_desc' => 'Hallinto- ja kunnallisrakennukset',
        'gym' => 'Kuntosali / Liikuntakeskus',
        'gym_desc' => 'Laitteiden ja pukuhuoneiden puhdistus',

        // Property Details
        'number_of_floors' => 'Kerrosten lukumäärä',
        'number_of_rooms' => 'Huoneiden lukumäärä',
        'number_of_people_per_room' => 'Henkilöiden lukumäärä per huone',
        'floor_area_value' => 'Lattiapinta-alan arvo',
        'units_label' => 'Yksiköt',
        'units_placeholder' => 'esim. m² / sqm, sq...',

        // Area Units
        'sqm' => 'Neliömetri (m² / sqm)',
        'sqft' => 'Neliöjalka (sq ft / ft²)',
        'sqyd' => 'Neliöjaardi (yd²)',
        'are' => 'Aari (a)',
        'hectare' => 'Hehtaari (ha)',
        'sqin' => 'Neliötuuma (in²)',

        // Location
        'property_location' => 'Kiinteistön sijainti',
    ],

    // Location Picker Modal
    'location_modal' => [
        'title' => 'Valitse kiinteistön sijainti',
        'search_placeholder' => 'Hae sijaintia...',
        'selected_location' => 'Valittu sijainti:',
        'no_location_selected' => 'Sijaintia ei valittu',
        'cancel' => 'Peruuta',
        'confirm_location' => 'Vahvista sijainti',
    ],

    // Step 3: Contact Information
    'step3' => [
        'title' => 'Vaihe 3:',
        'title_highlight' => 'Yhteystiedot',
        'subtitle' => 'Anna yhteystietosi',

        'company_name' => 'Yrityksen nimi',
        'company_name_placeholder' => 'Syötä yrityksen nimi',
        'contact_person_name' => 'Yhteyshenkilön nimi',
        'client_name' => 'Asiakkaan nimi',
        'name_placeholder' => 'Syötä nimesi',
        'phone_number' => 'Puhelinnumero',
        'phone_placeholder' => '+358 40 123 4567',
        'email_address' => 'Sähköpostiosoite',
        'email_placeholder' => 'Mihin lähetämme tarjouksen?',
        'submit_button' => 'Pyydä hintatarjous',
    ],

    // Form Validation & Submission Messages
    'messages' => [
        'submitting' => 'Lähetetään...',
        'location_required' => 'Valitse ensin sijainti kartalta.',
        'location_required_title' => 'Sijainti vaaditaan',
        'location_found' => 'Osoitekentät on täytetty automaattisesti.',
        'location_found_title' => 'Sijainti löydetty',
        'geocoding_failed' => 'Osoitteen hakeminen koordinaateista epäonnistui. Yritä uudelleen.',
        'geocoding_failed_title' => 'Geokoodaus epäonnistui',
        'geocoder_error' => 'Geokoodaajaa ei alustettu. Yritä uudelleen.',
        'geocoder_error_title' => 'Geokoodausvirhe',
        'getting_location' => 'Haetaan sijaintiasi...',
        'location_error_title' => 'Sijaintivirhe',
        'location_error_permission' => 'Salli sijainnin käyttö selaimesi asetuksista.',
        'location_error_unavailable' => 'Sijaintitiedot eivät ole saatavilla.',
        'location_error_timeout' => 'Sijaintipyyntö aikakatkaistiin.',
        'location_error_unknown' => 'Tuntematon virhe tapahtui.',
        'location_error_prefix' => 'Sijaintiasi ei voitu hakea.',
        'geolocation_not_supported_title' => 'Ei tuettu',
        'geolocation_not_supported' => 'Selaimesi ei tue paikannusta.',
        'booking_type_required_title' => 'Varaustyyppi vaaditaan',
        'booking_type_required' => 'Valitse varaustyyppi (Henkilökohtainen tai Yritys).',
        'missing_info_title' => 'Puuttuvia tietoja',
        'missing_info' => 'Täytä kaikki vaaditut yhteystiedot.',
        'submission_success_title' => 'Tarjouspyyntö lähetetty',
        'submission_success' => 'Tarjouspyyntösi on lähetetty onnistuneesti! Otamme sinuun yhteyttä pian.',
        'validation_errors_title' => 'Tarkistusvirheet',
        'validation_errors_prefix' => 'Korjaa seuraavat virheet:',
        'submission_failed_title' => 'Lähetys epäonnistui',
        'submission_failed' => 'Tarjouspyynnön lähettäminen epäonnistui. Yritä uudelleen.',
        'submission_error_title' => 'Lähetysvirhe',
        'submission_error' => 'Pyyntöä lähetettäessä tapahtui virhe. Tarkista internet-yhteytesi ja yritä uudelleen.',
    ],

    // Common
    'required_mark' => '*',

];
