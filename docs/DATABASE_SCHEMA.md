# Database Schema Documentation

Dokumentasi ini berisi rancangan basis data yang digunakan dalam aplikasi Student App.

## Entity Relationship Diagram (ERD)

Berikut adalah visualisasi hubungan antar tabel utama dalam database.

```mermaid
erDiagram
    USERS ||--o{ TRANSACTIONS : "makes"
    USERS ||--o{ ENROLLMENTS : "enrolls in"
    USERS ||--o{ MENTORING_SESSIONS : "participates in (as mentor/mentee)"
    USERS ||--o{ SUBSCRIPTIONS : "has"
    
    COURSES ||--o{ ENROLLMENTS : "has"
    COURSES ||--o{ COURSE_CURRICULUMS : "contains"
    
    COURSE_CURRICULUMS ||--o{ CURRICULUM_PROGRESS : "tracked in"
    ENROLLMENTS ||--o{ CURRICULUM_PROGRESS : "tracks"
    
    MENTORING_SESSIONS ||--o{ NEED_ASSESSMENTS : "has"
    MENTORING_SESSIONS ||--o{ COACHING_FILES : "has_files"
    
    SCHOLARSHIPS ||--o{ SCHOLARSHIP_APPLICATIONS : "receives"
    USERS ||--o{ SCHOLARSHIP_APPLICATIONS : "applies for"

    USERS {
        bigint id PK
        string name
        string email
        enum role "student, mentor, admin, corporate"
        string password
    }

    COURSES {
        bigint id PK
        string title
        enum type "course, bootcamp"
        decimal price
        enum access_type "free, premium"
    }

    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        string transaction_code
        decimal amount
        enum status "pending, paid, failed"
        string transactionable_type "Polymorphic"
        bigint transactionable_id "Polymorphic"
    }

    MENTORING_SESSIONS {
        bigint id PK
        bigint mentor_id FK
        bigint member_id FK
        datetime schedule
        enum status
    }

    ENROLLMENTS {
        bigint id PK
        bigint user_id FK
        bigint course_id FK
        int progress
        boolean completed
    }
```

## Deskripsi Tabel Utama

### 1. Users
Menyimpan data semua pengguna aplikasi.
- **role**: Membedakan hak akses (student, mentor, admin, corporate).
- **specialization**: Menyimpan data spesialisasi user (misal: IT, Business).

### 2. Courses & Curriculum
Menyimpan data kursus dan materi pembelajarannya.
- **courses**: Data ssecara umum (judul, harga, instruktur).
- **course_curriculums**: Daftar materi (bab/sub-bab) dalam satu course.
- **curriculum_progress**: Menyimpan status penyelesaian setiap materi oleh user (logika: jika enrollment_id X sudah menyelesaikan curriculum_id Y).

### 3. Transactions
Tabel sentral untuk segala jenis pembayaran.
- **Polymorphic Relation**: Menggunakan `transactionable_type` dan `transactionable_id` sehingga satu tabel bisa menangani pembelian Course, Subscription, maupun Mentoring.

### 4. Mentoring
- **mentoring_sessions**: Jadwal sesi antara mentor dan student.
- **need_assessments**: Catatan kebutuhan student sebelum sesi dimulai.
- **coaching_files**: File materi yang diupload mentor untuk sesi tersebut.

### 5. Scholarships
- **scholarships**: Program beasiswa yang dibuka oleh Corporate.
- **scholarship_applications**: Data pendaftaran student untuk beasiswa tertentu.
