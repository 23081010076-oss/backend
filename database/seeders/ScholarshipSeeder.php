<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        $scholarships = [
            // 1. LPDP
            [
                'provider_id' => 'LPDP_001',
                'image' => 'https://asset.uinjkt.ac.id/uploads/UTPtOVYX/2025/02/scholarship-program-lpdp-2025.jpg',
                'name' => 'LPDP Scholarship Program Reguler',
                'description' => "Beasiswa Pendidikan Indonesia (BPI) Program Reguler adalah beasiswa jenjang magister dan doktoral yang diperuntukkan bagi Warga Negara Indonesia. Program ini dikelola oleh Lembaga Pengelola Dana Pendidikan (LPDP) di bawah Kementerian Keuangan.\n\nFasilitas yang diberikan sangat komprehensif, mencakup biaya pra-studi (seperti visa dan tes bahasa), biaya selama studi (SPP, tunjangan hidup bulanan, asuransi kesehatan), hingga biaya pendukung riset tesis atau disertasi.",
                'benefit' => 'Full Coverage: SPP, Living Allowance, Visa, Asuransi, Tiket PP',
                'location' => 'Indonesia & Luar Negeri',
                'status' => 'open',
                'is_recommended' => true,
                'study_field' => 'General',
                'deadline' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(10),
            ],
            // 2. BUMN
            [
                'provider_id' => 'BUMN_002',
                'image' => 'https://cdn.sejutacita.id/dealls-blog-cms/beasiswa_aperti_bumn_2025_0e8abda77f.webp',
                'name' => 'Program Magang & Beasiswa BUMN',
                'description' => "Program Aliansi Perguruan Tinggi (APERTI) BUMN membuka kesempatan beasiswa bagi lulusan SMA/SMK berprestasi di seluruh Indonesia untuk berkuliah di universitas mitra BUMN.\n\nSelain biaya pendidikan penuh (8 semester), penerima beasiswa akan mendapatkan kesempatan magang eksklusif di perusahaan-perusahaan BUMN terkait untuk mendekatkan dunia pendidikan dengan industri.",
                'benefit' => 'Biaya Kuliah 100%, Uang Saku, Prioritas Magang',
                'location' => 'Nasional (Mitra Kampus BUMN)',
                'status' => 'open',
                'is_recommended' => true,
                'study_field' => 'Business & Engineering',
                'deadline' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(3),
            ],
            // 3. DIKTI (IMAGE UPDATED)
            [
                'provider_id' => 'DIKTI_003',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80', // Gambar Mahasiswa Diskusi
                'name' => 'Beasiswa Unggulan Kemendikbud',
                'description' => "Beasiswa Unggulan adalah pemberian biaya pendidikan oleh pemerintah Indonesia kepada putra-putri terbaik bangsa Indonesia pada jenjang S1, S2, dan S3.\n\nPrioritas diberikan kepada mahasiswa yang memiliki prestasi akademik maupun non-akademik tingkat nasional/internasional. Penerima beasiswa wajib mempertahankan IPK minimal sesuai standar yang ditetapkan setiap semesternya.",
                'benefit' => 'Biaya Pendidikan, Biaya Hidup, Biaya Buku',
                'location' => 'Universitas Negeri dan Swasta Indonesia',
                'status' => 'open',
                'is_recommended' => false,
                'study_field' => 'Education',
                'deadline' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'created_at' => Carbon::now()->subWeeks(1),
            ],
            // 4. Google
            [
                'provider_id' => 'GOOG_004',
                'image' => 'https://images.unsplash.com/photo-1571171637578-41bc2dd41cd2?auto=format&fit=crop&w=800&q=80',
                'name' => 'Google Bangkit Academy 2025',
                'description' => "Bangkit adalah program kesiapan karier yang didesain oleh Google untuk memberikan mahasiswa Indonesia paparan langsung dengan praktisi industri teknologi.\n\nTerdapat tiga jalur pembelajaran utama: Machine Learning, Mobile Development (Android), dan Cloud Computing. Peserta akan mendapatkan sertifikasi global Google dan kesempatan pendanaan inkubasi.",
                'benefit' => 'Sertifikasi Global Google, Konversi SKS, Dana Inkubasi',
                'location' => 'Online (Hybrid)',
                'status' => 'open',
                'is_recommended' => true,
                'study_field' => 'Technology',
                'deadline' => Carbon::now()->addWeeks(2)->format('Y-m-d'),
                'created_at' => Carbon::now(),
            ],
            // 5. Australia Awards
            [
                'provider_id' => 'AUST_005',
                'image' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=800&q=80',
                'name' => 'Australia Awards Scholarship (AAS)',
                'description' => "Australia Awards Scholarship adalah beasiswa internasional prestisius yang didanai oleh Pemerintah Australia untuk jenjang Master atau PhD.\n\nSalah satu keunggulan AAS adalah adanya program Pre-Departure Training (PDT) di Bali atau Jakarta untuk persiapan bahasa dan akademik sebelum berangkat ke Australia.",
                'benefit' => 'Full Tuition, Family Allowance, PDT Training',
                'location' => 'Australia',
                'status' => 'open',
                'is_recommended' => true,
                'study_field' => 'Development Studies',
                'deadline' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'created_at' => Carbon::now()->subMonth(1),
            ],
            // 6. Chevening
            [
                'provider_id' => 'CHEVR_006',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=800&q=80',
                'name' => 'Chevening Scholarship UK',
                'description' => "Chevening adalah program beasiswa global pemerintah Inggris yang menawarkan kesempatan bagi para pemimpin masa depan untuk belajar di Inggris selama satu tahun (Master).\n\nAnda akan bergabung dengan jaringan alumni global yang berpengaruh. Beasiswa ini fully funded, sehingga Anda bisa fokus pada studi dan networking.",
                'benefit' => 'Tuition fees, monthly stipend, travel costs',
                'location' => 'United Kingdom',
                'status' => 'open',
                'is_recommended' => false,
                'study_field' => 'Leadership & Social',
                'deadline' => Carbon::now()->addMonths(5)->format('Y-m-d'),
                'created_at' => Carbon::now()->subWeeks(2),
            ],
            // 7. Fulbright
            [
                'provider_id' => 'FULB_007',
                'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=800&q=80',
                'name' => 'Fulbright Scholarship USA',
                'description' => "Program Fulbright adalah program pertukaran pendidikan unggulan pemerintah AS yang dirancang untuk meningkatkan saling pengertian antara masyarakat AS dan masyarakat negara lain.\n\nBeasiswa ini mencakup pendanaan penuh untuk studi jenjang S2 dan S3 di universitas-universitas terkemuka di Amerika Serikat, termasuk dukungan visa J-1.",
                'benefit' => 'Tuition & Fees, Living Stipend, Health Insurance',
                'location' => 'United States',
                'status' => 'coming_soon',
                'is_recommended' => false,
                'study_field' => 'Arts & Humanities',
                'deadline' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(20),
            ],
            // 8. Erasmus (IMAGE UPDATED)
            [
                'provider_id' => 'ERASM_008',
                'image' => 'https://images.unsplash.com/photo-1491557345352-5929e343eb89?auto=format&fit=crop&w=800&q=80', // Gambar Eropa/Arsitektur
                'name' => 'Erasmus Mundus Joint Master',
                'description' => "Erasmus Mundus Joint Masters (EMJM) adalah program studi jenjang S2 bergengsi yang diselenggarakan oleh konsorsium universitas internasional.\n\nMahasiswa akan berpindah-pindah negara (mobilitas) setidaknya di dua negara Eropa yang berbeda selama masa studi. Ini memberikan pengalaman budaya dan akademik yang luar biasa.",
                'benefit' => 'Participation costs, travel allowance, monthly subsistence',
                'location' => 'European Union (Multiple Countries)',
                'status' => 'open',
                'is_recommended' => false,
                'study_field' => 'General',
                'deadline' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(40),
            ],
            // 9. DAAD (IMAGE UPDATED)
            [
                'provider_id' => 'DAAD_009',
                'image' => 'https://images.unsplash.com/photo-1599946347371-68eb71b16afc?auto=format&fit=crop&w=800&q=80', // Gambar Brandenburg Gate Germany
                'name' => 'DAAD Scholarship Germany',
                'description' => "DAAD (Deutscher Akademischer Austauschdienst) menawarkan beasiswa bagi lulusan negara berkembang untuk mengambil gelar pascasarjana di universitas negeri Jerman.\n\nFokus utama beasiswa ini adalah mencetak profesional yang siap menangani tantangan pembangunan global. Perkuliahan bisa dalam bahasa Inggris atau Jerman.",
                'benefit' => 'Monthly payment, insurance, travel allowance',
                'location' => 'Germany',
                'status' => 'open',
                'is_recommended' => false,
                'study_field' => 'Engineering',
                'deadline' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(15),
            ],
            // 10. Local/Mitra
            [
                'provider_id' => 'MITRA_010',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80',
                'name' => 'Beasiswa Perintis Pelosok Negeri',
                'description' => "Beasiswa Perintis adalah program bantuan biaya pendidikan khusus untuk siswa-siswi berprestasi yang berasal dari daerah 3T.\n\nSelain bantuan finansial berupa UKT (Uang Kuliah Tunggal), penerima beasiswa akan mendapatkan asrama gratis dan pendampingan mentor akademik selama masa studi.",
                'benefit' => 'Bantuan UKT, Asrama Gratis, Mentoring',
                'location' => 'Universitas Negeri (PTN) Indonesia',
                'status' => 'open',
                'is_recommended' => false,
                'study_field' => 'General',
                'deadline' => Carbon::now()->addWeeks(1)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($scholarships as $scholarship) {
            $data = $scholarship;

            // Assign random organization jika ada
            if ($organizations->count() > 0) {
                $data['organization_id'] = $organizations->random()->id;
            } else {
                $data['organization_id'] = null;
            }

            $data['user_id'] = null;

            Scholarship::create($data);
        }

        $this->command->info('Scholarship seeder completed with updated images!');
    }
}
