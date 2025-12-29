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
            // ========== EDUCATION CATEGORY ==========
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
                'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800',
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
- Stay hydrated!

### 3. Social Support
- Jangan isolate yourself
- Talk to friends, family, atau counselor
- Join study groups atau communities

## Remember:
- It\'s okay to not be okay sometimes
- Asking for help is a sign of strength, not weakness
- Your mental health is just as important as your grades

Take care of yourself, teman-teman. You\'re stronger than you think! 💚',
                'category' => 'education',
                'author' => 'Dr. Indira Sari',
                'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800',
            ],
            [
                'title' => 'Metode Belajar Efektif: Teknik Pomodoro dan Active Recall',
                'content' => 'Belajar berjam-jam tidak selalu efektif. Yang penting adalah kualitas, bukan kuantitas. Berikut metode belajar yang terbukti secara ilmiah:

## Teknik Pomodoro
Metode ini dikembangkan oleh Francesco Cirillo pada 1980-an.

**Cara Kerja:**
1. Pilih tugas yang ingin dikerjakan
2. Set timer 25 menit (1 Pomodoro)
3. Fokus penuh tanpa distraction
4. Istirahat 5 menit setelah 1 Pomodoro
5. Setelah 4 Pomodoro, istirahat 15-30 menit

**Benefits:**
- Meningkatkan fokus dan konsentrasi
- Mengurangi mental fatigue
- Membuat tracking progress lebih mudah
- Membantu mengatasi prokrastinasi

## Active Recall
Teknik ini lebih efektif daripada sekadar membaca ulang notes.

**Implementasi:**
- Tutup buku/notes, coba ingat apa yang sudah dipelajari
- Gunakan flashcards (Anki, Quizlet)
- Explain konsep dengan kata-kata sendiri
- Kerjakan practice questions

## Spaced Repetition
Mengulang materi dengan interval yang meningkat.

**Schedule Recommended:**
- Setelah belajar: Review dalam 24 jam
- Kemudian: 3 hari, 1 minggu, 2 minggu, 1 bulan

## Tips Tambahan
- Tidur cukup (memory consolidation terjadi saat tidur)
- Exercise sebelum belajar (meningkatkan blood flow ke otak)
- Minimize distractions (silent phone, block social media)
- Study in short, focused sessions

Selamat mencoba! 📚',
                'category' => 'education',
                'author' => 'Prof. Bambang Sutrisno',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800',
            ],
            [
                'title' => 'Panduan Menulis Skripsi: Dari Proposal hingga Sidang',
                'content' => 'Menulis skripsi adalah milestone penting bagi setiap mahasiswa. Berikut panduan lengkap untuk membantu Anda menyelesaikan skripsi dengan sukses.

## Tahap Persiapan

### 1. Pilih Topik yang Tepat
- Sesuai dengan minat dan kemampuan
- Memiliki novelty atau kontribusi baru
- Data mudah diakses
- Dosen pembimbing tersedia

### 2. Literature Review
- Baca minimal 20-30 jurnal terkait
- Gunakan Google Scholar, Scopus, atau IEEE
- Catat gap penelitian yang ada
- Buat reference manager (Mendeley, Zotero)

## Menyusun Proposal

**Struktur Proposal:**
1. Latar Belakang Masalah
2. Rumusan Masalah
3. Tujuan Penelitian
4. Manfaat Penelitian
5. Tinjauan Pustaka
6. Metodologi
7. Timeline
8. Daftar Pustaka

## Tips Menulis
- Write first, edit later
- Set daily writing goals (min 500 kata)
- Gunakan outline yang jelas
- Backup file secara regular
- Konsultasi rutin dengan dosen pembimbing

## Persiapan Sidang
- Kuasai materi skripsi secara menyeluruh
- Siapkan presentasi yang clear dan concise
- Latihan presentasi beberapa kali
- Antisipasi pertanyaan yang mungkin muncul
- Tidur cukup sebelum hari H

## Common Mistakes to Avoid
- Plagiarisme (selalu cite sumber)
- Deadline mepet
- Kurang komunikasi dengan pembimbing
- Terlalu ambisius dengan scope

Semangat menyelesaikan skripsi! 🎓',
                'category' => 'education',
                'author' => 'Dr. Ratna Kusumawati',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800',
            ],
            [
                'title' => 'Belajar Bahasa Inggris Otodidak: Panduan Lengkap untuk Pemula',
                'content' => 'Kemampuan bahasa Inggris sangat penting di era globalisasi. Berikut panduan belajar bahasa Inggris secara otodidak:

## Fondasi: Grammar dan Vocabulary

### Grammar Basics
- Tenses (Present, Past, Future)
- Subject-Verb Agreement
- Articles (a, an, the)
- Prepositions

**Resources:**
- English Grammar in Use (Raymond Murphy)
- Grammarly (free browser extension)
- YouTube channels: English with Lucy, BBC Learning English

### Building Vocabulary
- Learn 10-20 kata baru setiap hari
- Gunakan dalam konteks kalimat
- Flashcard apps: Anki, Quizlet
- Read extensively

## Listening Skills
- Listen to podcasts (TED Talks, BBC)
- Watch movies/series dengan English subtitles
- Listen to English songs dan pelajari liriknya
- YouTube channels dengan native speakers

## Speaking Practice
- Speak to yourself (yes, it works!)
- Language exchange apps (HelloTalk, Tandem)
- Join English speaking clubs
- Record yourself dan review

## Reading Skills
- Start dengan graded readers
- Baca news articles (BBC, CNN)
- Novels sesuai level (Harry Potter untuk intermediate)
- Highlight unknown words

## Writing Practice
- Keep a daily journal dalam bahasa Inggris
- Post di social media dalam English
- Ikut online writing communities
- Get feedback dari native speakers

## Tips Konsistensi
- Set specific time untuk belajar setiap hari
- Mix different activities untuk avoid boredom
- Track progress Anda
- Celebrate small wins!

Good luck with your English learning journey! 🌟',
                'category' => 'education',
                'author' => 'Sarah Johnson',
                'image' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=800',
            ],

            // ========== SCHOLARSHIP CATEGORY ==========
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
                'image' => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=800',
            ],
            [
                'title' => 'Daftar Beasiswa S2 Luar Negeri Full Funded 2024',
                'content' => 'Bermimpi kuliah S2 di luar negeri? Berikut daftar beasiswa full-funded yang bisa Anda coba:

## Eropa

### 1. Erasmus Mundus (EU)
- Covers tuition, living costs, travel
- 100+ program master tersedia
- Deadline: biasanya Oktober-Januari
- Tips: Pilih program sesuai background

### 2. Chevening (UK)
- Fully funded ke universitas UK manapun
- Untuk future leaders
- Deadline: November
- Strong leadership experience required

### 3. DAAD (Jerman)
- Berbagai skema beasiswa
- Banyak program dalam bahasa Inggris
- Deadline: varies by program
- German language skill is a plus

## Amerika

### 4. Fulbright (USA)
- Prestigious scholarship ke US
- Master atau PhD
- Deadline: Februari-April
- Comprehensive application process

### 5. Australia Awards (Australia)
- Untuk developing countries termasuk Indonesia
- Covers everything + pre-departure training
- Deadline: April-May
- Work experience preferred

## Asia

### 6. MEXT (Jepang)
- Japanese Government Scholarship
- Research students atau degree programs
- Deadline: varies by embassy
- Japanese language training included

### 7. Korea Government Scholarship (Korea)
- Full coverage termasuk Korean language training
- Deadline: varies
- Research proposal needed

## Tips Umum
- Start preparation 1 tahun sebelumnya
- Prepare English proficiency test early
- Get strong recommendation letters
- Craft compelling personal statement
- Apply to multiple scholarships

Good luck! 🌍',
                'category' => 'scholarship',
                'author' => 'Amanda Putri',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800',
            ],
            [
                'title' => 'Cara Menulis Essay Beasiswa yang Memikat',
                'content' => 'Essay adalah salah satu komponen terpenting dalam aplikasi beasiswa. Berikut tips menulis essay yang memikat hati reviewers:

## Struktur Essay yang Baik

### 1. Opening Hook
- Start dengan cerita personal yang menarik
- Avoid klise seperti "Since I was a child..."
- Buat pembaca ingin terus membaca

### 2. Body - Your Story
- Ceritakan journey Anda
- Highlight challenges dan bagaimana Anda mengatasinya
- Tunjukkan growth dan learning
- Be specific, avoid generalities

### 3. Why This Program/Country?
- Research mendalam tentang program
- Connect dengan goals Anda
- Show genuine interest

### 4. Future Plans
- Clear dan realistic goals
- Contribution plan ke Indonesia/community
- Show how scholarship will help achieve goals

### 5. Strong Closing
- Tie back ke opening hook
- Leave lasting impression
- Call to action or inspiring thought

## Do\'s and Don\'ts

**Do:**
- Be authentic dan genuine
- Use concrete examples
- Proofread berkali-kali
- Get feedback dari orang lain
- Follow word limit

**Don\'t:**
- Copy dari internet
- Berbohong atau melebih-lebihkan
- Gunakan vocabulary yang terlalu complex
- Submit tanpa proofreading
- Ignore essay prompt

## Final Tips
- Start early, revise often
- Read successful essays for inspiration
- Let your personality shine through
- Have someone else read it before submitting

Your story is unique. Tell it well! ✨',
                'category' => 'scholarship',
                'author' => 'Dr. Maya Anggraini',
                'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800',
            ],
            [
                'title' => 'Persiapan IELTS untuk Beasiswa: Target Band 7.0+',
                'content' => 'IELTS adalah salah satu persyaratan utama untuk beasiswa luar negeri. Berikut strategi untuk mendapatkan skor 7.0+:

## Memahami Format IELTS

### Listening (30 menit)
- 4 sections, 40 questions
- Hanya diputar 1 kali
- Tips: Preview questions sebelum audio dimulai

### Reading (60 menit)
- 3 passages, 40 questions
- Academic texts dari journals/magazines
- Tips: Scan for keywords, manage time carefully

### Writing (60 menit)
- Task 1: Report/Letter (150 words, 20 min)
- Task 2: Essay (250 words, 40 min)
- Tips: Practice essay structures

### Speaking (11-14 menit)
- Part 1: Introduction (4-5 min)
- Part 2: Long turn dengan cue card (3-4 min)
- Part 3: Discussion (4-5 min)
- Tips: Be natural, don\'t memorize answers

## Study Plan (3 Bulan)

### Month 1: Foundation
- Diagnostic test untuk identify weaknesses
- Focus on grammar dan vocabulary
- Daily reading dalam bahasa Inggris
- Listening to English podcasts

### Month 2: Practice
- Full practice tests setiap minggu
- Focus on weak areas
- Learn essay templates
- Speaking practice dengan partner

### Month 3: Refinement
- Timed practice tests
- Mock speaking tests
- Review mistakes
- Build stamina

## Resources Recommended
- Cambridge IELTS Books (Official)
- IELTS Liz (YouTube & website)
- Road to IELTS (British Council)
- IELTSPodcast.com

## Common Mistakes
- Poor time management
- Skipping questions
- Not reading instructions carefully
- Speaking too fast atau too slow
- Ignoring word limit in writing

Target 7.0+ achievable dengan dedicated practice. Good luck! 📝',
                'category' => 'scholarship',
                'author' => 'Lisa Nguyen',
                'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800',
            ],
            [
                'title' => 'Beasiswa Dalam Negeri yang Jarang Diketahui',
                'content' => 'Selain LPDP, ada banyak beasiswa dalam negeri yang jarang diketahui tapi sangat bermanfaat:

## Beasiswa Pemerintah

### 1. Beasiswa Unggulan Kemendikbud
- Untuk S1, S2, S3
- Mencakup biaya kuliah dan living costs
- Deadline: varies, cek website Kemendikbud

### 2. Beasiswa BUDI (LPDP + Kemendikbud)
- Untuk dosen dan tenaga kependidikan
- Full scholarship
- Prioritas untuk PTN/PTS terakreditasi

### 3. Beasiswa Afirmasi Dikti
- Untuk mahasiswa dari daerah 3T
- Mencakup biaya kuliah dan hidup
- Fokus pada pemerataan pendidikan

## Beasiswa Swasta

### 4. Beasiswa Djarum
- Untuk mahasiswa S1 semester 4+
- IPK minimal 3.0
- Leadership experience is a plus
- Termasuk softskill training

### 5. Beasiswa Tanoto Foundation
- Need-based dan merit-based
- Mencakup berbagai universitas
- Program pengembangan diri included

### 6. Beasiswa Bank Indonesia
- Untuk mahasiswa ekonomi/keuangan
- IPK minimal 3.0
- Termasuk program magang

## Beasiswa Universitas

### 7. Beasiswa Bidik Misi (KIP Kuliah)
- Untuk keluarga kurang mampu
- Full coverage biaya kuliah
- Available di hampir semua PTN

### 8. Beasiswa Prestasi Universitas
- Berdasarkan prestasi akademik/non-akademik
- Setiap universitas punya skema berbeda
- Cek bagian kemahasiswaan

## Tips Apply
- Cek deadline jauh-jauh hari
- Siapkan dokumen lengkap
- Tulis essay yang personal
- Apply ke multiple beasiswa
- Follow up status aplikasi

Jangan menyerah mencari beasiswa! 💪',
                'category' => 'scholarship',
                'author' => 'Dewi Lestari',
                'image' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=800',
            ],

            // ========== CAREER CATEGORY ==========
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
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
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

## 3. Full-Stack TypeScript
JavaScript tetap populer, tapi TypeScript semakin dominan untuk project besar.

**TypeScript Ecosystem:**
- Next.js 14 dengan App Router
- Prisma untuk database ORM
- tRPC untuk type-safe APIs
- Tailwind CSS untuk styling

## Tips Belajar Efektif
1. **Focus on Fundamentals**: Jangan chase setiap framework baru
2. **Build Projects**: Theory without practice is useless
3. **Join Communities**: Discord, Reddit, Twitter tech communities
4. **Contribute to Open Source**: Great way to learn and network

Tidak perlu menguasai semuanya sekaligus. Pilih 1-2 area yang align dengan career goals Anda dan fokus mendalam! 💻',
                'category' => 'career',
                'author' => 'Agus Prasetyo',
                'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800',
            ],
            [
                'title' => 'Tips Lolos Interview Kerja di Perusahaan Tech',
                'content' => 'Interview di perusahaan tech bisa intimidating. Berikut tips untuk membantu Anda lolos:

## Tahap Interview Umumnya

### 1. HR Screening
- Behavioral questions
- Salary expectation
- Availability

### 2. Technical Interview
- Coding challenges
- System design (untuk senior)
- Tech stack specific questions

### 3. Culture Fit
- Team collaboration
- Problem-solving approach
- Career goals

## Persiapan Technical Interview

### Coding Practice
- LeetCode, HackerRank, Codewars
- Focus pada Data Structures & Algorithms
- Practice whiteboard coding
- Time yourself

### System Design (Senior)
- Study common architectures
- Practice designing Twitter, Uber, etc.
- Understand trade-offs
- Books: Designing Data-Intensive Applications

### Framework Specific
- Kuasai framework yang Anda claim di CV
- Prepare untuk deep-dive questions
- Know best practices

## Behavioral Interview Tips

### STAR Method
- **S**ituation: Context of the story
- **T**ask: Your responsibility
- **A**ction: What you did
- **R**esult: Outcome and learnings

### Common Questions
- Tell me about yourself
- Why this company?
- Describe a challenging project
- How do you handle conflict?
- Where do you see yourself in 5 years?

## Day of Interview
- Dress appropriately (smart casual usually OK)
- Arrive 10-15 menit lebih awal
- Bring extra copies of CV
- Prepare questions for interviewer
- Be confident but humble

## After Interview
- Send thank you email
- Follow up if no response dalam 1-2 minggu
- Reflect on what went well/could improve

Good luck! You\'ve got this! 🚀',
                'category' => 'career',
                'author' => 'Rina Handayani',
                'image' => 'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?w=800',
            ],
            [
                'title' => 'Remote Work: Panduan Lengkap Bekerja dari Rumah',
                'content' => 'Remote work semakin populer pasca-pandemi. Berikut panduan untuk produktif bekerja dari rumah:

## Setting Up Workspace

### Ruang Kerja Dedicated
- Pisahkan area kerja dan tempat istirahat
- Pencahayaan yang baik
- Kursi ergonomis (investasi penting!)
- Meja yang proper height

### Tech Setup
- Internet stabil (backup dengan mobile data)
- Laptop/PC yang memadai
- External monitor (sangat recommended)
- Headset dengan mic yang bagus
- Webcam untuk video calls

## Productivity Tips

### Time Management
- Set working hours yang jelas
- Use time blocking technique
- Take regular breaks (Pomodoro!)
- Avoid multitasking

### Communication
- Over-communicate dengan tim
- Quick responses to messages
- Daily standups jika perlu
- Document everything

### Avoiding Distractions
- Use website blockers
- Communicate dengan keluarga tentang work hours
- Put phone in different room
- Noise-cancelling headphones

## Tools Recommended
- **Communication**: Slack, Discord, Microsoft Teams
- **Video Calls**: Zoom, Google Meet
- **Project Management**: Notion, Trello, Asana
- **Time Tracking**: Toggl, Clockify
- **Focus**: Forest, Focus@Will

## Menjaga Work-Life Balance

### Boundaries
- Stop bekerja setelah jam kerja
- Create end-of-work ritual
- Weekend adalah weekend
- Take time off

### Physical Health
- Regular exercise
- Proper posture
- Eye breaks (20-20-20 rule)
- Stay hydrated

### Mental Health
- Regular check-ins dengan rekan
- Virtual social activities
- Recognize burnout signs
- Seek help when needed

Remote work bisa sangat rewarding jika dilakukan dengan benar! 🏠',
                'category' => 'career',
                'author' => 'Dimas Prakoso',
                'image' => 'https://images.unsplash.com/photo-1521898284481-a5ec348cb555?w=800',
            ],
            [
                'title' => 'Cara Negosiasi Gaji: Dapat Offer Terbaik',
                'content' => 'Negosiasi gaji adalah skill penting yang jarang diajarkan. Berikut panduan untuk mendapatkan offer terbaik:

## Persiapan Sebelum Negosiasi

### Research Salary Range
- Glassdoor, LinkedIn Salary Insights
- Kalibrr, JobStreet salary data
- Network dengan professionals di bidang sama
- Consider lokasi dan cost of living

### Know Your Value
- List achievements dan impact Anda
- Quantify jika memungkinkan (increased revenue by X%)
- Unique skills yang Anda bawa
- Market demand untuk skill Anda

### Determine Your Range
- Target salary (ideal)
- Minimum acceptable
- Walk-away number

## Timing yang Tepat
- Setelah offer diberikan, bukan sebelum
- Jangan negosiasi di interview pertama
- Give yourself time untuk consider

## Strategi Negosiasi

### 1. Express Enthusiasm First
"I\'m really excited about this opportunity and I think I can contribute significantly to the team."

### 2. Ask, Don\'t Demand
"Based on my research and experience, I was hoping we could discuss a salary in the range of X-Y."

### 3. Let Them Respond First
Dont\'t fill the silence. Biarkan mereka respond.

### 4. Consider Total Package
- Base salary
- Bonus structure
- Stock options/equity
- Benefits (health, dental)
- Remote work flexibility
- Professional development budget
- Vacation days

### 5. Get It in Writing
Setelah deal, minta offer letter tertulis.

## Phrases to Use
- "Is there flexibility in the salary?"
- "Based on my experience and market rate..."
- "What would it take to get to X?"
- "Can we discuss the total compensation package?"

## What NOT to Do
- Jangan accept immediately (minta waktu)
- Jangan mention personal financial needs
- Jangan lie about other offers
- Jangan be aggressive atau arrogant
- Jangan negotiate via email if possible

## If They Say No
- Ask about timeline untuk review
- Negotiate other benefits
- Ask what milestones untuk salary increase
- Consider if still worth it

Remember: The worst they can say is no. You won\'t lose the offer for asking professionally! 💰',
                'category' => 'career',
                'author' => 'Kevin Wijaya',
                'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800',
            ],
            [
                'title' => 'Freelancing untuk Pemula: Memulai Karier Independen',
                'content' => 'Tertarik menjadi freelancer? Berikut panduan lengkap untuk memulai:

## Apakah Freelancing Cocok untuk Anda?

**Pros:**
- Flexibility waktu dan tempat
- Potential income lebih tinggi
- Pilih project yang disukai
- No office politics

**Cons:**
- Income tidak stabil
- No benefits (health insurance, etc.)
- Self-discipline diperlukan
- Lonely sometimes

## Langkah Memulai

### 1. Tentukan Service yang Ditawarkan
- Web development
- Mobile app development
- UI/UX design
- Content writing
- Digital marketing
- Video editing

### 2. Build Portfolio
- Personal projects
- Pro bono work untuk NGO
- Contribute to open source
- Case studies

### 3. Set Pricing
**Pricing Models:**
- Hourly rate
- Project-based
- Retainer (monthly)

**Research Market Rate:**
- Tanya sesama freelancer
- Check platforms (Upwork, Fiverr)
- Consider experience level

### 4. Find Clients

**Platforms:**
- Upwork, Freelancer, Fiverr
- Toptal (untuk expert)
- Projects.co.id (lokal)
- Sribulancer

**Other Methods:**
- LinkedIn networking
- Cold emailing
- Referrals dari clients sebelumnya
- Personal website/blog

## Managing Freelance Business

### Contracts
- SELALU gunakan contract
- Scope of work yang jelas
- Payment terms
- Revision policy
- Termination clause

### Invoicing
- Use invoicing tools (Invoice Ninja, Wave)
- Clear payment terms
- Follow up untuk late payments

### Taxes
- Set aside % untuk pajak
- Konsultasi dengan tax professional
- Keep receipts untuk expenses

## Tips Sukses
- Deliver lebih dari expected
- Communicate proactively
- Build long-term relationships
- Continuously improve skills
- Have an emergency fund

## Common Mistakes
- Underpricing services
- Working tanpa contract
- Scope creep (accepting extra work without extra pay)
- Poor time estimation
- Tidak menabung untuk pajak

Freelancing bisa sangat rewarding. Start small dan grow from there! 🎯',
                'category' => 'career',
                'author' => 'Sandra Dewi',
                'image' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800',
            ],

            // ========== TESTIMONIAL CATEGORY ==========
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
Setelah hampir 2 tahun di sini, saya bisa bilang Jerman amazing untuk kuliah! Research facilities yang excellent, professor yang supportive, dan pendidikan berkualitas tinggi.

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
                'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=800',
            ],
            [
                'title' => 'Dari Fresh Graduate ke Software Engineer di Startup Unicorn',
                'content' => 'Halo! Saya mau share perjalanan saya dari fresh graduate hingga menjadi software engineer di salah satu startup unicorn di Indonesia.

## Background
Saya lulusan Teknik Informatika dari universitas negeri. Selama kuliah, saya aktif di organisasi kampus dan ikut beberapa hackathon. IPK saya 3.5 - not the best, but decent.

## Persiapan Saat Kuliah

### Skills yang Saya Bangun
- Fundamental programming (data structures, algorithms)
- Web development (React, Node.js)
- Database (SQL, MongoDB)
- Git dan version control
- Basic system design

### Pengalaman yang Membantu
- Internship di 2 perusahaan berbeda
- Freelance projects
- Open source contributions
- Hackathon participations

## Proses Apply ke Startup

### Research
- Target beberapa startup yang culture-nya cocok
- Stalk employees di LinkedIn
- Baca tentang tech stack mereka

### Application
- Tailored CV untuk setiap company
- Cover letter yang genuine
- Portfolio website yang up-to-date

### Interview Process
1. **HR Screening (30 min)**: Basic questions, salary expectation
2. **Technical Test (3 hours)**: Take-home coding challenge
3. **Technical Interview (1.5 hours)**: Live coding + system design discussion
4. **Culture Fit (1 hour)**: With team lead dan product manager

## Tips yang Saya Pelajari

### Technical
- Practice LeetCode secara consistent (1-2 soal per hari)
- Build real projects, bukan cuma tutorial
- Understand WHY, bukan cuma HOW
- Learn to read documentation

### Non-Technical
- Communication skill sangat penting
- Learn to explain technical concepts simply
- Show enthusiasm dan willingness to learn
- Ask good questions during interview

## Current Role
Sekarang saya bekerja sebagai Junior Full Stack Developer di startup. Masih banyak belajar setiap hari, tapi the journey has been worth it.

**What I Love:**
- Solving problems daily
- Learning never stops
- Great team environment
- Work yang fulfilling

## Advice untuk Fresh Graduates
1. Jangan terlalu fokus ke company besar saja
2. Skill > gelar, tapi ijazah tetap penting
3. Network early, akan sangat membantu
4. Don\'t compare to others, focus on your journey
5. It\'s okay to fail interviews, itu learning process

Semoga cerita saya bisa menginspirasi! Feel free to connect jika ada pertanyaan. 🚀',
                'category' => 'testimonial',
                'author' => 'Ryan Kurniawan',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800',
            ],
            [
                'title' => 'Pengalaman Kuliah di Jepang dengan Beasiswa MEXT',
                'content' => 'Konnichiwa! Saya mau berbagi pengalaman mendapat beasiswa MEXT (Monbukagakusho) dan kehidupan kuliah di Jepang.

## Kenapa Jepang?
Sejak SMA saya tertarik dengan teknologi Jepang. Mimpi untuk kuliah di sini sudah ada sejak lama, dan MEXT adalah jalan yang saya pilih.

## Proses MEXT

### Embassy Recommendation Track
Saya apply melalui jalur kedutaan yang lebih accessible.

**Tahap 1: Dokumen Awal**
- Formulir aplikasi
- Field of study proposal
- Transkrip dan ijazah

**Tahap 2: Ujian Tertulis**
- Bahasa Jepang atau Bahasa Inggris
- Matematika
- Subject sesuai jurusan

**Tahap 3: Interview**
- Di kedutaan Jepang
- Tentang motivasi dan research plan
- Basic Japanese language test

### Tips Apply MEXT
1. Research proposal yang specific dan feasible
2. Hubungi professor sebelum apply (Letter of Acceptance helps!)
3. Basic Japanese language skill
4. Show commitment jangka panjang ke Japan-Indonesia relations

## Kehidupan di Jepang

### Akademik
- Research-focused untuk graduate students
- Professor yang sangat dedicated
- Lab culture yang unik
- Presentasi dan seminar rutin

### Daily Life
- Tinggal di university dormitory (affordable!)
- Part-time job allowed (max 28 hours/week)
- Transportasi yang sangat efficient
- Makanan yang amazing 🍜

### Challenges
- Language barrier (especially awal-awal)
- Culture shock
- Weather (hot summer, cold winter)
- Loneliness sometimes

### Best Parts
- Experiencing Japanese culture firsthand
- Making friends dari berbagai negara
- Travel ke berbagai kota
- Career opportunities di Japan atau international companies

## Tips untuk Calon Awardee

### Before Departure
- Belajar bahasa Jepang sebanyak mungkin
- Join komunitas Indonesian students di Jepang
- Prepare mentally untuk hidup mandiri
- Bawa obat-obatan dari Indonesia

### After Arrival
- Attend orientation carefully
- Build network dengan sesama students
- Explore your city
- Manage keuangan dengan baik

## Career After Graduation
Banyak pilihan setelah lulus:
- Kerja di perusahaan Jepang
- Kerja di international company
- Kembali ke Indonesia dengan network Jepang
- Lanjut PhD

Jepang memberikan pengalaman yang luar biasa. Highly recommended untuk yang interested in research dan Japanese culture!

頑張ってください! (Ganbatte kudasai - Good luck!) 🇯🇵',
                'category' => 'testimonial',
                'author' => 'Anisa Rahmawati',
                'image' => 'https://images.unsplash.com/photo-1480796927426-f609979314bd?w=800',
            ],
            [
                'title' => 'Dari Bootcamp ke Full Stack Developer: 6 Bulan Transformasi',
                'content' => 'Hai semua! Saya ingin berbagi perjalanan non-linear saya menjadi developer. Background saya bukan IT - saya lulusan Manajemen.

## Why Career Switch?
Setelah 3 tahun bekerja di bidang marketing, saya merasa stuck. Saya selalu tertarik dengan tech tapi takut untuk switch. Pandemi COVID memberi saya waktu untuk reflect, dan saya memutuskan untuk ambil risiko.

## Choosing Bootcamp vs Self-Study

### Self-Study Attempt (3 bulan)
Awalnya saya coba belajar sendiri:
- FreeCodeCamp
- Coursera
- YouTube tutorials

**Hasil:** Overwhelming, tidak structured, susah stay motivated

### Bootcamp Decision
Akhirnya saya join bootcamp coding intensif 4 bulan:
- Structured curriculum
- Mentorship
- Career support
- Community

## Bootcamp Experience

### What I Learned
**Month 1-2: Fundamentals**
- HTML, CSS, JavaScript
- Git, Terminal basics
- Problem-solving mindset

**Month 3: Backend**
- Node.js, Express
- Database (PostgreSQL)
- REST APIs

**Month 4: Full Stack + Projects**
- React.js
- Full stack integration
- Capstone project

### Challenges
- Steep learning curve
- Imposter syndrome
- Information overload
- Sleep deprivation 😅

### Tips Survive Bootcamp
1. Embrace the struggle, it\'s part of the process
2. Ask for help, jangan malu
3. Focus on understanding, not just copying
4. Build projects di luar curriculum
5. Take care of mental health

## Job Search (2 bulan)

### Strategy
- Apply 5-10 jobs per day
- Customize CV dan cover letter
- Network via LinkedIn
- Join tech communities

### Results
- 150+ applications
- 12 interviews
- 3 offers
- 1 dream job 🎉

### What Helped
- Strong portfolio dengan 3-4 projects
- GitHub yang aktif
- Bootcamp career support
- Mock interview practice
- Never giving up

## Current Role
Sekarang saya bekerja sebagai Junior Full Stack Developer di startup. Masih banyak belajar setiap hari, tapi the journey has been worth it.

**What I Love:**
- Solving problems daily
- Learning never stops
- Great team environment
- Work yang fulfilling

## Advice untuk Career Switchers
1. It\'s never too late to change
2. Invest in structured learning jika mampu
3. Build projects, bukan cuma courses
4. Network adalah kunci
5. Embrace failure sebagai learning
6. Your previous experience is valuable!

Kalau saya bisa, kalian juga pasti bisa! 💪',
                'category' => 'testimonial',
                'author' => 'Fitri Amalia',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800',
            ],
            [
                'title' => 'Pengalaman Magang di FAANG: Tips dan Cerita',
                'content' => 'Hello! Saya mau share pengalaman magang di salah satu perusahaan FAANG (sekarang dikenal juga sebagai "Big Tech").

## Background Saya
Saat apply, saya mahasiswa semester 6 Teknik Informatika. Saya aktif competitive programming dan punya beberapa personal projects.

## Proses Apply

### Preparation (6+ bulan sebelumnya)
- LeetCode: 300+ problems solved
- System Design basics
- Baca "Cracking the Coding Interview"
- Mock interviews dengan teman

### Application
- Apply via career portal
- Referral dari alumni (helps!)
- Resume yang clean dan concise

### Interview Stages

**1. Online Assessment (2 hours)**
- 2-3 coding problems
- Medium to hard difficulty
- Time management crucial!

**2. Technical Phone Screen (45 min)**
- Live coding dengan interviewer
- Problem solving discussion
- Some behavioral questions

**3. Virtual Onsite (4-5 hours)**
- 3-4 technical interviews
- Coding + system design
- 1 behavioral interview (Leadership Principles, etc.)

## Tips Interview FAANG

### Coding Interviews
- Talk through your thought process
- Ask clarifying questions
- Start with brute force, optimize later
- Test your code manually
- Practice with timer

### Behavioral Interviews
- Use STAR method
- Have 5-6 strong stories ready
- Show leadership dan ownership
- Be honest, don\'t exaggerate

## Internship Experience

### Work
- Assigned real project dengan impact
- Mentor yang sangat supportive
- Weekly 1:1s dengan manager
- Access to internal resources

### Culture
- Collaborative environment
- Free food! 🍕
- Amazing office facilities
- Diverse team dari berbagai negara

### What I Learned
- Industry-scale coding practices
- Code review culture
- Working with large codebase
- Professional communication

## Challenges
- Imposter syndrome (surrounded by smart people)
- Adjusting to fast pace
- Time zone differences (remote internship)
- High expectations

## After Internship
Internship bisa lead ke:
- Return offer untuk full-time
- Network untuk future opportunities
- Strong resume boost
- Skills yang transferable

## Advice untuk yang Mau Coba
1. Start preparing early (6-12 months)
2. LeetCode is important, tapi bukan segalanya
3. Projects dan experience juga matter
4. Apply anyway even if you feel not ready
5. Learn from rejections
6. It\'s a number game, keep trying!

Yang penting adalah growth mindset dan consistency. Good luck! 🌟',
                'category' => 'testimonial',
                'author' => 'David Tanaka',
                'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800',
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
                'image' => $article['image'],
            ]);
        }

        $this->command->info('Article seeder completed successfully! Created ' . count($articles) . ' articles.');
    }
}
