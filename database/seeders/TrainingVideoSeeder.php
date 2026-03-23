<?php

namespace Database\Seeders;

use App\Models\TrainingVideo;
use Illuminate\Database\Seeder;

class TrainingVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            // Cleaning Techniques
            [
                'category' => 'cleaning_techniques',
                'title' => 'Professional Floor Mopping Techniques',
                'title_fi' => 'Ammattimaiset lattian moppaustekniikat',
                'description' => 'Learn the proper techniques for mopping different floor types effectively. This includes choosing the right mop and cleaning solution, using the correct motion patterns, and maintaining safety standards to avoid slips while working.',
                'description_fi' => 'Opi oikeat tekniikat erilaisten lattiapintojen moppaamiseen tehokkaasti. Tämä sisältää oikean mopin ja puhdistusaineen valinnan, oikeiden liikeratojen käyttämisen sekä turvallisuusstandardien noudattamisen liukastumisten välttämiseksi työn aikana.',
                'video_id' => 'ZXsQAXx_ao0',
                'platform' => 'youtube',
                'duration' => '5:30',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'cleaning_techniques',
                'title' => 'Window Cleaning Best Practices',
                'title_fi' => 'Ikkunanpesun parhaat käytännöt',
                'description' => 'Master streak-free window cleaning using professional tools and techniques. You will learn how to use squeegees, microfiber cloths, and the right cleaning solutions to achieve spotless results every time.',
                'description_fi' => 'Hallitse raidaton ikkunanpesu ammattilaistyökaluilla ja -tekniikoilla. Opit käyttämään lastoja, mikrokuituliinoja ja oikeita puhdistusaineita saavuttaaksesi täydellisen lopputuloksen joka kerta.',
                'video_id' => 'jNQXAC9IVRw',
                'platform' => 'youtube',
                'duration' => '7:15',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'category' => 'cleaning_techniques',
                'title' => 'Stain Removal Guide',
                'title_fi' => 'Tahranpoistoopas',
                'description' => 'Comprehensive guide to removing various types of stains from different surfaces. Covers coffee, ink, grease, and wine stains on carpets, upholstery, and hard floors with step-by-step instructions.',
                'description_fi' => 'Kattava opas erilaisten tahrojen poistamiseen eri pinnoilta. Käsittelee kahvi-, muste-, rasva- ja viiniläiskiä matoissa, verhoiluissa ja kovilla lattioilla vaiheittaisten ohjeiden avulla.',
                'video_id' => '9bZkp7q19f0',
                'platform' => 'youtube',
                'duration' => '8:45',
                'required' => true,
                'sort_order' => 3,
            ],

            // Body Safety
            [
                'category' => 'body_safety',
                'title' => 'Proper Lifting Techniques',
                'title_fi' => 'Oikeat nostotekniikat',
                'description' => 'Learn how to lift heavy objects safely and properly so you don\'t pull a muscle, strain your neck, or rupture a disc in your back. Which means you stay on the job, earning your full pay.',
                'description_fi' => 'Opi nostamaan raskaita esineitä turvallisesti ja oikein, jotta et vedä lihasta, rasita niskaasi tai saa välilevyn pullistumaa selkääsi. Näin pysyt työkuntoisena ja ansaitset täyden palkkasi.',
                'video_id' => 'kJQP7kiw5Fk',
                'platform' => 'youtube',
                'duration' => '4:20',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'body_safety',
                'title' => 'Ergonomic Cleaning Postures',
                'title_fi' => 'Ergonomiset siivousasennot',
                'description' => 'Maintain proper posture while cleaning to reduce fatigue and prevent long-term injuries. Learn how to position your body correctly when vacuuming, scrubbing, and reaching high or low surfaces.',
                'description_fi' => 'Säilytä oikea ryhti siivouksen aikana vähentääksesi väsymystä ja ehkäistäksesi pitkäaikaisia vammoja. Opi asettamaan kehosi oikein imuroidessasi, hangatessasi ja kurkottaessasi korkeille tai matalille pinnoille.',
                'video_id' => 'fJ9rUzIMcZQ',
                'platform' => 'youtube',
                'duration' => '6:00',
                'required' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'body_safety',
                'title' => 'Personal Protective Equipment (PPE)',
                'title_fi' => 'Henkilönsuojaimet (PPE)',
                'description' => 'Understanding when and how to use personal protective equipment properly. Covers gloves, goggles, masks, and non-slip footwear to keep you safe from chemicals, dust, and physical hazards.',
                'description_fi' => 'Ymmärrä milloin ja miten henkilönsuojaimia käytetään oikein. Käsittelee käsineitä, suojalaseja, maskeja ja liukumattomia jalkineita, jotka suojaavat sinua kemikaaleilta, pölyltä ja fyysisiltä vaaroilta.',
                'video_id' => 'JGwWNGJdvx8',
                'platform' => 'youtube',
                'duration' => '5:45',
                'required' => true,
                'sort_order' => 3,
            ],

            // Hazard Prevention
            [
                'category' => 'hazard_prevention',
                'title' => 'Slip and Fall Prevention',
                'title_fi' => 'Liukastumis- ja kaatumistapaturmien ehkäisy',
                'description' => 'Identify and prevent common slip and fall hazards in the workplace. Learn to use wet floor signs, choose proper footwear, and maintain clean walkways to protect yourself and others.',
                'description_fi' => 'Tunnista ja ehkäise yleisiä liukastumis- ja kaatumisriskejä työpaikalla. Opi käyttämään märän lattian varoituskylttejä, valitsemaan oikeat jalkineet ja pitämään kulkuväylät puhtaina suojellaksesi itseäsi ja muita.',
                'video_id' => 'OPf0YbXqDm0',
                'platform' => 'youtube',
                'duration' => '5:15',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'hazard_prevention',
                'title' => 'Electrical Safety Basics',
                'title_fi' => 'Sähköturvallisuuden perusteet',
                'description' => 'Essential electrical safety knowledge for cleaning professionals. Learn to identify damaged cords, avoid water near electrical outlets, and report hazards before they cause injury.',
                'description_fi' => 'Olennainen sähköturvallisuustieto siivousalan ammattilaisille. Opi tunnistamaan vaurioituneet johdot, välttämään vettä sähköpistorasioiden lähellä ja ilmoittamaan vaaroista ennen kuin ne aiheuttavat loukkaantumisia.',
                'video_id' => 'CevxZvSJLk8',
                'platform' => 'youtube',
                'duration' => '4:30',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'category' => 'hazard_prevention',
                'title' => 'Emergency Procedures',
                'title_fi' => 'Hätätoimenpiteet',
                'description' => 'Know what to do in case of emergencies including fire, medical incidents, and evacuation. This training covers alarm systems, exit routes, first aid basics, and how to call for help.',
                'description_fi' => 'Tiedä mitä tehdä hätätilanteissa, mukaan lukien tulipalot, lääketieteelliset tapaukset ja evakuointi. Tämä koulutus käsittelee hälytysjärjestelmiä, poistumisreittejä, ensiavun perusteita ja avun hälyttämistä.',
                'video_id' => 'hT_nvWreIhg',
                'platform' => 'youtube',
                'duration' => '7:30',
                'required' => true,
                'sort_order' => 3,
            ],

            // Chemical Safety
            [
                'category' => 'chemical_safety',
                'title' => 'Safe Handling of Cleaning Chemicals',
                'title_fi' => 'Puhdistuskemikaalien turvallinen käsittely',
                'description' => 'Learn how to safely handle, store, and dispose of cleaning chemicals. Includes proper dilution ratios, ventilation requirements, and what to do if chemicals contact your skin or eyes.',
                'description_fi' => 'Opi käsittelemään, säilyttämään ja hävittämään puhdistuskemikaaleja turvallisesti. Sisältää oikeat laimennussuhteet, tuuletusvaatimukset ja toimintaohjeet, jos kemikaaleja joutuu iholle tai silmiin.',
                'video_id' => 'RgKAFK5djSk',
                'platform' => 'youtube',
                'duration' => '6:45',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'chemical_safety',
                'title' => 'Reading Chemical Labels (SDS)',
                'title_fi' => 'Kemikaalimerkkien lukeminen (KTT)',
                'description' => 'Understanding Safety Data Sheets and chemical warning labels. Learn to read hazard symbols, identify risk phrases, and follow safe usage instructions before handling any chemical product.',
                'description_fi' => 'Käyttöturvallisuustiedotteiden ja kemiallisten varoitusmerkkien ymmärtäminen. Opi lukemaan vaarasymboleja, tunnistamaan riskiilmauksia ja noudattamaan turvallisia käyttöohjeita ennen minkään kemiallisen tuotteen käsittelyä.',
                'video_id' => 'e-ORhEE9VVg',
                'platform' => 'youtube',
                'duration' => '5:00',
                'required' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'chemical_safety',
                'title' => 'Chemical Mixing Safety',
                'title_fi' => 'Kemikaalien sekoitusturvallisuus',
                'description' => 'Learn which chemicals should never be mixed and why. Mixing bleach with ammonia or acids can create toxic fumes that cause serious harm. This video teaches you how to stay safe.',
                'description_fi' => 'Opi mitä kemikaaleja ei saa koskaan sekoittaa ja miksi. Valkaisuaineen sekoittaminen ammoniakin tai happojen kanssa voi synnyttää myrkyllisiä kaasuja, jotka aiheuttavat vakavaa haittaa. Tämä video opettaa sinulle turvallisen toimintatavan.',
                'video_id' => 'lp-EO5I60KA',
                'platform' => 'youtube',
                'duration' => '4:15',
                'required' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($videos as $video) {
            TrainingVideo::create($video);
        }
    }
}
