# Database Schema Documentation

Dokumentasi ini berisi rancangan lengkap basis data yang digunakan dalam aplikasi Student App.

## Entity Relationship Diagram (ERD)

Berikut adalah visualisasi hubungan antar tabel utama dalam database.

```mermaid
classDiagram
    direction TD
    
    namespace Auth_System {
        class USERS {
            +bigint id PK
            +string name
            +string email
            +string password
            +enum role
            +enum gender
            +date birth_date
            +string phone
            +text address
            +string institution
            +string major
            +string education_level
            +json specialization
            +text bio
            +string profile_photo
            +string google_id
            +string cv_path
            +timestamp email_verified_at
            +timestamp created_at
            +timestamp updated_at
        }
        class PASSWORD_RESET_TOKENS {
            +string email PK
            +string token
            +timestamp created_at
        }
    }

    namespace Learning_Management {
        class COURSES {
            +bigint id PK
            +string title
            +string image
            +string category
            +text description
            +enum type
            +string instructor
            +enum level
            +string duration
            +decimal price
            +enum access_type
            +string certificate_url
            +text video_url
            +string video_duration
            +int total_videos
            +timestamp created_at
            +timestamp updated_at
        }
        class COURSE_CURRICULUMS {
            +bigint id PK
            +bigint course_id FK
            +string section
            +int section_order
            +string title
            +text description
            +int order
            +string duration
            +timestamp created_at
            +timestamp updated_at
        }
        class ENROLLMENTS {
            +bigint id PK
            +bigint user_id FK
            +bigint course_id FK
            +int progress
            +boolean completed
            +string certificate_url
            +timestamp created_at
            +timestamp updated_at
        }
        class CURRICULUM_PROGRESS {
            +bigint id PK
            +bigint enrollment_id FK
            +bigint curriculum_id FK
            +boolean completed
            +timestamp completed_at
            +timestamp created_at
            +timestamp updated_at
        }
    }

    namespace Mentoring_System {
        class MENTORING_SESSIONS {
            +bigint id PK
            +bigint mentor_id FK
            +bigint member_id FK
            +string session_id
            +enum type
            +datetime schedule
            +string meeting_link
            +enum payment_method
            +enum status
            +enum need_assessment_status
            +timestamp created_at
            +timestamp updated_at
        }
        class NEED_ASSESSMENTS {
            +bigint id PK
            +bigint mentoring_session_id FK
            +json form_data
            +timestamp completed_at
            +timestamp created_at
            +timestamp updated_at
        }
        class COACHING_FILES {
            +bigint id PK
            +bigint mentoring_session_id FK
            +string file_name
            +string file_path
            +string file_type
            +bigint uploaded_by FK
            +timestamp created_at
            +timestamp updated_at
        }
    }

    namespace Scholarship_Center {
        class SCHOLARSHIPS {
            +bigint id PK
            +bigint user_id FK
            +bigint organization_id FK
            +string provider_id
            +string name
            +text description
            +text benefit
            +string location
            +enum status
            +date deadline
            +timestamp created_at
            +timestamp updated_at
        }
        class SCHOLARSHIP_APPLICATIONS {
            +bigint id PK
            +bigint user_id FK
            +bigint scholarship_id FK
            +string motivation_letter
            +string cv_path
            +string transcript_path
            +string recommendation_path
            +enum status
            +datetime submitted_at
            +timestamp created_at
            +timestamp updated_at
        }
    }

    namespace Finance {
        class TRANSACTIONS {
            +bigint id PK
            +bigint user_id FK
            +string transaction_code
            +enum type
            +string transactionable_type
            +bigint transactionable_id
            +decimal amount
            +enum payment_method
            +enum status
            +text payment_details
            +string payment_proof
            +timestamp paid_at
            +timestamp expired_at
            +timestamp created_at
            +timestamp updated_at
        }
        class SUBSCRIPTIONS {
            +bigint id PK
            +bigint user_id FK
            +string plan
            +date start_date
            +date end_date
            +enum package_type
            +int duration
            +enum duration_unit
            +json courses_ids
            +decimal price
            +boolean auto_renew
            +enum status
            +timestamp created_at
            +timestamp updated_at
        }
    }

    namespace Portfolio_Profile {
        class EXPERIENCES {
            +bigint id PK
            +bigint user_id FK
            +string title
            +text description
            +enum type
            +string level
            +string company
            +date start_date
            +date end_date
            +string certificate_url
            +timestamp created_at
            +timestamp updated_at
        }
        class ACHIEVEMENTS {
            +bigint id PK
            +bigint user_id FK
            +string title
            +text description
            +string organization
            +year year
            +timestamp created_at
            +timestamp updated_at
        }
        class ORGANIZATIONS {
            +bigint id PK
            +bigint user_id FK
            +string name
            +string type
            +text description
            +string location
            +string website
            +string contact_email
            +string phone
            +int founded_year
            +string logo_url
            +timestamp created_at
            +timestamp updated_at
        }
    }

    namespace Content_General {
        class ARTICLES {
            +bigint id PK
            +bigint author_id FK
            +string title
            +text content
            +string category
            +string author
            +timestamp created_at
            +timestamp updated_at
        }
        class REVIEWS {
            +bigint id PK
            +bigint user_id FK
            +string reviewable_type
            +bigint reviewable_id
            +int rating
            +text comment
            +timestamp created_at
            +timestamp updated_at
        }
        class CORPORATE_CONTACTS {
            +bigint id PK
            +bigint org_id FK
            +string name
            +string email
            +text message
            +timestamp created_at
            +timestamp updated_at
        }
    }

    %% RELATIONSHIPS
    %% User Core Relations
    USERS "1" --> "*" ENROLLMENTS : enrolls in
    USERS "1" --> "*" TRANSACTIONS : makes
    USERS "1" --> "*" SUBSCRIPTIONS : has
    USERS "1" --> "*" MENTORING_SESSIONS : participates
    USERS "1" --> "*" SCHOLARSHIP_APPLICATIONS : applies
    USERS "1" --> "*" EXPERIENCES : has
    USERS "1" --> "*" ACHIEVEMENTS : has
    USERS "1" --> "*" ORGANIZATIONS : joins
    USERS "1" --> "*" ARTICLES : writes
    USERS "1" --> "*" REVIEWS : writes

    %% Module Internal Relations
    COURSES "1" *-- "*" COURSE_CURRICULUMS : contains
    COURSES "1" --> "*" ENROLLMENTS : has students
    ENROLLMENTS "1" *-- "*" CURRICULUM_PROGRESS : tracks
    COURSE_CURRICULUMS "1" --> "*" CURRICULUM_PROGRESS : completed in

    MENTORING_SESSIONS "1" *-- "*" NEED_ASSESSMENTS : has
    MENTORING_SESSIONS "1" *-- "*" COACHING_FILES : has files

    SCHOLARSHIPS "1" --> "*" SCHOLARSHIP_APPLICATIONS : receives
    
    ORGANIZATIONS "1" --> "*" CORPORATE_CONTACTS : may have
```

## Detail Struktur Tabel

Berikut adalah penjelasan mendetail dari setiap tabel dalam format tabel.

### 1. Authentication & Users

**Tabel: `users`**
Tabel utama untuk menyimpan data pengguna aplikasi.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `role` | Enum | `student`, `mentor`, `admin`, `corporate` |
| `name` | String | Nama lengkap pengguna |
| `email` | String (Unique) | Alamat email (login) |
| `password` | String | Password terenkripsi |
| `specialization` | JSON | Bidang keahlian/minat pengguna |
| `google_id` | String | ID untuk login Google (Nullable) |
| `profile_photo` | String | URL foto profil |
| `cv_path` | String | URL file CV pengguna |

**Tabel: `password_reset_tokens`**
Menyimpan token untuk reset password.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `email` | String (PK) | Email pengguna |
| `token` | String | Token verifikasi |
| `created_at` | Timestamp | Waktu pembuatan link |

### 2. E-Learning (Course & Curriculum)

**Tabel: `courses`**
Menyimpan data kelas atau bootcamp.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `title` | String | Judul course |
| `type` | Enum | `course`, `bootcamp` |
| `access_type` | Enum | `free`, `regular`, `premium` |
| `price` | Decimal | Harga course |
| `video_url` | Text | Link video preview/intro |
| `description` | Text | Deskripsi lengkap course |

**Tabel: `course_curriculums`**
Menyimpan silabus/materi course.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `course_id` | BigInt (FK) | Relasi ke tabel `courses` |
| `section` | String | Nama Bab/Section |
| `title` | String | Judul Materi |
| `duration` | String | Estimasi durasi (mis: "10 menit") |
| `order` | Integer | Urutan materi |

**Tabel: `enrollments`**
Mencatat partisipasi user dalam course.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `user_id` | BigInt (FK) | Student yang mendaftar |
| `course_id` | BigInt (FK) | Course yang diambil |
| `progress` | Integer | Persentase (0-100) |
| `completed` | Boolean | Status kelulusan |
| `certificate_url` | String | Link file sertifikat |

**Tabel: `curriculum_progress`**
Tracking detail per materi.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `enrollment_id` | BigInt (FK) | Relasi ke enrollment |
| `curriculum_id` | BigInt (FK) | Relasi ke materi spesifik |
| `completed` | Boolean | Status selesai |

### 3. Mentoring

**Tabel: `mentoring_sessions`**
Jadwal sesi mentoring.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `mentor_id` | BigInt (FK) | User (Mentor) |
| `member_id` | BigInt (FK) | User (Student/Mentee) |
| `schedule` | DateTime | Waktu pelaksanaan |
| `meeting_link` | String | Link Zoom/GMeet |
| `status` | Enum | `pending`, `scheduled`, `completed`, `cancelled` |

**Tabel: `need_assessments`**
Assessment kebutuhan mentee.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `mentoring_session_id` | BigInt (FK) | Sesi terkait |
| `form_data` | JSON | Isian form assessment |

**Tabel: `coaching_files`**
File pendukung sesi mentoring.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `mentoring_session_id` | BigInt (FK) | Sesi terkait |
| `file_path` | String | Lokasi file |
| `file_type` | String | Jenis file |

### 4. Scholarships (Beasiswa)

**Tabel: `scholarships`**
Program beasiswa.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `name` | String | Nama beasiswa |
| `description` | Text | Deskripsi lengkap |
| `status` | Enum | `open`, `closed`, `coming_soon` |
| `deadline` | Date | Batas akhir pendaftaran |

**Tabel: `scholarship_applications`**
Pendaftaran beasiswa.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `scholarship_id` | BigInt (FK) | Program beasiswa |
| `user_id` | BigInt (FK) | Pelamar |
| `status` | Enum | `submitted`, `review`, `accepted`, `rejected` |
| `cv_path` | String | File CV pelamar |
| `motivation_letter` | String | Surat motivasi |

### 5. Transactions & Payment

**Tabel: `transactions`**
Pusat data transaksi.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `transaction_code` | String (Unique) | Kode unik transaksi |
| `user_id` | BigInt (FK) | Pembayar |
| `amount` | Decimal | Nominal bayar |
| `payment_method` | Enum | `qris`, `bank_transfer`, `manual`, dll |
| `status` | Enum | `pending`, `paid`, `failed`, `expired` |
| `transactionable_type` | String | Model terkait (Course/Subscription) |
| `transactionable_id` | BigInt | ID dari model terkait |

**Tabel: `subscriptions`**
Langganan user.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Primary Key |
| `user_id` | BigInt (FK) | User |
| `plan` | String | Nama paket (`free`/`premium`) |
| `start_date` | Date | Mulai aktif |
| `end_date` | Date | Berakhir |
| `status` | Enum | `active`, `expired` |

### 6. Portfolio & Profile

**Tabel: `experiences`** (Pengalaman Kerja), **`achievements`** (Prestasi), **`organizations`** (Organisasi).

| Nama Tabel | Kolom Penting | Keterangan |
| :--- | :--- | :--- |
| `experiences` | `title`, `company`, `type` | Riwayat pekerjaan/magang |
| `achievements` | `title`, `year`, `organization` | Penghargaan yang diraih |
| `organizations` | `name`, `role`, `description` | Riwayat organisasi |

### 7. Content & Feedback

**Tabel: `articles`** (Artikel), **`reviews`** (Ulasan), **`corporate_contacts`** (Pesan).

| Nama Tabel | Kolom Penting | Keterangan |
| :--- | :--- | :--- |
| `articles` | `title`, `content`, `category` | Konten artikel edukasi |
| `reviews` | `rating`, `comment`, `reviewable` | Rating bintang 1-5 |
| `corporate_contacts` | `name`, `email`, `message` | Inquiry dari perusahaan |
