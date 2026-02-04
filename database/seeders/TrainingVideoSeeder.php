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
                'description' => 'Learn the proper techniques for mopping different floor types effectively while maintaining safety standards.',
                'description_fi' => 'Opi oikeat tekniikat erilaisten lattiapintojen moppaamiseen tehokkaasti turvallisuusstandardeja noudattaen.',
                'video_id' => 'ZXsQAXx_ao0', // Relaxing piano music (for testing)
                'platform' => 'youtube',
                'duration' => '5:30',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'cleaning_techniques',
                'title' => 'Window Cleaning Best Practices',
                'title_fi' => 'Ikkunanpesun parhaat käytännöt',
                'description' => 'Master streak-free window cleaning using professional tools and techniques.',
                'description_fi' => 'Hallitse raidattoman ikkunanpesun ammattilaistyökaluilla ja -tekniikoilla.',
                'video_id' => 'jNQXAC9IVRw', // Me at the zoo (first YT video)
                'platform' => 'youtube',
                'duration' => '7:15',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'category' => 'cleaning_techniques',
                'title' => 'Stain Removal Guide',
                'title_fi' => 'Tahranpoistoopas',
                'description' => 'Comprehensive guide to removing various types of stains from different surfaces.',
                'description_fi' => 'Kattava opas erilaisten tahrojen poistamiseen eri pinnoilta.',
                'video_id' => '9bZkp7q19f0', // Gangnam Style
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
                'description' => 'Learn how to lift heavy objects safely to prevent back injuries and strain.',
                'description_fi' => 'Opi nostamaan raskaita esineitä turvallisesti selkävammojen ja rasituksen välttämiseksi.',
                'video_id' => 'kJQP7kiw5Fk', // Despacito
                'platform' => 'youtube',
                'duration' => '4:20',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'body_safety',
                'title' => 'Ergonomic Cleaning Postures',
                'title_fi' => 'Ergonomiset siivousasennot',
                'description' => 'Maintain proper posture while cleaning to reduce fatigue and prevent injuries.',
                'description_fi' => 'Säilytä oikea ryhti siivouksen aikana väsymyksen vähentämiseksi ja vammojen ehkäisemiseksi.',
                'video_id' => 'fJ9rUzIMcZQ', // Bohemian Rhapsody
                'platform' => 'youtube',
                'duration' => '6:00',
                'required' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'body_safety',
                'title' => 'Personal Protective Equipment (PPE)',
                'title_fi' => 'Henkilönsuojaimet (PPE)',
                'description' => 'Understanding when and how to use personal protective equipment properly.',
                'description_fi' => 'Ymmärrä milloin ja miten henkilönsuojaimia käytetään oikein.',
                'video_id' => 'JGwWNGJdvx8', // Shape of You
                'platform' => 'youtube',
                'duration' => '5:45',
                'required' => true,
                'sort_order' => 3,
            ],

            // Hazard Prevention
            [
                'category' => 'hazard_prevention',
                'title' => 'Slip and Fall Prevention',
                'title_fi' => 'Liukastumis- ja kaatumisonnettomuuksien ehkäisy',
                'description' => 'Identify and prevent common slip and fall hazards in the workplace.',
                'description_fi' => 'Tunnista ja ehkäise yleisiä liukastumis- ja kaatumisriskejä työpaikalla.',
                'video_id' => 'OPf0YbXqDm0', // Uptown Funk
                'platform' => 'youtube',
                'duration' => '5:15',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'hazard_prevention',
                'title' => 'Electrical Safety Basics',
                'title_fi' => 'Sähköturvallisuuden perusteet',
                'description' => 'Essential electrical safety knowledge for cleaning professionals.',
                'description_fi' => 'Olennainen sähköturvallisuustieto siivousalan ammattilaisille.',
                'video_id' => 'CevxZvSJLk8', // Roar
                'platform' => 'youtube',
                'duration' => '4:30',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'category' => 'hazard_prevention',
                'title' => 'Emergency Procedures',
                'title_fi' => 'Hätätoimenpiteet',
                'description' => 'Know what to do in case of emergencies including fire, medical, and evacuation.',
                'description_fi' => 'Tiedä mitä tehdä hätätilanteissa, mukaan lukien tulipalot, lääketieteelliset tilanteet ja evakuoinnit.',
                'video_id' => 'hT_nvWreIhg', // Counting Stars
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
                'description' => 'Learn how to safely handle, store, and dispose of cleaning chemicals.',
                'description_fi' => 'Opi käsittelemään, säilyttämään ja hävittämään puhdistuskemikaaleja turvallisesti.',
                'video_id' => 'RgKAFK5djSk', // See You Again
                'platform' => 'youtube',
                'duration' => '6:45',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'chemical_safety',
                'title' => 'Reading Chemical Labels (SDS)',
                'title_fi' => 'Kemikaalimerkkien lukeminen (SDS)',
                'description' => 'Understanding Safety Data Sheets and chemical warning labels.',
                'description_fi' => 'Käyttöturvallisuustiedotteiden ja kemiallisten varoitusmerkkien ymmärtäminen.',
                'video_id' => 'e-ORhEE9VVg', // Blank Space
                'platform' => 'youtube',
                'duration' => '5:00',
                'required' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'chemical_safety',
                'title' => 'Chemical Mixing Safety',
                'title_fi' => 'Kemikaalien sekoitusturvallisuus',
                'description' => 'Learn which chemicals should never be mixed and why.',
                'description_fi' => 'Opi mitä kemikaaleja ei saa koskaan sekoittaa ja miksi.',
                'video_id' => 'lp-EO5I60KA', // Shake It Off
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
