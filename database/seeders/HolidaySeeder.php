<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Models\Tenant;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;
        $tenantId = $tenant->id;
        $year     = date('Y');

        Holiday::where('tenant_id', $tenantId)->delete();

        $holidays = [
            // National / Public Holidays India
            ['name'=>'New Year',                  'date'=>"{$year}-01-01", 'type'=>'public',  'description'=>'New Year Day',                        'recurring'=>true],
            ['name'=>'Republic Day',               'date'=>"{$year}-01-26", 'type'=>'public',  'description'=>'Indian Republic Day',                 'recurring'=>true],
            ['name'=>'Holi',                       'date'=>"{$year}-03-25", 'type'=>'public',  'description'=>'Festival of Colours',                 'recurring'=>false],
            ['name'=>'Good Friday',                'date'=>"{$year}-04-18", 'type'=>'public',  'description'=>'Good Friday – Christian observance',  'recurring'=>false],
            ['name'=>'Dr. Ambedkar Jayanti',       'date'=>"{$year}-04-14", 'type'=>'public',  'description'=>'B.R. Ambedkar birth anniversary',     'recurring'=>true],
            ['name'=>'Maharashtra Day',            'date'=>"{$year}-05-01", 'type'=>'public',  'description'=>'Maharashtra Foundation Day',          'recurring'=>true],
            ['name'=>'Independence Day',           'date'=>"{$year}-08-15", 'type'=>'public',  'description'=>'Indian Independence Day',              'recurring'=>true],
            ['name'=>'Janmashtami',                'date'=>"{$year}-08-26", 'type'=>'public',  'description'=>'Lord Krishna birth anniversary',      'recurring'=>false],
            ['name'=>'Gandhi Jayanti',             'date'=>"{$year}-10-02", 'type'=>'public',  'description'=>'Mahatma Gandhi birth anniversary',    'recurring'=>true],
            ['name'=>'Dussehra (Vijayadashami)',   'date'=>"{$year}-10-12", 'type'=>'public',  'description'=>'Victory of good over evil',           'recurring'=>false],
            ['name'=>'Diwali (Laxmi Puja)',        'date'=>"{$year}-10-20", 'type'=>'public',  'description'=>'Festival of Lights – main day',       'recurring'=>false],
            ['name'=>'Diwali Holiday',             'date'=>"{$year}-10-21", 'type'=>'company', 'description'=>'Company holiday around Diwali',       'recurring'=>false],
            ['name'=>'Guru Nanak Jayanti',         'date'=>"{$year}-11-15", 'type'=>'public',  'description'=>'Birth anniversary of Guru Nanak',     'recurring'=>false],
            ['name'=>'Christmas Day',              'date'=>"{$year}-12-25", 'type'=>'public',  'description'=>'Christmas Day',                       'recurring'=>true],
            // Company specific
            ['name'=>'Company Foundation Day',     'date'=>"{$year}-07-15", 'type'=>'company', 'description'=>'TechFlow Solutions founding anniversary','recurring'=>true],
            ['name'=>'Annual Day Off',             'date'=>"{$year}-12-31", 'type'=>'company', 'description'=>'Year-end company closure',             'recurring'=>true],
            // Optional / Regional
            ['name'=>'Ugadi / Gudi Padwa',         'date'=>"{$year}-03-30", 'type'=>'optional','description'=>'Telugu & Marathi New Year',           'recurring'=>false],
            ['name'=>'Eid ul-Fitr',                'date'=>"{$year}-04-10", 'type'=>'optional','description'=>'Eid al-Fitr – End of Ramadan',        'recurring'=>false],
        ];

        foreach ($holidays as $h) {
            Holiday::create(array_merge($h, ['tenant_id' => $tenantId]));
        }

        $this->command->info('✅ 18 Indian holidays seeded (National + Company + Optional).');
    }
}