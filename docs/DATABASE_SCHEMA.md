# Database Schema Documentation

Dokumentasi ini berisi rancangan lengkap basis data yang digunakan dalam aplikasi Student App.

## Entity Relationship Diagram (ERD)

Berikut adalah visualisasi hubungan antar tabel utama dalam database.

```mermaid
erDiagram
    %% AUTH & USERS
    USERS {
        bigint id PK
        string name
        string email
        string password
        enum role "student, mentor, admin, corporate"
        json specialization
        string profile_photo
    }
    
    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        timestamp created_at
    }

    %% E-LEARNING
    COURSES {
        bigint id PK
        string title
        string type "course, bootcamp"
        decimal price
        enum access_type "free, premium"
        string video_url
    }

    COURSE_CURRICULUMS {
        bigint id PK
        bigint course_id FK
        string section
        string title
        int order
        string duration
    }

    ENROLLMENTS {
        bigint id PK
        bigint user_id FK
        bigint course_id FK
        int progress
        boolean completed
        string certificate_url
    }

    CURRICULUM_PROGRESS {
        bigint id PK
        bigint enrollment_id FK
        bigint curriculum_id FK
        boolean completed
    }

    %% MENTORING
    MENTORING_SESSIONS {
        bigint id PK
        bigint mentor_id FK
        bigint member_id FK
        datetime schedule
        string meeting_link
        enum status "pending, completed, cancelled"
    }

    NEED_ASSESSMENTS {
        bigint id PK
        bigint mentoring_session_id FK
        json form_data
    }

    COACHING_FILES {
        bigint id PK
        bigint mentoring_session_id FK
        string file_path
        string file_type
    }

    %% SCHOLARSHIPS
    SCHOLARSHIPS {
        bigint id PK
        string name
        text description
        enum status "open, closed"
        date deadline
    }

    SCHOLARSHIP_APPLICATIONS {
        bigint id PK
        bigint scholarship_id FK
        bigint user_id FK
        enum status "submitted, accepted, rejected"
        string cv_path
    }

    %% TRANSACTIONS & SUBSCRIPTIONS
    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        string transaction_code
        decimal amount
        enum status "pending, paid, failed"
        string transactionable_type
        bigint transactionable_id
    }

    SUBSCRIPTIONS {
        bigint id PK
        bigint user_id FK
        string plan "free, premium"
        date start_date
        date end_date
        enum status "active, expired"
    }

    %% PORTFOLIO
    EXPERIENCES {
        bigint id PK
        bigint user_id FK
        string title
        string company
        enum type "work, internship"
    }

    ACHIEVEMENTS {
        bigint id PK
        bigint user_id FK
        string title
        year year
    }

    ORGANIZATIONS {
        bigint id PK
        bigint user_id FK
        string name
        string role
    }

    %% CONTENT
    ARTICLES {
        bigint id PK
        bigint author_id FK
        string title
        string category
    }

    REVIEWS {
        bigint id PK
        bigint user_id FK
        string reviewable_type
        bigint reviewable_id
        int rating
        text comment
    }

    %% RELATIONS
    USERS ||--o{ ENROLLMENTS : "student enrolls"
    USERS ||--o{ TRANSACTIONS : "makes"
    USERS ||--o{ SUBSCRIPTIONS : "subscribes"
    USERS ||--o{ MENTORING_SESSIONS : "mentor/mentee"
    USERS ||--o{ SCHOLARSHIP_APPLICATIONS : "applies"
    USERS ||--o{ EXPERIENCES : "has"
    USERS ||--o{ ACHIEVEMENTS : "has"
    USERS ||--o{ ORGANIZATIONS : "joins"
    USERS ||--o{ ARTICLES : "writes"
    USERS ||--o{ REVIEWS : "writes"

    COURSES ||--o{ COURSE_CURRICULUMS : "contains"
    COURSES ||--o{ ENROLLMENTS : "has students"
    
    ENROLLMENTS ||--o{ CURRICULUM_PROGRESS : "tracks"
    COURSE_CURRICULUMS ||--o{ CURRICULUM_PROGRESS : "completed in"

    MENTORING_SESSIONS ||--o{ NEED_ASSESSMENTS : "has"
    MENTORING_SESSIONS ||--o{ COACHING_FILES : "has files"

    SCHOLARSHIPS ||--o{ SCHOLARSHIP_APPLICATIONS : "receives"
```

## Detail Struktur Tabel

Berikut adalah penjelasan mendetail dari setiap tabel yang ada di database.

### 1. Authentication & Users

#### `users`
Tabel utama untuk menyimpan data pengguna aplikasi.
- `id` (PK): Primary key.
- `role`: Peran pengguna (`student`, `mentor`, `admin`, `corporate`).
- `name`, `email`, `password`: Kredensial login.
- `specialization`: Bidang keahlian/minat (JSON).
- `cv_path`: Link file CV user.
- `google_id`: ID untuk login via Google.
- `profile_photo`, `phone`, `address`: Data profil tambahan.

#### `password_reset_tokens`
Menyimpan token sementara untuk fitur lupa password.

### 2. E-Learning (Course & Curriculum)

#### `courses`
Menyimpan data kelas atau bootcamp.
- `title`, `description`: Informasi kursus.
- `type`: Jenis (`course` atau `bootcamp`).
- `access_type`: Akses (`free`, `regular`, `premium`).
- `price`: Harga kursus.
- `video_url`: Link video intro.

#### `course_curriculums`
Menyimpan daftar materi (silabus) untuk setiap kursus.
- `course_id` (FK): Relasi ke tabel courses.
- `section`: Nama bab (misal: "Bab 1").
- `title`: Judul materi.
- `duration`: Estimasi durasi belajar.
- `order`: Urutan materi.

#### `enrollments`
Mencatat pendaftaran user ke dalam course.
- `user_id`, `course_id` (FK): Relasi user dan course.
- `progress`: Persentase penyelesaian (0-100).
- `completed`: Status selesai (boolean).
- `certificate_url`: Link sertifikat jika sudah lulus.

#### `curriculum_progress`
Tracking detail penyelesaian per materi.
- `enrollment_id` (FK): Relasi ke data pendaftaran.
- `curriculum_id` (FK): Relasi ke materi spesifik.
- `completed`: Status selesai.

### 3. Mentoring

#### `mentoring_sessions`
Jadwal sesi mentoring antara mentor dan student.
- `mentor_id`, `member_id` (FK): Relasi ke user (mentor & mentee).
- `schedule`: Waktu pelaksanaan.
- `meeting_link`: Link Google Meet/Zoom.
- `status`: Status sesi (`pending`, `completed`, `cancelled`).

#### `need_assessments`
Formulir kebutuhan mentee sebelum sesi dimulai.
- `mentoring_session_id` (FK): Relasi ke sesi.
- `form_data` (JSON): Jawaban assessment dari mentee.

#### `coaching_files`
File materi yang dibagikan mentor untuk sesi mentoring.
- `file_path`, `file_type`: Lokasi dan tipe file.

### 4. Scholarships (Beasiswa)

#### `scholarships`
Data program beasiswa yang tersedia.
- `name`, `description`, `benefit`: Informasi beasiswa.
- `status`: Status pendaftaran (`open`, `closed`).
- `deadline`: Batas waktu pendaftaran.

#### `scholarship_applications`
Data aplikasi/lamaran beasiswa dari student.
- `scholarship_id`, `user_id` (FK): Relasi beasiswa dan pelamar.
- `status`: Status seleksi (`submitted`, `review`, `accepted`, `rejected`).
- `cv_path`, `motivation_letter`: Dokumen persyaratan.

### 5. Transactions & Payment

#### `transactions`
Tabel sentral untuk semua pembayaran (Course, Mentoring, Subscription).
- `transactionable_type`, `transactionable_id`: Polymorphic relation (bisa ke course, subscription, dll).
- `amount`: Jumlah pembayaran.
- `status`: Status pembayaran (`pending`, `paid`, `failed`).
- `payment_method`: Metode bayar (QRIS, VA, transfer).
- `payment_proof`: Bukti transfer manual.

#### `subscriptions`
Paket langganan user.
- `plan`: Jenis paket (free, premium).
- `start_date`, `end_date`: Periode aktif.
- `status`: Status langganan (`active`, `expired`).

### 6. Portfolio & Profile

#### `experiences`
Riwayat pengalaman kerja/organisasi user.
- `title`, `company`, `type` (work/internship).
- `start_date`, `end_date`.

#### `achievements`
Prestasi atau penghargaan user.
- `title`, `year`, `organization`.

#### `organizations`
Data organisasi yang diikuti user (bisa juga data master organisasi).
- `name`, `role`, `description`.

### 7. Content & Feedback

#### `articles`
Artikel edukasi atau berita karir.
- `title`, `content`, `category`.
- `author_id` (FK): Penulis artikel.

#### `reviews`
Sistem rating dan ulasan.
- `reviewable_type`, `reviewable_id`: Polymorphic (bisa review Course atau Mentor).
- `rating`: Bintang 1-5.
- `comment`: Isi ulasan.

#### `corporate_contacts`
Pesan masuk dari perusahaan (inquiry).
- `name`, `email`, `message`.
