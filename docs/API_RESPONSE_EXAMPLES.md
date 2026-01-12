# API Response Examples

Dokumentasi ini berisi contoh response JSON dari endpoint utama aplikasi Student App. Response disusun berdasarkan modul fitur dan menggunakan format baku dari `ApiResponse` trait.

## Standar Response

**Sukses (200 OK / 201 Created)**
```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Pesan sukses"
    },
    "data": { ... }
}
```

**Error (400/401/403/404/500)**
```json
{
    "meta": {
        "status": "error",
        "code": 400,
        "message": "Pesan error"
    },
    "data": null
}
```

---

## 2.1 User Management

### Login
`POST /api/auth/login`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Login berhasil"
    },
    "data": {
        "user": {
            "id": 1,
            "name": "Budi Santoso",
            "email": "student@example.com",
            "role": "student",
            "profile_photo_url": "http://localhost:8000/storage/profile-photos/default.jpg"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Get Profile
`GET /api/auth/profile`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Profil berhasil diambil"
    },
    "data": {
        "user": {
            "id": 1,
            "name": "Budi Santoso",
            "email": "student@example.com",
            "role": "student",
            "major": "Informatika",
            "institution": "Universitas Indonesia",
            "bio": "Mahasiswa tingkat akhir yang antusias belajar AI.",
            "cv_url": "http://localhost/storage/cvs/budi.pdf",
            "specialization": ["Machine Learning", "Web Development"]
        },
        "achievements": [
            {
                "id": 1,
                "title": "Juara 1 Hackathon",
                "year": 2024
            }
        ],
        "experiences": [
             {
                "id": 1,
                "title": "Internship Backend Dev",
                "company": "Tokopedia",
                "type": "internship"
            }
        ]
    }
}
```

---

## 2.2 E-Learning & Bootcamp

### Get Course List
`GET /api/courses`

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
            "title": "Mastering Laravel 11",
            "type": "course",
            "price": 0,
            "access_type": "free",
            "instructor": "Taylor Otwell",
            "rating": 4.8,
            "total_students": 1250,
            "image": "http://localhost/storage/courses/laravel.jpg"
        },
        {
            "id": 2,
            "title": "Fullstack Bootcamp",
            "type": "bootcamp",
            "price": 500000,
            "access_type": "premium",
            "instructor": "Tim NF",
            "rating": 5.0,
            "total_students": 50,
            "image": "http://localhost/storage/courses/bootcamp.jpg"
        }
    ],
    "links": { ... },
    "meta_pagination": { ... }
}
```

### Get Course Detail (With Curriculum)
`GET /api/courses/1`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Detail kursus berhasil diambil"
    },
    "data": {
        "id": 1,
        "title": "Mastering Laravel 11",
        "description": "Belajar Laravel dari nol sampai mahir.",
        "curriculum": [
            {
                "section": "Pengenalan",
                "lessons": [
                    {
                        "id": 101,
                        "title": "Instalasi Laravel",
                        "duration": "10 min",
                        "video_url": "https://video.com/123"
                    }
                ]
            }
        ]
    }
}
```

---

## 2.3 Scholarship Portal

### Get Scholarship List
`GET /api/scholarships`

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
            "name": "Beasiswa Pendidikan Indonesia",
            "provider": {
                "id": 10,
                "name": "Kemendikbud",
                "logo": "http://..."
            },
            "status": "open",
            "deadline": "2025-12-31",
            "category": "S1/S2"
        }
    ]
}
```

### Apply Scholarship
`POST /api/scholarships/5/apply`

```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Lamaran beasiswa berhasil dikirim"
    },
    "data": {
        "id": 88,
        "scholarship_id": 5,
        "user_id": 1,
        "status": "submitted",
        "submitted_at": "2025-06-15 10:00:00",
        "cv_path": "path/to/cv.pdf"
    }
}
```

---

## 2.4 My Mentor

### Get Mentor List (Schedule)
`GET /api/mentors/10/schedule`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Jadwal mentor berhasil diambil"
    },
    "data": [
        {
            "date": "2025-06-20",
            "slots": ["09:00", "10:00", "13:00"]
        },
        {
            "date": "2025-06-21",
            "slots": ["09:00", "10:00"]
        }
    ]
}
```

### Create Session (Booking)
`POST /api/mentoring-sessions`

Request:
```json
{
    "mentor_id": 10,
    "schedule": "2025-06-20 09:00:00",
    "type": "academic"
}
```

Response:
```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Sesi mentoring berhasil dibuat"
    },
    "data": {
        "id": 202,
        "mentor_id": 10,
        "member_id": 1,
        "status": "pending",
        "schedule": "2025-06-20 09:00:00",
        "payment_status": "pending"
    }
}
```

### Pre-Mentoring Need Assessment
`POST /api/mentoring-sessions/202/need-assessments`

Request:
```json
{
    "topic": "Konsultasi Skripsi",
    "goals": "Mendapatkan feedback tentang Bab 1",
    "current_hurdles": "Bingung menentukan latar belakang"
}
```

Response:
```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Assessment berhasil disimpan"
    },
    "data": {
        "id": 55,
        "mentoring_session_id": 202,
        "form_data": {
             "topic": "Konsultasi Skripsi",
             "goals": "Mendapatkan feedback tentang Bab 1",
             "current_hurdles": "Bingung menentukan latar belakang"
        },
        "completed_at": "2025-06-19 14:30:00"
    }
}
```

---

## 2.5 Article & Corporate

### Get Articles
`GET /api/articles`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Artikel berhasil diambil"
    },
    "data": [
        {
            "id": 99,
            "title": "Tips Lolos Beasiswa LPDP",
            "category": "beasiswa",
            "author": "Admin NF",
            "published_at": "2025-05-01"
        }
    ]
}
```

### Corporate Contact Inquiry
`POST /api/corporate-contact`

```json
{
    "meta": {
        "status": "success",
        "code": 201,
        "message": "Pesan berhasil dikirim"
    },
    "data": {
        "id": 12,
        "name": "PT Maju Bersama",
        "email": "hrd@majubersama.com",
        "message": "Kami ingin membuka program beasiswa kerjasama."
    }
}
```

### Get Organization/Provider Profile
`GET /api/organizations/10`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Profil organisasi berhasil diambil"
    },
    "data": {
        "id": 10,
        "name": "Kemendikbud",
        "description": "Kementerian Pendidikan dan Kebudayaan RI.",
        "website": "https://kemdikbud.go.id",
        "logo_url": "http://localhost/storage/orgs/logo.png",
        "active_scholarships": [
            {
                "id": 5,
                "name": "Beasiswa Pendidikan Indonesia"
            }
        ]
    }
}
```

---

## 2.6 Subscription & Packages

### Get Subscription Plans
`GET /api/subscription-plans`

```json
{
    "meta": {
        "status": "success",
        "code": 200,
        "message": "Daftar paket berlangganan berhasil diambil"
    },
    "data": [
        {
            "id": 1,
            "name": "Single Course",
            "price": 150000,
            "features": ["Akses 1 Kursus Selamanya", "Sertifikat"]
        },
        {
            "id": 2,
            "name": "All-Access Monthly",
            "price": 300000,
            "features": ["Akses Semua Kursus", "Prioritas Mentoring", "Akses Bootcamp"]
        }
    ]
}
```
