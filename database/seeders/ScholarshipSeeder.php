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
        // Pastikan tabel organizations sudah terisi sebelumnya
        $organizations = Organization::all();

        $scholarships = [
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'LPDP_001',
                'image' => 'https://asset.uinjkt.ac.id/uploads/UTPtOVYX/2025/02/scholarship-program-lpdp-2025.jpg',
                'name' => 'LPDP Scholarship Program',
                'description' => 'Beasiswa penuh untuk program master dan doktor di dalam dan luar negeri. Mencakup biaya kuliah, biaya hidup, dan tunjangan penelitian.',
                'benefit' => 'Biaya kuliah penuh, biaya hidup bulanan, tunjangan buku, asuransi kesehatan, tiket pesawat PP',
                'location' => 'Indonesia dan Luar Negeri',
                'status' => 'open',
                'is_recommended' => true,
                'deadline' => '2024-03-31',
                'study_field' => 'General',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'BUMN_002',
                'image' => 'https://cdn.sejutacita.id/dealls-blog-cms/beasiswa_aperti_bumn_2025_0e8abda77f.webp',
                'name' => 'Beasiswa BUMN 2024',
                'description' => 'Program beasiswa dari BUMN untuk mahasiswa berprestasi dengan komitmen bekerja di perusahaan BUMN setelah lulus.',
                'benefit' => 'Biaya kuliah, uang saku bulanan, jaminan kerja di BUMN, pelatihan kepemimpinan',
                'location' => 'Seluruh Indonesia',
                'status' => 'open',
                'is_recommended' => true,
                'deadline' => '2024-02-28',
                'study_field' => 'Business & Management',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'DIKTI_003',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80', // Wisuda / Pendidikan
                'name' => 'Beasiswa Unggulan Kemendikbud',
                'description' => 'Beasiswa prestasi untuk mahasiswa S1, S2, dan S3 dari Kementerian Pendidikan dan Kebudayaan.',
                'benefit' => 'Biaya pendidikan, biaya hidup, dana penelitian, pembimbingan akademik',
                'location' => 'Universitas Negeri dan Swasta Indonesia',
                'status' => 'open',
                'is_recommended' => false,
                'deadline' => '2024-04-15',
                'study_field' => 'Education',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'GOOG_004',
                'image' => 'https://images.unsplash.com/photo-1571171637578-41bc2dd41cd2?auto=format&fit=crop&w=800&q=80', // Coding / Tech
                'name' => 'Google Developer Scholarship',
                'description' => 'Beasiswa khusus untuk pengembangan skills teknologi dan programming dari Google.',
                'benefit' => 'Akses course premium, mentoring dari Google engineers, sertifikat Google',
                'location' => 'Online - Global',
                'status' => 'open',
                'is_recommended' => true,
                'deadline' => '2024-05-30',
                'study_field' => 'Technology',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'AUST_005',
                'image' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=800&q=80', // Kampus Klasik
                'name' => 'Australia Awards Scholarship',
                'description' => 'Beasiswa penuh dari pemerintah Australia untuk program master dan PhD di universitas Australia.',
                'benefit' => 'Biaya kuliah penuh, tunjangan hidup, asuransi kesehatan, tiket pesawat',
                'location' => 'Australia',
                'status' => 'open',
                'is_recommended' => true,
                'deadline' => '2024-04-30',
                'study_field' => 'General',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'CHEVR_006',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=800&q=80', // London Big Ben
                'name' => 'Chevening Scholarship UK',
                'description' => 'Beasiswa bergengsi dari pemerintah Inggris untuk program master satu tahun di UK.',
                'benefit' => 'Biaya kuliah, biaya hidup, tiket pesawat PP, visa allowance',
                'location' => 'United Kingdom',
                'status' => 'coming_soon',
                'is_recommended' => false,
                'deadline' => '2024-11-08',
                'study_field' => 'Social Science',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'FULB_007',
                'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=800&q=80', // Kampus USA
                'name' => 'Fulbright Scholarship',
                'description' => 'Program pertukaran pendidikan dan budaya antara Indonesia dan Amerika Serikat.',
                'benefit' => 'Biaya kuliah, biaya hidup, asuransi kesehatan, program budaya',
                'location' => 'United States',
                'status' => 'coming_soon',
                'is_recommended' => false,
                'deadline' => '2024-06-15',
                'study_field' => 'Arts & Humanities',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'ERASM_008',
                'image' => 'https://images.unsplash.com/photo-1471347324794-ffa83d75c949?auto=format&fit=crop&w=800&q=80', // Travel / Peta Eropa
                'name' => 'Erasmus Mundus Joint Master',
                'description' => 'Beasiswa untuk program master joint di beberapa universitas Eropa.',
                'benefit' => 'Biaya kuliah, monthly allowance, travel costs, visa support',
                'location' => 'European Union',
                'status' => 'open',
                'is_recommended' => false,
                'deadline' => '2024-01-15',
                'study_field' => 'General',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'DAAD_009',
                'image' => 'https://images.unsplash.com/photo-1464965911861-746a04b4b0a6?auto=format&fit=crop&w=800&q=80', // Perpustakaan Klasik / Jerman
                'name' => 'DAAD Scholarship Germany',
                'description' => 'Beasiswa dari layanan pertukaran akademik Jerman untuk berbagai program studi.',
                'benefit' => 'Monthly stipend, health insurance, travel allowance, German language course',
                'location' => 'Germany',
                'status' => 'open',
                'is_recommended' => false,
                'deadline' => '2024-03-15',
                'study_field' => 'Engineering',
            ],
            [
                'user_id' => null,
                'organization_id' => null,
                'provider_id' => 'LOCAL_010',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80', // Belajar / Lokal
                'name' => 'Beasiswa Yayasan Pendidikan Indonesia',
                'description' => 'Beasiswa lokal untuk mahasiswa kurang mampu dengan prestasi akademik baik.',
                'benefit' => 'Bantuan biaya kuliah 50%, bimbingan belajar, pelatihan soft skills',
                'location' => 'Indonesia',
                'status' => 'closed',
                'is_recommended' => false,
                'deadline' => '2023-12-31',
                'study_field' => 'General',
            ],
        ];

        foreach ($scholarships as $scholarship) {
            // Assign random organization jika ada, agar relasi database valid
            if ($organizations->count() > 0) {
                $scholarship['organization_id'] = $organizations->random()->id;
            }

            Scholarship::create($scholarship);
        }

        $this->command->info('Scholarship seeder completed successfully!');
    }
}
