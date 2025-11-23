<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get corporate users to assign as owners of organizations
        $corporateUsers = User::where('role', 'corporate')->get();
        
        $organizations = [
            [
                'name' => 'Google Indonesia',
                'type' => 'company',
                'description' => 'Google mission is to organize the world information and make it universally accessible and useful.',
                'location' => 'Jakarta, Indonesia',
                'website' => 'https://about.google/',
                'contact_email' => 'contact@google.com',
                'phone' => '+62 21 2358 8000',
                'founded_year' => 1998,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg',
            ],
            [
                'name' => 'LPDP (Lembaga Pengelola Dana Pendidikan)',
                'type' => 'government',
                'description' => 'Lembaga Pengelola Dana Pendidikan yang berada di bawah Kementerian Keuangan Republik Indonesia.',
                'location' => 'Jakarta, Indonesia',
                'website' => 'https://lpdp.kemenkeu.go.id/',
                'contact_email' => 'bantuan@lpdp.kemenkeu.go.id',
                'phone' => '1500652',
                'founded_year' => 2012,
                'logo_url' => 'https://lpdp.kemenkeu.go.id/static/images/logo-lpdp.png',
            ],
            [
                'name' => 'Universitas Indonesia',
                'type' => 'university',
                'description' => 'A modern, comprehensive, open-minded, multi-culture, and humanism campus that covers wide arrays of scientific disciplines.',
                'location' => 'Depok, Indonesia',
                'website' => 'https://www.ui.ac.id/',
                'contact_email' => 'humas-ui@ui.ac.id',
                'phone' => '+62 21 786 7222',
                'founded_year' => 1849,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Makara_of_Universitas_Indonesia.svg/1200px-Makara_of_Universitas_Indonesia.svg.png',
            ],
            [
                'name' => 'Ruangguru',
                'type' => 'company',
                'description' => 'The largest education technology company in Southeast Asia.',
                'location' => 'Jakarta, Indonesia',
                'website' => 'https://ruangguru.com/',
                'contact_email' => 'info@ruangguru.com',
                'phone' => '+62 21 2854 3000',
                'founded_year' => 2014,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Ruangguru_Logo.png/640px-Ruangguru_Logo.png',
            ],
            [
                'name' => 'Bank Rakyat Indonesia (BRI)',
                'type' => 'company',
                'description' => 'Salah satu bank milik pemerintah yang terbesar di Indonesia.',
                'location' => 'Jakarta, Indonesia',
                'website' => 'https://bri.co.id/',
                'contact_email' => 'callbri@bri.co.id',
                'phone' => '14017',
                'founded_year' => 1895,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/1200px-BANK_BRI_logo.svg.png',
            ],
        ];

        foreach ($organizations as $index => $orgData) {
            // Assign a corporate user if available, cycling through them
            if ($corporateUsers->count() > 0) {
                $orgData['user_id'] = $corporateUsers[$index % $corporateUsers->count()]->id;
            }
            
            Organization::create($orgData);
        }

        $this->command->info('Organization seeder completed successfully!');
    }
}
