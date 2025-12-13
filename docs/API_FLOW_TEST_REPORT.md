# API Flow Test Report (Laporan Pengujian API)

Dokumen ini berisi detail pengujian API lengkap berdasarkan **User Flow** untuk setiap role (Student, Mentor, Corporate, Admin).

---

## 1. Flow Student (Mahasiswa)
**User Story**: Registrasi, belajar (course), mentoring, beasiswa, dan portofolio.

### A. Registrasi & Login
#### 1.1 Registrasi Akun
- **Endpoint**: `POST /api/register`
- **Request**:
  ```json
  {
      "name": "Budi Santoso",
      "email": "budi@student.com",
      "password": "password123",
      "password_confirmation": "password123",
      "role": "student"
  }
  ```
- **Response (201 Created)**: Sukses registrasi.

#### 1.2 Login Manual
- **Endpoint**: `POST /api/login`
- **Request**: `{ "email": "budi@student.com", "password": "password123" }`
- **Response (200 OK)**: Returns `token`.

#### 1.3 Login Google (OAuth)
- **Endpoint (Redirect)**: `GET /api/auth/google/redirect` -> *Redirects to Google*
- **Endpoint (Callback)**: `GET /api/auth/google/callback?code=...`
- **Response (200 OK)**: Returns `token` (sama seperti login manual).

### B. E-Learning (Courses)
#### 1.4 Lihat Daftar Kursus
- **Endpoint**: `GET /api/courses`
- **Response**: List of courses.

#### 1.5 Detail Kursus & Kurikulum
- **Endpoint**: `GET /api/courses/{courseId}` (Include `curriculum` relation)
- **Response**: Detail course info + list of curriculum sections.

#### 1.6 Beli/Enroll Kursus
- **Endpoint**: `POST /api/transactions/courses/{courseId}`
- **Request**: `{ "payment_method": "bank_transfer" }`
- **Response (201 Created)**: Transaction details.

#### 1.7 Akses Materi (Menandai Selesai)
- **Endpoint**: `POST /api/curriculums/{curriculumId}/complete`
- **Response (200 OK)**: Progress updated.

### C. Mentoring
#### 1.8 Cari Mentor & Lihat Jadwal
- **Endpoint**: `GET /api/mentors/{mentorId}/schedule`
- **Response**: List slot waktu tersedia (e.g., `["09:00", "13:00"]`).

#### 1.9 Booking Sesi Mentoring
- **Endpoint**: `POST /api/mentoring-sessions`
- **Request**:
  ```json
  { "mentor_id": 20, "schedule": "2025-08-20 09:00:00" }
  ```
- **Response (201 Created)**: Session created with status `pending`.

#### 1.10 Bayar Sesi Mentoring
- **Endpoint**: `POST /api/transactions/mentoring-sessions/{sessionId}`
- **Response**: Transaction created.

#### 1.11 Beri Review/Feedback (Setelah Sesi)
- **Endpoint**: `POST /api/mentoring-sessions/{sessionId}/feedback`
- **Request**: `{ "rating": 5, "review": "Sangat membantu!" }`
- **Response (200 OK)**: Feedback saved.

### D. Scholarship (Beasiswa)
#### 1.12 Lihat Info Beasiswa
- **Endpoint**: `GET /api/scholarships`
- **Response**: List active scholarships.

#### 1.13 Apply Beasiswa
- **Endpoint**: `POST /api/scholarships/{id}/apply`
- **Request**: `{ "cv_path": "link/to/cv.pdf" }` (atau multipart jika upload file langsung)
- **Response (201 Created)**: Application submitted.

#### 1.14 Cek Status Aplikasi
- **Endpoint**: `GET /api/my-applications`
- **Response**: List aplikasi saya (Status: `pending`, `accepted`, `rejected`).

### E. Portfolio
#### 1.15 Update Profil
- **Endpoint**: `PUT /api/auth/profile`
- **Request**: `{ "bio": "Mahasiswa tingkat akhir", "institution": "ITB" }`
- **Response**: Updated profile.

#### 1.16 Upload CV
- **Endpoint**: `POST /api/auth/profile/cv` (Multipart)
- **Response**: URL CV yang diupload.

#### 1.17 Tambah Pengalaman (Experience)
- **Endpoint**: `POST /api/experiences`
- **Request**: `{ "title": "Intern", "company": "Google", "type": "job" }`
- **Response**: Experience added.

---

## 2. Flow Mentor
**User Story**: Login, atur jadwal, sesi mentoring.

#### 2.1 Login Mentor
- **Endpoint**: `POST /api/login` (Role: mentor)

#### 2.2 Atur Jadwal (Availability)
- **Endpoint**: `POST /api/mentoring-sessions` (Note: Butuh endpoint khusus `Set Schedule` jika belum ada, atau via update profile. Asumsi current implementation: via `POST /api/mentors/schedule` or similar logic in backend). *Update: Based on routes, use dedicated logic if available, otherwise mock setup.*

#### 2.3 Lihat Sesi Masuk
- **Endpoint**: `GET /api/my-mentoring-sessions`
- **Response**: List sesi yang dibooking student.

#### 2.4 Isi Need Assessment (Catatan Mentor)
- **Endpoint**: `POST /api/mentoring-sessions/{id}/need-assessments`
- **Request**: `{ "notes": "Perlu bimbingan skripsi bab 1" }`
- **Response**: Assessment saved.

#### 2.5 Upload Coaching Files
- **Endpoint**: `POST /api/mentoring-sessions/{id}/coaching-files` (Multipart)
- **Response**: File uploaded.

#### 2.6 Selesaikan Sesi
- **Endpoint**: `PUT /api/mentoring-sessions/{id}/status`
- **Request**: `{ "status": "completed" }`
- **Response**: Status updated.

#### 2.7 Update Keahlian (Profile)
- **Endpoint**: `PUT /api/auth/profile`
- **Request**: `{ "specialization": ["Data Science", "Python"] }`

---

## 3. Flow Corporate (Perusahaan)
**User Story**: Login, beasiswa, artikel.

#### 3.1 Login Corporate
- **Endpoint**: `POST /api/login` (Role: corporate)

#### 3.2 Buat Program Beasiswa
- **Endpoint**: `POST /api/scholarships`
- **Request**: `{ "title": "Beasiswa 2025", "funding_amount": 5000000 }`

#### 3.3 Lihat Pelamar Beasiswa
- **Endpoint**: `GET /api/scholarships/{id}` (With applicants relation)
- **Response**: List applicants.

#### 3.4 Seleksi Pelamar
- **Endpoint**: `PUT /api/scholarship-applications/{appId}/status`
- **Request**: `{ "status": "accepted" }`
- **Response**: Applicant accepted.

#### 3.5 Publikasi Artikel
- **Endpoint**: `POST /api/articles`
- **Request**: `{ "title": "Tech News", "content": "..." }`

---

## 4. Flow Admin
**User Story**: Manage Users, Courses, Validation.

#### 4.1 Login Admin
- **Endpoint**: `POST /api/login`

#### 4.2 Manajemen User (Suspend)
- **Endpoint**: `POST /api/admin/users/{id}/suspend`
- **Response**: User deactivated.

#### 4.3 Tambah Kursus Baru
- **Endpoint**: `POST /api/courses`
- **Request**: `{ "title": "New Course", "price": 100000 }`
- **Response**: Course created.

#### 4.4 Verifikasi Pembayaran
- **Endpoint**: `POST /api/transactions/{id}/confirm`
- **Response**: Transaction status `paid`.

#### 4.5 Moderasi Artikel (Hapus)
- **Endpoint**: `DELETE /api/articles/{id}`
- **Response**: Artikel terhapus.

#### 4.6 Lihat Pesan Masuk (Corporate Contact)
- **Endpoint**: `GET /api/corporate-contacts`
- **Response**: List pesan dari perusahaan.
