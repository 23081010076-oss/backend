<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = User::where('role', 'admin')->get();
        $mentors = User::where('role', 'mentor')->get();
        $authors = $admins->merge($mentors);

        $articles = [
            [
                'title' => '5 Tips Memilih Jurusan Kuliah yang Tepat',
                'content' => 'Memilih jurusan kuliah adalah keputusan penting yang akan mempengaruhi masa depan karier Anda. Berikut adalah 5 tips untuk membantu Anda membuat keputusan yang tepat:

1. **Kenali Minat dan Bakat Anda**
   Sebelum memilih jurusan, penting untuk memahami apa yang benar-benar Anda minati dan di bidang apa Anda memiliki bakat natural.

2. **Riset Prospek Karier**
   Pelajari peluang kerja, gaji rata-rata, dan perkembangan industri di bidang yang Anda minati.

3. **Pertimbangkan Kemampuan Finansial**
   Pastikan Anda atau keluarga mampu membiayai pendidikan di jurusan tersebut.

4. **Konsultasi dengan Ahli**
   Bicaralah dengan counselor, alumni, atau profesional di bidang yang diminati.

5. **Ikuti Passion, Tapi Tetap Realistis**
   Keseimbangan antara passion dan realitas pasar kerja sangat penting.

Ingatlah bahwa tidak ada pilihan yang salah, yang penting adalah komitmen dan usaha Anda dalam menjalani pendidikan tersebut.',
                'category' => 'education',
                'author' => 'Dr. Sari Wijayanti',
            ],
            [
                'title' => 'Panduan Lengkap Mendaftar Beasiswa LPDP 2024',
                'content' => 'Beasiswa LPDP (Lembaga Pengelola Dana Pendidikan) adalah salah satu beasiswa bergengsi di Indonesia. Berikut panduan lengkap untuk mendaftar:

## Persyaratan Umum
- Warga Negara Indonesia
- Lulusan S1/S2 dengan IPK minimal 3.0
- Memiliki skor IELTS/TOEFL yang memenuhi syarat
- Sehat jasmani dan rohani

## Tahap Seleksi
1. **Seleksi Administrasi**
   Pastikan semua dokumen lengkap dan sesuai format yang diminta.

2. **Tes Substansi**
   Meliputi tes potensi keberhasilan studi, kemampuan bahasa Inggris, dan wawasan kebangsaan.

3. **Wawancara**
   Tahap final dimana Anda akan dievaluasi secara menyeluruh.

## Tips Sukses
- Siapkan essay yang kuat dan personal
- Pelajari isu-isu terkini Indonesia
- Latihan speaking bahasa Inggris
- Buat rencana kontribusi yang jelas

## Timeline 2024
- Pendaftaran: Januari - Maret 2024
- Tes Substansi: April - Mei 2024
- Wawancara: Juni - Juli 2024
- Pengumuman: Agustus 2024

Persiapan yang matang adalah kunci sukses mendapatkan beasiswa LPDP.',
                'category' => 'scholarship',
                'author' => 'Prof. Ahmad Syafiq',
            ],
            [
                'title' => 'Strategi Membangun Portfolio Developer yang Menarik',
                'content' => 'Portfolio adalah hal pertama yang dilihat oleh recruiter ketika melamar pekerjaan sebagai developer. Berikut strategi membangun portfolio yang menarik:

## Komponen Wajib Portfolio
1. **About Section**
   Ceritakan siapa Anda, background, dan passion dalam programming.

2. **Skills & Technologies**
   Tampilkan tech stack yang Anda kuasai dengan jujur.

3. **Featured Projects**
   Pilih 3-5 project terbaik yang menunjukkan kemampuan berbeda.

4. **Contact Information**
   Buat mudah untuk dihubungi.

## Tips Project Selection
- **Variasi**: Tunjukkan kemampuan frontend, backend, dan full-stack
- **Real-world Problems**: Pilih project yang solve masalah nyata
- **Clean Code**: Pastikan kode Anda readable dan well-documented
- **Live Demo**: Deploy project Anda agar bisa dicoba langsung

## Platform Recommended
- **GitHub Pages**: Gratis dan terintegrasi dengan GitHub
- **Netlify**: Deployment mudah untuk static sites
- **Vercel**: Perfect untuk React/Next.js projects
- **Personal Domain**: Lebih professional

## Common Mistakes to Avoid
- Portfolio yang tidak responsive
- Tidak ada live demo
- Project yang tidak selesai
- Tidak ada README yang proper
- Design yang outdated

Remember: Quality over quantity. Lebih baik 3 project berkualitas daripada 10 project yang biasa-biasa saja.',
                'category' => 'career',
                'author' => 'Budi Santoso',
            ],
            [
                'title' => 'Pengalaman Mendapat Beasiswa ke Jerman: DAAD Scholarship',
                'content' => 'Hai teman-teman! Saya ingin berbagi pengalaman mendapatkan beasiswa DAAD untuk kuliah S2 di Jerman. Semoga bisa menginspirasi kalian yang bermimpi kuliah di luar negeri.

## Background Saya
Saya lulusan S1 Teknik Informatika dari universitas swasta di Indonesia dengan IPK 3.7. Sempat bekerja sebagai software engineer selama 2 tahun sebelum memutuskan melanjutkan S2.

## Proses Aplikasi
**Persiapan (6 bulan sebelum deadline)**
- Riset program dan universitas yang sesuai
- Belajar bahasa Jerman (minimal A2)
- Persiapan dokumen (transkrip, CV, motivation letter)

**Aplikasi (3 bulan sebelum deadline)**
- Submit aplikasi ke universitas target
- Apply beasiswa DAAD secara parallel
- Request letter of recommendation dari dosen dan atasan

**Waiting Period (4-6 bulan)**
- Periode paling stressful! Harus bersabar menunggu

## Tips Sukses
1. **Start Early**: Mulai persiapan minimal 1 tahun sebelumnya
2. **Learn German**: Walaupun program dalam bahasa Inggris, basic German sangat membantu
3. **Strong Motivation**: Jelaskan dengan spesifik mengapa Jerman dan mengapa program tersebut
4. **Network**: Join grup-grup Indonesian students di Jerman untuk tips

## Life in Germany
Setelah hampir 2 tahun di sini, saya bisa bilang Jerman amazing untuk kuliah! Research facilities yang excellent, professor yang supportive, dan tentunya pendidikan yang berkualitas tinggi.

**Challenges:**
- Cuaca yang dingin (especially winter)
- Birokrasi yang ribet
- Homesick (normal banget!)

**Benefits:**
- Pengalaman hidup yang tak terlupakan
- Networking internasional
- Career opportunities yang luas
- Travel murah ke negara Eropa lain

## Advice for Future Applicants
Jangan takut untuk bermimpi besar! Yang penting preparation yang matang dan never give up. Rejection bukan akhir dunia, it\'s just redirection.

Feel free to reach out jika ada pertanyaan! Good luck! 🇩🇪',
                'category' => 'testimonial',
                'author' => 'Maya Sari',
            ],
            [
                'title' => 'Tren Teknologi 2024 yang Wajib Dikuasai Developer',
                'content' => 'Dunia teknologi berkembang sangat cepat. Sebagai developer, kita perlu selalu update dengan tren terbaru agar tetap relevan di industri. Berikut tren teknologi 2024 yang wajib dikuasai:

## 1. Artificial Intelligence & Machine Learning
AI bukan lagi buzzword, tapi sudah menjadi kebutuhan nyata di berbagai industri.

**Yang perlu dipelajari:**
- Large Language Models (LLMs)
- Computer Vision
- Natural Language Processing
- AI/ML integration in web apps

**Tools & Frameworks:**
- TensorFlow, PyTorch
- OpenAI API, Hugging Face
- LangChain untuk LLM applications

## 2. Cloud-Native Development
Hampir semua perusahaan bermigrasi ke cloud. Cloud-native skills menjadi must-have.

**Key Concepts:**
- Microservices architecture
- Containerization (Docker, Kubernetes)
- Serverless computing
- Infrastructure as Code (IaC)

**Platform Focus:**
- AWS, Google Cloud, Azure
- Vercel, Netlify untuk frontend
- PlanetScale, Supabase untuk database

## 3. Full-Stack TypeScript
JavaScript tetap populer, tapi TypeScript semakin dominan untuk project besar.

**TypeScript Ecosystem:**
- Next.js 14 dengan App Router
- Prisma untuk database ORM
- tRPC untuk type-safe APIs
- Tailwind CSS untuk styling

## 4. Web3 & Blockchain (Optional tapi Promising)
Meskipun hype-nya turun, aplikasi Web3 yang real mulai bermunculan.

**Areas to explore:**
- Smart contract development (Solidity)
- DeFi protocols
- NFT marketplaces
- Cryptocurrency integrations

## 5. Mobile Development Evolution
Mobile dev semakin sophisticated dengan cross-platform solutions.

**Trending Frameworks:**
- Flutter (Dart) - Google\'s bet
- React Native - Still strong
- Expo - Makes RN development easier
- PWA - Progressive Web Apps

## Tips Belajar Efektif
1. **Focus on Fundamentals**: Jangan chase setiap framework baru
2. **Build Projects**: Theory without practice is useless
3. **Join Communities**: Discord, Reddit, Twitter tech communities
4. **Follow Industry Leaders**: Keep up with tech influencers
5. **Contribute to Open Source**: Great way to learn and network

## Kesimpulan
Tidak perlu menguasai semuanya sekaligus. Pilih 1-2 area yang align dengan career goals Anda dan fokus mendalam. Yang penting adalah continuous learning mindset!

What tech trends are you most excited about? Let me know in the comments! 💻',
                'category' => 'career',
                'author' => 'Agus Prasetyo',
            ],
            [
                'title' => 'Mental Health untuk Mahasiswa: Tips Mengatasi Stress Kuliah',
                'content' => 'Kuliah bukan hanya tentang akademik, tapi juga tentang menjaga kesehatan mental. Stress adalah hal normal, tapi kita perlu tahu cara mengatasinya dengan baik.

## Mengenali Tanda-Tanda Stress
**Fisik:**
- Susah tidur atau tidur berlebihan
- Perubahan nafsu makan
- Sakit kepala atau pusing
- Mudah lelah

**Emosional:**
- Mudah marah atau sensitif
- Merasa overwhelmed
- Kehilangan motivasi
- Anxiety atau panic attacks

**Behavioral:**
- Prokrastinasi yang berlebihan
- Menghindari teman atau keluarga
- Penurunan performa akademik
- Mengabaikan self-care

## Strategi Mengatasi Stress

### 1. Time Management
- Gunakan calendar atau planner
- Break down tugas besar jadi smaller tasks
- Set realistic deadlines
- Learn to say NO to unnecessary commitments

### 2. Healthy Lifestyle
**Exercise Regularly:**
- Minimal 30 menit, 3x seminggu
- Bisa jalan kaki, jogging, atau yoga
- Olahraga release endorphins yang bikin mood better

**Proper Nutrition:**
- Makan teratur, jangan skip meals
- Kurangi caffeine berlebihan
- Avoid junk food saat stress
- Stay hydrated!

**Quality Sleep:**
- 7-9 jam per malam
- Consistent sleep schedule
- Avoid gadgets 1 jam sebelum tidur
- Create comfortable sleep environment

### 3. Social Support
- Jangan isolate yourself
- Talk to friends, family, atau counselor
- Join study groups atau communities
- Build meaningful relationships

### 4. Mindfulness & Relaxation
**Meditation:**
- Start dengan 5-10 menit daily
- Apps: Headspace, Calm, atau Insight Timer
- Focus on breathing

**Relaxation Techniques:**
- Progressive muscle relaxation
- Deep breathing exercises
- Journaling
- Listen to calming music

## Kapan Harus Seek Professional Help?
Jika Anda mengalami:
- Persistent sadness atau hopelessness
- Suicidal thoughts
- Substance abuse
- Panic attacks yang frequent
- Inability to function dalam daily activities

**Resources:**
- University counseling center
- Hotline kesehatan mental: 119 ext 8
- Online counseling platforms
- Mental health professionals

## Self-Care Activities
- Reading for pleasure
- Creative hobbies (drawing, music, writing)
- Spending time in nature
- Taking warm baths
- Watching funny movies
- Cooking healthy meals

## Remember:
- It\'s okay to not be okay sometimes
- Asking for help is a sign of strength, not weakness
- Your mental health is just as important as your grades
- This too shall pass

Take care of yourself, teman-teman. You\'re stronger than you think! 💚

If you\'re struggling, please reach out. There are people who care and want to help.',
                'category' => 'education',
                'author' => 'Dr. Indira Sari',
            ],
        ];

        foreach ($articles as $index => $article) {
            $author = $authors->count() > 0 ? $authors->random() : null;

            Article::create([
                'title' => $article['title'],
                'content' => $article['content'],
                'category' => $article['category'],
                'author' => $article['author'],
                'author_id' => $author ? $author->id : null,
            ]);
        }

        $this->command->info('Article seeder completed successfully!');
    }
}
