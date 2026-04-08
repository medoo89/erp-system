<?php

namespace Database\Seeders;

use App\Models\JobApplicationField;
use App\Models\JobApplicationFieldOption;
use Illuminate\Database\Seeder;

class JobApplicationFieldOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $nationalityField = JobApplicationField::where('field_key', 'nationality')->first();
        $testField = JobApplicationField::where('field_key', 'test')->first();

        if ($nationalityField) {
            $nationalities = [
                ['Afghanistan', 'afghanistan'],
                ['Albania', 'albania'],
                ['Algeria', 'algeria'],
                ['Andorra', 'andorra'],
                ['Angola', 'angola'],
                ['Argentina', 'argentina'],
                ['Armenia', 'armenia'],
                ['Australia', 'australia'],
                ['Austria', 'austria'],
                ['Azerbaijan', 'azerbaijan'],
                ['Bahrain', 'bahrain'],
                ['Bangladesh', 'bangladesh'],
                ['Belgium', 'belgium'],
                ['Benin', 'benin'],
                ['Botswana', 'botswana'],
                ['Brazil', 'brazil'],
                ['Bulgaria', 'bulgaria'],
                ['Cameroon', 'cameroon'],
                ['Canada', 'canada'],
                ['Chad', 'chad'],
                ['China', 'china'],
                ['Colombia', 'colombia'],
                ['Congo', 'congo'],
                ['Croatia', 'croatia'],
                ['Cyprus', 'cyprus'],
                ['Czech Republic', 'czech_republic'],
                ['Denmark', 'denmark'],
                ['Djibouti', 'djibouti'],
                ['Egypt', 'egypt'],
                ['Eritrea', 'eritrea'],
                ['Estonia', 'estonia'],
                ['Ethiopia', 'ethiopia'],
                ['Finland', 'finland'],
                ['France', 'france'],
                ['Gabon', 'gabon'],
                ['Gambia', 'gambia'],
                ['Georgia', 'georgia'],
                ['Germany', 'germany'],
                ['Ghana', 'ghana'],
                ['Greece', 'greece'],
                ['Hungary', 'hungary'],
                ['India', 'india'],
                ['Indonesia', 'indonesia'],
                ['Iran', 'iran'],
                ['Iraq', 'iraq'],
                ['Ireland', 'ireland'],
                ['Italy', 'italy'],
                ['Japan', 'japan'],
                ['Jordan', 'jordan'],
                ['Kazakhstan', 'kazakhstan'],
                ['Kenya', 'kenya'],
                ['Kuwait', 'kuwait'],
                ['Latvia', 'latvia'],
                ['Lebanon', 'lebanon'],
                ['Liberia', 'liberia'],
                ['Libya', 'libya'],
                ['Lithuania', 'lithuania'],
                ['Luxembourg', 'luxembourg'],
                ['Malaysia', 'malaysia'],
                ['Mali', 'mali'],
                ['Malta', 'malta'],
                ['Mauritania', 'mauritania'],
                ['Morocco', 'morocco'],
                ['Mozambique', 'mozambique'],
                ['Namibia', 'namibia'],
                ['Netherlands', 'netherlands'],
                ['New Zealand', 'new_zealand'],
                ['Niger', 'niger'],
                ['Nigeria', 'nigeria'],
                ['Norway', 'norway'],
                ['Oman', 'oman'],
                ['Pakistan', 'pakistan'],
                ['Philippines', 'philippines'],
                ['Poland', 'poland'],
                ['Portugal', 'portugal'],
                ['Qatar', 'qatar'],
                ['Romania', 'romania'],
                ['Russia', 'russia'],
                ['Rwanda', 'rwanda'],
                ['Saudi Arabia', 'saudi_arabia'],
                ['Senegal', 'senegal'],
                ['Serbia', 'serbia'],
                ['Sierra Leone', 'sierra_leone'],
                ['Singapore', 'singapore'],
                ['Slovakia', 'slovakia'],
                ['Slovenia', 'slovenia'],
                ['Somalia', 'somalia'],
                ['South Africa', 'south_africa'],
                ['South Korea', 'south_korea'],
                ['South Sudan', 'south_sudan'],
                ['Spain', 'spain'],
                ['Sudan', 'sudan'],
                ['Sweden', 'sweden'],
                ['Switzerland', 'switzerland'],
                ['Syria', 'syria'],
                ['Tanzania', 'tanzania'],
                ['Thailand', 'thailand'],
                ['Tunisia', 'tunisia'],
                ['Turkey', 'turkey'],
                ['Uganda', 'uganda'],
                ['Ukraine', 'ukraine'],
                ['United Arab Emirates', 'united_arab_emirates'],
                ['United Kingdom', 'united_kingdom'],
                ['United States', 'united_states'],
                ['Yemen', 'yemen'],
                ['Zambia', 'zambia'],
                ['Zimbabwe', 'zimbabwe'],
            ];

            foreach ($nationalities as $index => [$label, $value]) {
                JobApplicationFieldOption::updateOrCreate(
                    [
                        'field_id' => $nationalityField->id,
                        'option_value' => $value,
                    ],
                    [
                        'option_label' => $label,
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }

        if ($testField) {
            $testOptions = [
                ['Yes', 'yes', 1],
                ['No', 'no', 2],
            ];

            foreach ($testOptions as [$label, $value, $order]) {
                JobApplicationFieldOption::updateOrCreate(
                    [
                        'field_id' => $testField->id,
                        'option_value' => $value,
                    ],
                    [
                        'option_label' => $label,
                        'sort_order' => $order,
                    ],
                );
            }
        }
    }
}