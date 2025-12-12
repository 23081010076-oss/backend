# Dokumentasi Lengkap API Student App

Dokumentasi ini berisi detail teknis implementasi REST API untuk aplikasi **Student App**. Setiap endpoint dilengkapi dengan metode HTTP, URL, serta contoh *Request Body* dan *Response Body*.

## Ringkasan Route API (Untuk Postman)

Berikut adalah daftar lengkap endpoint yang tersedia. Base URL: `http://localhost:8000`

### Public Routes (Tanpa Auth)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/register` | Pendaftaran akun baru |
| `POST` | `/api/login` | Login pengguna |
| `GET` | `/api/courses` | Lihat daftar kursus |
| `GET` | `/api/courses/{id}` | Lihat detail kursus |
| `GET` | `/api/scholarships` | Lihat info beasiswa |
| `GET` | `/api/articles` | Lihat artikel |
| `GET` | `/api/reviews` | Lihat ulasan |
| `POST` | `/api/corporate-contact` | Kirim pesan kerjasama |

### Protected Routes (Butuh Token Bearer)

**User & Profile**
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/auth/me` | Cek user yang sedang login |
| `GET` | `/api/auth/profile` | Lihat profil lengkap |
| `PUT` | `/api/auth/profile` | Update data diri |
| `POST` | `/api/auth/profile/photo` | Upload foto profil |
| `POST` | `/api/auth/profile/cv` | Upload CV |
| `GET` | `/api/auth/portfolio` | Lihat portofolio user |
| `POST` | `/api/auth/logout` | Logout |

**Learning & Subscription**
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/courses/{id}/enroll` | Daftar kursus (Enroll) |
| `GET` | `/api/my-courses` | Lihat kursus saya |
| `GET` | `/api/courses/{id}/progress` | Cek progres kurikulum |
| `POST` | `/api/curriculums/{id}/complete`| Tandai materi selesai |
| `GET` | `/api/subscriptions` | Lihat status langganan |
| `POST` | `/api/subscriptions` | Beli langganan baru |

**Mentoring**
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/mentors/{id}/schedule` | Cek jadwal mentor |
| `POST` | `/api/mentoring-sessions` | Booking sesi mentoring |
| `GET` | `/api/my-mentoring-sessions` | Riwayat sesi saya |
| `POST` | `/api/mentoring-sessions/{id}/feedback` | Beri rating mentor |
| `POST` | `/api/mentoring-sessions/{id}/need-assessments` | Isi assessment pra-mentoring |

**Transactions**
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/transactions` | Riwayat transaksi |
| `POST` | `/api/transactions/courses/{id}` | Bayar kursus |
| `POST` | `/api/transactions/subscriptions`| Bayar langganan |
| `POST` | `/api/transactions/{id}/payment-proof` | Upload bukti bayar |

---

## 1. User Management
Fitur pengelolaan akun pengguna (Student, Mentor, Corporate, Admin).

### 1.1 Registrasi Pengguna
**Endpoint:** `POST /api/auth/register`
**Deskripsi:** Mendaftarkan pengguna baru sebagai Student, Mentor, atau Corporate.

**Request Body:**
```json
{
    "name": "Budi Santoso",
    "email": "budi@student.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "student",
    "phone": "08123456789",
    "major": "Informatika",
    "institution": "Universitas Indonesia"
}
```

**Response (201 Created):**
```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Pendaftaran berhasil. Silakan login."
    },
    "data": {
        "id": 101,
        "name": "Budi Santoso",
        "email": "budi@student.com",
        "role": "student"
    }
}
```

### 1.2 Login Pengguna
**Endpoint:** `POST /api/auth/login`
**Deskripsi:** Masuk ke aplikasi menggunakan email dan password.

**Request Body:**
```json
{
    "email": "budi@student.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Login berhasil"
    },
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "user": {
            "id": 101,
            "name": "Budi Santoso",
            "role": "student",
            "profile_photo": "http://api.studentapp.com/storage/default.jpg"
        }
    }
}
```

### 1.3 Update Profil
**Endpoint:** `PUT /api/auth/profile`
**Deskripsi:** Memperbarui data diri, jurusan, dan spesialisasi.

**Request Body:**
```json
{
    "name": "Budi Santoso S.Kom",
    "bio": "Fresh graduate enthusiast in AI",
    "specialization": ["Machine Learning", "Web Dev"],
    "address": "Jl. Merdeka No. 10, Jakarta"
}
```

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Profil berhasil diupdate"
    },
    "data": {
        "id": 101,
        "name": "Budi Santoso S.Kom",
        "bio": "Fresh graduate enthusiast in AI",
        "specialization": ["Machine Learning", "Web Dev"]
    }
}
```

---

## 2. E-Learning & Bootcamp
Fitur pembelajaran mandiri dan intensif.

### 2.1 Lihat Daftar Kursus
**Endpoint:** `GET /api/courses`
**Deskripsi:** Menampilkan daftar kursus dengan filter (kategori, harga, tipe).
**Query Params:** `?type=bootcamp&search=Laravel`

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Daftar kursus berhasil diambil"
    },
    "data": [
        {
            "id": 1,
            "title": "Bootcamp Fullstack Laravel",
            "type": "bootcamp",
            "price": 500000,
            "instructor": "Tim NF",
            "image": "http://api.studentapp.com/storage/courses/bootcamp.jpg"
        }
    ],
    "links": { ... },
    "meta_pagination": { ... }
}
```

### 2.2 Detail Kursus (Kurikulum)
**Endpoint:** `GET /api/courses/{id}`
**Deskripsi:** Menampilkan detail lengkap kursus termasuk silabus materi.

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Detail kursus berhasil diambil"
    },
    "data": {
        "id": 1,
        "title": "Bootcamp Fullstack Laravel",
        "description": "Belajar Laravel 11 dari nol.",
        "video_url": "https://youtube.com/embed/xyz",
        "curriculum": [
            {
                "section": "Pendahuluan",
                "lessons": [
                    { "title": "Instalasi", "duration": "10 min" },
                    { "title": "Konfigurasi DB", "duration": "15 min" }
                ]
            }
        ]
    }
}
```

---

## 3. Scholarship Portal
Portal informasi beasiswa.

### 3.1 Daftar Beasiswa
**Endpoint:** `GET /api/scholarships`
**Filter:** `?category=S1&location=Luar Negeri&status=open`

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Daftar beasiswa berhasil diambil"
    },
    "data": [
        {
            "id": 5,
            "name": "Beasiswa LPDP Tahap 1",
            "provider": {
                "name": "LPDP Kemenkeu",
                "logo": "http://..."
            },
            "deadline": "2025-08-31",
            "status": "open"
        }
    ]
}
```

### 3.2 Profil Penyelenggara (Company Profile)
**Endpoint:** `GET /api/organizations/{id}`
**Deskripsi:** Melihat profil lembaga penyedia beasiswa.

**Response (200 OK):**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Profil organisasi berhasil diambil"
    },
    "data": {
        "id": 10,
        "name": "LPDP Kemenkeu",
        "description": "Lembaga Pengelola Dana Pendidikan",
        "website": "https://lpdp.kemenkeu.go.id",
        "active_scholarships": [ ... ]
    }
}
```

### 3.3 Apply Beasiswa
**Endpoint:** `POST /api/scholarships/{id}/apply`
**Deskripsi:** Mengirim lamaran beasiswa beserta dokumen pendukung.

**Request Body (Multipart/Form-Data):**
- `cv`: file.pdf
- `transcript`: file.pdf
- `motivation_letter`: "Saya sangat berminat..."

**Response (201 Created):**
```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Lamaran berhasil dikirim"
    },
    "data": {
        "id": 88,
        "status": "submitted",
        "submitted_at": "2025-06-15 10:00:00"
    }
}
```

---

## 4. My Mentor
Layanan mentoring akademik dan perencanaan hidup.

### 4.1 Jadwal Mentor
**Endpoint:** `GET /api/mentors/{id}/schedule`
**Deskripsi:** Melihat ketersediaan jadwal mentor.

**Response (200 OK):**
```json
{
    "meta": { "status": "success", "code": 200 },
    "data": [
        { "date": "2025-06-20", "slots": ["09:00", "13:00"] },
        { "date": "2025-06-21", "slots": ["10:00"] }
    ]
}
```

### 4.2 Booking Sesi
**Endpoint:** `POST /api/mentoring-sessions`
**Request Body:**
```json
{
    "mentor_id": 55,
    "schedule": "2025-06-20 09:00:00",
    "type": "academic_mentoring"
}
```

**Response (201 Created):**
```json
{
    "meta": { "status": "success", "code": 201, "message": "Booking berhasil" },
    "data": {
        "id": 202,
        "status": "pending_payment",
        "payment_method": "qris",
        "amount": 150000
    }
}
```

### 4.3 Pre-Mentoring Assessment
**Endpoint:** `POST /api/mentoring-sessions/{id}/need-assessments`
**Deskripsi:** Mengisi form kebutuhan mentoring sebelum sesi dimulai.

**Request Body:**
```json
{
    "goals": "Ingin review proposal skripsi bab 1",
    "challenges": "Bingung di latar belakang masalah"
}
```

**Response (201 Created):**
```json
{
    "meta": { "status": "success", "code": 201, "message": "Assessment tersimpan" },
    "data": { "id": 12, "goals": "Ingin review proposal..." }
}
```

---

## 5. Article & Corporate

### 5.1 Kirim Pesan Kerjasama (Contact Us)
**Endpoint:** `POST /api/corporate-contact`
**Request Body:**
```json
{
    "name": "PT Teknologi Maju",
    "email": "hr@tekno.com",
    "message": "Kami ingin membuka lowongan magang eksklusif."
}
```

**Response (201 Created):**
```json
{
    "meta": { "status": "success", "code": 201, "message": "Pesan terkirim" },
    "data": { "id": 5, "name": "PT Teknologi Maju" }
}
```

---

## 6. Portfolio & Activity

### 6.1 Upload Sertifikat/Prestasi
**Endpoint:** `POST /api/achievements`
**Request Body:**
```json
{
    "title": "Juara 1 Lomba Coding",
    "year": "2024",
    "organization": "Kemenkominfo"
}
```

**Response (201 Created):**
```json
{
    "meta": { "status": "success", "code": 201 },
    "data": { "id": 10, "title": "Juara 1 Lomba Coding" }
}
```

### 6.2 Get Full Portfolio
**Endpoint:** `GET /api/auth/portfolio`
**Deskripsi:** Mengambil seluruh data user (profile, experience, achievement, courses) dalam satu request.

**Response (200 OK):**
```json
{
    "meta": { "status": "success", "code": 200 },
    "data": {
        "user": { "name": "Budi", "role": "student" },
        "experiences": [ ... ],
        "achievements": [ ... ],
        "courses": [ ... ],
        "mentoring_history": [ ... ]
    }
}
```

---

## 7. Subscription & Transaction
Fitur berlangganan dan pembayaran.

### 7.1 Daftar Paket Langganan
**Endpoint:** `GET /api/subscription-plans`
**Deskripsi:** Melihat opsi paket (Single Course, Monthly All-Access, Yearly).

**Response (200 OK):**
```json
{
    "meta": { "status": "success", "code": 200 },
    "data": [
        {
            "id": "plan_monthly",
            "name": "Premium Monthly",
            "price": 50000,
            "features": ["Akses Semua Course", "Sertifikat"]
        },
        {
            "id": "plan_yearly",
            "name": "Premium Yearly",
            "price": 450000,
            "features": ["Hemat 25%", "Prioritas Mentoring"]
        }
    ]
}
```

### 7.2 Buat Transaksi (Checkout)
**Endpoint:** `POST /api/transactions`
**Request Body:**
```json
{
    "type": "subscription",
    "item_id": "plan_monthly",
    "payment_method": "gopay"
}
```

**Response (201 Created):**
```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Transaksi dibuat"
    },
    "data": {
        "transaction_code": "TRX-20251212-001",
        "amount": 50000,
        "payment_url": "https://app.midtrans.com/snap/v1/transactions/..."
    }
}
```
