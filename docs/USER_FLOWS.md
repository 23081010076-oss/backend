# User Flow Documentation

Berikut adalah alur kerja lengkap (User Flow) untuk setiap role dalam aplikasi Student App.

## 1. Flow Student (Mahasiswa)

Mahasiswa adalah pengguna utama yang mengakses materi pembelajaran, mentoring, dan beasiswa.

### Alur Utama
1.  **Registrasi & Login**: Mendaftar akun atau login via Google.
2.  **E-Learning**:
    *   Melihat daftar kursus.
    *   Membeli kursus (transaksi).
    *   Mengakses materi (Curriculum).
    *   Mengerjakan kuis/tugas (jika ada).
    *   Mendapatkan sertifikat (progress 100%).
3.  **Mentoring**:
    *   Mencari mentor.
    *   Booking jadwal sesi mentoring.
    *   Melakukan pembayaran mentoring.
    *   Melaksanakan sesi (Need Assessment & Coaching Files).
    *   Memberikan review/feedback.
4.  **Scholarship**:
    *   Melihat info beasiswa.
    *   Mendaftar beasiswa (Apply).
    *   Memantau status aplikasi.
5.  **Portfolio**:
    *   Mengupload CV dan sertifikat.
    *   Menambahkan pengalaman organisasi dan prestasi.

```mermaid
graph TD
    Start((Mulai)) --> Login{Login/Register?}
    Login -- Register --> FormReg[Isi Form Registrasi]
    Login -- Google --> GoogleAuth[Google OAuth]
    Login -- Email/Pass --> Auth[Otentikasi]
    
    Auth --> Dashboard
    
    Dashboard --> Menu{Pilih Menu}
    
    %% Course Flow
    Menu -- Courses --> ListCourse[Lihat Daftar Course]
    ListCourse --> CourseDetail[Detail Course]
    CourseDetail --> Enroll{Enroll?}
    Enroll -- Ya --> PayCourse[Pembayaran]
    PayCourse --> AccessCourse[Akses Materi]
    AccessCourse --> Progress[Tracking Progress]
    Progress --> Certificate[Klaim Sertifikat]
    
    %% Mentoring Flow
    Menu -- Mentoring --> ListMentor[Cari Mentor]
    ListMentor --> BookSession[Booking Jadwal]
    BookSession --> PayMentor[Pembayaran Mentoring]
    PayMentor --> Session[Sesi Mentoring]
    Session --> Feedback[Beri Feedback]
    
    %% Scholarship Flow
    Menu -- Scholarship --> ListScholarship[Lihat Beasiswa]
    ListScholarship --> Apply[Apply Beasiswa]
    Apply --> WaitResult[Tunggu Pengumuman]
    
    %% Portfolio Flow
    Menu -- Profile --> UpdateProfile[Update Profil]
    UpdateProfile --> UploadCV[Upload CV/Portofolio]
    
    Feedback --> End((Selesai))
    Certificate --> End
    WaitResult --> End
    UploadCV --> End
```

---

## 2. Flow Mentor

Mentor bertugas membimbing mahasiswa melalui sesi mentoring one-on-one.

### Alur Utama
1.  **Login**: Masuk sebagai Mentor.
2.  **Manajemen Jadwal**: Mengatur ketersediaan jadwal (Schedule).
3.  **Sesi Mentoring**:
    *   Menerima booking sesi.
    *   Membuat Need Assessment untuk mentee.
    *   Mengupload Coaching Files (materi pendukung).
    *   Menyelesaikan sesi (Mark as Completed).
4.  **Profil Mentor**: Mengupdate keahlian dan pengalaman untuk menarik mentee.

```mermaid
graph TD
    Start((Mulai)) --> Login[Login Mentor]
    Login --> Dashboard
    
    Dashboard --> SetSchedule[Atur Jadwal Ketersediaan]
    
    Dashboard --> IncomingSession{Sesi Masuk?}
    IncomingSession -- Ya --> ReviewSession[Review Peserta]
    
    ReviewSession --> ConductSession[Lakukan Sesi Mentoring]
    ConductSession --> NeedAssessment[Isi Need Assessment]
    ConductSession --> UploadFiles[Upload Coaching Files]
    ConductSession --> CompleteSession[Selesaikan Sesi]
    
    CompleteSession --> End((Selesai))
```

---

## 3. Flow Corporate (Perusahaan/Mitra)

Corporate berfokus pada penyediaan beasiswa dan publikasi artikel/info karir.

### Alur Utama
1.  **Login**: Masuk sebagai Corporate.
2.  **Beasiswa (Scholarship)**:
    *   Membuat program beasiswa baru.
    *   Melihat pelamar beasiswa.
    *   Menyeleksi pelamar (Update Status: Accepted/Rejected).
3.  **Artikel**:
    *   Menulis artikel atau berita perusahaan.
    *   Mempublikasikan artikel.

```mermaid
graph TD
    Start((Mulai)) --> Login[Login Corporate]
    Login --> Dashboard
    
    Dashboard --> Menu{Menu}
    
    %% Scholarship Management
    Menu -- Beasiswa --> CreateScholarship[Buat Program Beasiswa]
    CreateScholarship --> ViewApplicants[Lihat Pelamar]
    ViewApplicants --> ScreenApplicant[Seleksi Berkas]
    ScreenApplicant --> Decision{Keputusan}
    Decision -- Terima --> StatusAcc[Status: Accepted]
    Decision -- Tolak --> StatusRej[Status: Rejected]
    
    %% Article Management
    Menu -- Artikel --> WriteArticle[Tulis Artikel]
    WriteArticle --> Publish[Publish Artikel]
    
    StatusAcc --> End((Selesai))
    StatusRej --> End
    Publish --> End
```

---

## 4. Flow Admin

Admin memiliki akses penuh untuk manajemen sistem dan moderasi.

### Alur Utama
1.  **User Management**: Mengelola user (create, update, suspend, activate).
2.  **Course Management**: Membuat dan mengedit course serta kurikulum.
3.  **Verifikasi Pembayaran**: Mengkonfirmasi transaksi pembayaran manual (jika bukan otomatis).
4.  **Content Moderation**: Mengelola artikel dan beasiswa (bisa edit/hapus konten yang tidak sesuai).
5.  **Corporate Contact**: Melihat pesan masuk dari perusahaan yang ingin bekerja sama.

```mermaid
graph TD
    Start((Mulai)) --> Login[Login Admin]
    Login --> Dashboard
    
    Dashboard --> ManageUsers[Manajemen User]
    Dashboard --> ManageCourses[Manajemen Courses]
    ManageCourses --> AddContent[Tambah Materi/Video]
    
    Dashboard --> VerifyPayment[Verifikasi Pembayaran]
    VerifyPayment -- Konfirmasi --> TransSuccess[Transaksi Sukses]
    
    Dashboard --> ViewInquiries[Lihat Corporate Contact]
    
    TransSuccess --> End((Selesai))
```
