<?php

namespace Database\Seeders;

use App\Models\CorporateContact;
use App\Models\User;
use Illuminate\Database\Seeder;

class CorporateContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'PT. Teknologi Maju Indonesia',
                'email' => 'hr@tekno-maju.co.id',
                'message' => 'Kami tertarik untuk bermitra dalam program scholarship untuk mahasiswa IT. Mohon informasi lebih lanjut mengenai program kerjasama yang tersedia.',
            ],
            [
                'name' => 'CV. Digital Kreatif',
                'email' => 'info@digitalkreatif.com',
                'message' => 'Ingin mengetahui lebih lanjut tentang program mentoring untuk karyawan kami di bidang web development dan UI/UX design.',
            ],
            [
                'name' => 'Yayasan Pendidikan Nusantara',
                'email' => 'partnership@ypn.or.id',
                'message' => 'Kami ingin mendiskusikan kemungkinan kerjasama untuk menyediakan beasiswa bagi siswa kurang mampu yang berprestasi.',
            ],
            [
                'name' => 'PT. Inovasi Digital Solutions',
                'email' => 'corporate@inovasi-digital.com',
                'message' => 'Tertarik untuk membuat program bootcamp khusus untuk fresh graduate. Mohon hubungi kami untuk diskusi lebih lanjut.',
            ],
            [
                'name' => 'Startup Hub Indonesia',
                'email' => 'hello@startuphub.id',
                'message' => 'Ingin berkolaborasi dalam program mentoring untuk startup founders dan tech talents.',
            ],
        ];

        foreach ($contacts as $contactData) {
            CorporateContact::create($contactData);
        }

        $this->command->info('CorporateContact seeder completed successfully!');
    }
}
