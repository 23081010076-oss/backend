<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scholarships = [
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'LPDP_001',
                'name' => 'LPDP Scholarship Program',
                'description' => 'Beasiswa penuh untuk program master dan doktor di dalam dan luar negeri. Mencakup biaya kuliah, biaya hidup, dan tunjangan penelitian.',
                'benefit' => 'Biaya kuliah penuh, biaya hidup bulanan, tunjangan buku, asuransi kesehatan, tiket pesawat PP',
                'location' => 'Indonesia dan Luar Negeri',
                'status' => 'open',
                'deadline' => '2024-03-31',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'BUMN_002',
                'name' => 'Beasiswa BUMN 2024',
                'description' => 'Program beasiswa dari BUMN untuk mahasiswa berprestasi dengan komitmen bekerja di perusahaan BUMN setelah lulus.',
                'benefit' => 'Biaya kuliah, uang saku bulanan, jaminan kerja di BUMN, pelatihan kepemimpinan',
                'location' => 'Seluruh Indonesia',
                'status' => 'open',
                'deadline' => '2024-02-28',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'DIKTI_003',
                'name' => 'Beasiswa Unggulan Kemendikbud',
                'description' => 'Beasiswa prestasi untuk mahasiswa S1, S2, dan S3 dari Kementerian Pendidikan dan Kebudayaan.',
                'benefit' => 'Biaya pendidikan, biaya hidup, dana penelitian, pembimbingan akademik',
                'location' => 'Universitas Negeri dan Swasta Indonesia',
                'status' => 'open',
                'deadline' => '2024-04-15',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'GOOG_004',
                'name' => 'Google Developer Scholarship',
                'description' => 'Beasiswa khusus untuk pengembangan skills teknologi dan programming dari Google.',
                'benefit' => 'Akses course premium, mentoring dari Google engineers, sertifikat Google',
                'location' => 'Online - Global',
                'status' => 'open',
                'deadline' => '2024-05-30',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'AUST_005',
                'name' => 'Australia Awards Scholarship',
                'description' => 'Beasiswa penuh dari pemerintah Australia untuk program master dan PhD di universitas Australia.',
                'benefit' => 'Biaya kuliah penuh, tunjangan hidup, asuransi kesehatan, tiket pesawat',
                'location' => 'Australia',
                'status' => 'open',
                'deadline' => '2024-04-30',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'CHEVR_006',
                'name' => 'Chevening Scholarship UK',
                'description' => 'Beasiswa bergengsi dari pemerintah Inggris untuk program master satu tahun di UK.',
                'benefit' => 'Biaya kuliah, biaya hidup, tiket pesawat PP, visa allowance',
                'location' => 'United Kingdom',
                'status' => 'coming_soon',
                'deadline' => '2024-11-08',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'FULB_007',
                'name' => 'Fulbright Scholarship',
                'description' => 'Program pertukaran pendidikan dan budaya antara Indonesia dan Amerika Serikat.',
                'benefit' => 'Biaya kuliah, biaya hidup, asuransi kesehatan, program budaya',
                'location' => 'United States',
                'status' => 'coming_soon',
                'deadline' => '2024-06-15',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'ERASM_008',
                'name' => 'Erasmus Mundus Joint Master',
                'description' => 'Beasiswa untuk program master joint di beberapa universitas Eropa.',
                'benefit' => 'Biaya kuliah, monthly allowance, travel costs, visa support',
                'location' => 'European Union',
                'status' => 'open',
                'deadline' => '2024-01-15',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'DAAD_009',
                'name' => 'DAAD Scholarship Germany',
                'description' => 'Beasiswa dari layanan pertukaran akademik Jerman untuk berbagai program studi.',
                'benefit' => 'Monthly stipend, health insurance, travel allowance, German language course',
                'location' => 'Germany',
                'status' => 'open',
                'deadline' => '2024-03-15',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'LOCAL_010',
                'name' => 'Beasiswa Yayasan Pendidikan Indonesia',
                'description' => 'Beasiswa lokal untuk mahasiswa kurang mampu dengan prestasi akademik baik.',
                'benefit' => 'Bantuan biaya kuliah 50%, bimbingan belajar, pelatihan soft skills',
                'location' => 'Indonesia',
                'status' => 'closed',
                'deadline' => '2023-12-31',
            ],
        ];

        foreach ($scholarships as $scholarship) {
            Scholarship::create($scholarship);
        }

        $this->command->info('Scholarship seeder completed successfully!');
    }
}
