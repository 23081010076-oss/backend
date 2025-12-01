# 📚 PANDUAN APLIKASI - Untuk Programmer Pemula

## 🎯 Apa Ini?

Ini adalah **Backend API** untuk aplikasi Learning Platform (Platform Belajar).
Backend artinya bagian server yang menyimpan dan mengolah data.

---

## 📁 Struktur Folder Penting

```
app/
├── Http/
│   └── Controllers/Api/    ← CONTROLLER: Logic/otak aplikasi
├── Models/                 ← MODEL: Struktur data/tabel database
├── Services/               ← SERVICE: Fungsi bisnis yang kompleks
├── Policies/               ← POLICY: Aturan siapa boleh akses apa
└── Traits/                 ← TRAIT: Kode yang bisa dipakai ulang
```

---

## 🔧 File-File Utama

### 1. MODELS (app/Models/)

**Fungsi:** Mewakili tabel di database

| File                   | Fungsi                                |
| ---------------------- | ------------------------------------- |
| `User.php`             | Data pengguna (nama, email, password) |
| `Course.php`           | Data kursus/pelajaran                 |
| `Article.php`          | Data artikel/blog                     |
| `Scholarship.php`      | Data beasiswa                         |
| `Achievement.php`      | Data prestasi pengguna                |
| `Experience.php`       | Data pengalaman (kerja/pendidikan)    |
| `Enrollment.php`       | Data pendaftaran kursus               |
| `MentoringSession.php` | Data sesi mentoring                   |
| `Transaction.php`      | Data transaksi pembayaran             |
| `Subscription.php`     | Data langganan                        |

### 2. CONTROLLERS (app/Http/Controllers/Api/)

**Fungsi:** Menerima request dan memberikan response

| File                        | Fungsi                          |
| --------------------------- | ------------------------------- |
| `AuthController.php`        | Login, Register, Logout, Profil |
| `CourseController.php`      | CRUD Kursus                     |
| `ArticleController.php`     | CRUD Artikel                    |
| `ScholarshipController.php` | CRUD Beasiswa                   |
| `UserController.php`        | Kelola User (Admin)             |
| `AchievementController.php` | CRUD Prestasi                   |
| `ExperienceController.php`  | CRUD Pengalaman                 |

### 3. TRAIT (app/Traits/)

**Fungsi:** Kode yang bisa dipakai di banyak tempat

| File              | Fungsi                            |
| ----------------- | --------------------------------- |
| `ApiResponse.php` | Format response JSON yang seragam |

---

## 📝 Istilah-Istilah Penting

| Istilah        | Arti                                                   |
| -------------- | ------------------------------------------------------ |
| **API**        | Cara aplikasi berkomunikasi (kirim/terima data)        |
| **Endpoint**   | Alamat URL untuk akses fitur tertentu                  |
| **Request**    | Permintaan dari client (frontend/Postman)              |
| **Response**   | Balasan dari server                                    |
| **JWT Token**  | "Kartu identitas" untuk akses fitur yang perlu login   |
| **CRUD**       | Create, Read, Update, Delete (buat, baca, ubah, hapus) |
| **Validation** | Pengecekan apakah data yang dikirim valid              |
| **Middleware** | "Penjaga pintu" yang cek sebelum masuk controller      |

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Jalankan Server

```bash
php artisan serve
```

Server akan jalan di: `http://127.0.0.1:8000`

### 2. Test Endpoint

Gunakan Postman atau REST Client untuk test API.

---

## 📌 Endpoint Utama

### AUTENTIKASI (Login/Register)

| Method | Endpoint                    | Fungsi               |
| ------ | --------------------------- | -------------------- |
| POST   | `/api/auth/register`        | Daftar akun baru     |
| POST   | `/api/auth/login`           | Login                |
| POST   | `/api/auth/logout`          | Logout               |
| GET    | `/api/auth/me`              | Lihat data saya      |
| GET    | `/api/auth/profile`         | Lihat profil lengkap |
| PUT    | `/api/auth/profile`         | Update profil        |
| PUT    | `/api/auth/change-password` | Ganti password       |

### KURSUS

| Method | Endpoint            | Fungsi              |
| ------ | ------------------- | ------------------- |
| GET    | `/api/courses`      | Lihat semua kursus  |
| GET    | `/api/courses/{id}` | Lihat detail kursus |
| POST   | `/api/courses`      | Buat kursus baru    |
| PUT    | `/api/courses/{id}` | Update kursus       |
| DELETE | `/api/courses/{id}` | Hapus kursus        |

### BEASISWA

| Method | Endpoint                       | Fungsi                |
| ------ | ------------------------------ | --------------------- |
| GET    | `/api/scholarships`            | Lihat semua beasiswa  |
| GET    | `/api/scholarships/{id}`       | Lihat detail beasiswa |
| POST   | `/api/scholarships/{id}/apply` | Daftar beasiswa       |

---

## 🔐 Cara Pakai Token

Setelah login, kamu dapat **token**. Token ini harus dikirim di setiap request yang butuh login.

**Header yang perlu ditambahkan:**

```
Authorization: Bearer {token_kamu}
Accept: application/json
```

---

## 📊 Format Response

### Jika SUKSES:

```json
{
    "sukses": true,
    "pesan": "Data berhasil diambil",
    "data": {
        "id": 1,
        "name": "John Doe"
    }
}
```

### Jika GAGAL:

```json
{
    "sukses": false,
    "pesan": "Data tidak ditemukan",
    "data": null
}
```

---

## 💡 Tips untuk Pemula

1. **Mulai dari AuthController** - Pelajari dulu cara login/register
2. **Baca komentar di code** - Sudah dibuat dalam Bahasa Indonesia
3. **Gunakan Postman** - Untuk test API tanpa perlu buat frontend
4. **Lihat file routes/api.php** - Untuk tahu semua endpoint yang tersedia
5. **Jangan takut error** - Error adalah cara belajar terbaik!

---

## 🎓 Memahami Request & Resource

### Apa Itu Request?

**Request** adalah class untuk **validasi input**. Daripada tulis validasi di controller, kita pindahkan ke file terpisah.

**Lokasi:** `app/Http/Requests/`

**Contoh tanpa Request (kurang rapi):**

```php
public function register(Request $request)
{
    // Validasi di dalam controller (panjang!)
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'role'     => 'required|in:student,mentor',
    ]);

    // ... kode lainnya
}
```

**Contoh dengan Request (lebih rapi):**

```php
// Controller jadi bersih!
public function register(RegisterRequest $request)
{
    // Validasi OTOMATIS dijalankan sebelum masuk sini
    $validated = $request->validated();

    // ... kode lainnya
}
```

**File Request yang tersedia:**
| File | Fungsi |
|------|--------|
| `LoginRequest.php` | Validasi data login |
| `RegisterRequest.php` | Validasi data pendaftaran |
| `ChangePasswordRequest.php` | Validasi ganti password |
| `UpdateProfileRequest.php` | Validasi update profil |

---

### Apa Itu Resource?

**Resource** adalah class untuk **format output/response**. Daripada tulis format response manual di controller, kita pakai Resource.

**Lokasi:** `app/Http/Resources/`

**Contoh tanpa Resource (kurang rapi):**

```php
public function me(Request $request)
{
    $user = $request->user();

    // Format manual di controller (panjang!)
    return response()->json([
        'id'            => $user->id,
        'name'          => $user->name,
        'email'         => $user->email,
        'role'          => $user->role,
        'phone'         => $user->phone,
        'address'       => $user->address,
        'institution'   => $user->institution,
        // ... dan seterusnya
    ]);
}
```

**Contoh dengan Resource (lebih rapi):**

```php
public function me(Request $request)
{
    // Satu baris! Format sudah diatur di UserResource
    return $this->successResponse(
        new UserResource($request->user()),
        'Data berhasil diambil'
    );
}
```

**File Resource yang tersedia:**
| File | Fungsi |
|------|--------|
| `UserResource.php` | Format data user |
| `CourseResource.php` | Format data kursus |
| `ScholarshipResource.php` | Format data beasiswa |
| `EnrollmentResource.php` | Format data enrollment |

---

### Alur Request → Controller → Resource

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│   REQUEST       │      │   CONTROLLER    │      │   RESOURCE      │
│   (Validasi)    │ ──▶  │   (Logic)       │ ──▶  │   (Format)      │
│                 │      │                 │      │                 │
│ - Cek input     │      │ - Proses data   │      │ - Format output │
│ - Tolak jika    │      │ - Query DB      │      │ - Konsisten     │
│   tidak valid   │      │ - Return data   │      │   di semua API  │
└─────────────────┘      └─────────────────┘      └─────────────────┘
```

**Penjelasan:**

1. **Request** cek apakah data yang dikirim valid
2. Jika valid, masuk ke **Controller**
3. Controller proses data, lalu kirim ke **Resource**
4. Resource format data sebelum dikirim ke user

---

### Keuntungan Pakai Request & Resource

| Keuntungan                  | Penjelasan                                              |
| --------------------------- | ------------------------------------------------------- |
| **Controller lebih bersih** | Tidak ada validasi panjang di controller                |
| **Kode terorganisir**       | Validasi di folder Requests, format di folder Resources |
| **Mudah diubah**            | Mau ubah validasi? Edit di satu tempat saja             |
| **Bisa dipakai ulang**      | Request/Resource bisa dipakai di banyak controller      |
| **Error message konsisten** | Pesan error sudah diatur di Request                     |

---

## 🏭 Memahami SERVICE

### Apa Itu Service?

**Service** adalah class untuk **logika bisnis yang kompleks**. Daripada tulis logika panjang di controller, kita pindahkan ke Service agar controller tetap bersih.

**Lokasi:** `app/Services/`

**Analoginya begini:**

-   **Controller** = Pelayan restoran (terima pesanan, antar makanan)
-   **Service** = Koki di dapur (masak makanan yang rumit)

Pelayan tidak masak makanan, dia hanya menerima pesanan dan mengantarkan hasil masakan dari koki.

---

### Kapan Pakai Service?

Gunakan Service ketika:

1. Logika melibatkan **banyak langkah** (lebih dari 5-10 baris)
2. Logika perlu **dipakai di banyak tempat**
3. Ada **transaksi database** yang kompleks
4. Perlu **menghitung atau memproses** sesuatu yang rumit

---

### Contoh Tanpa Service (Kurang Rapi)

```php
// Controller jadi PANJANG dan BERANTAKAN!
public function store(Request $request)
{
    // Logika kompleks di controller (buruk!)
    $validated = $request->validated();

    // 1. Cek apakah user sudah punya subscription aktif
    $existing = Subscription::where('user_id', auth()->id())
                            ->where('status', 'active')
                            ->first();
    if ($existing) {
        return $this->errorResponse('Sudah punya langganan aktif');
    }

    // 2. Hitung harga berdasarkan paket
    $price = 0;
    if ($validated['plan'] == 'regular') {
        $price = 99000;
    } elseif ($validated['plan'] == 'premium') {
        $price = 199000;
    }

    // 3. Hitung tanggal berakhir
    $endDate = now()->addMonths($validated['duration']);

    // 4. Buat subscription
    $subscription = Subscription::create([
        'user_id'    => auth()->id(),
        'plan'       => $validated['plan'],
        'price'      => $price,
        'start_date' => now(),
        'end_date'   => $endDate,
        'status'     => 'pending',
    ]);

    // 5. Kirim email notifikasi
    // ... kode kirim email ...

    return $this->createdResponse($subscription);
}
```

---

### Contoh Dengan Service (Lebih Rapi)

**File Service (`app/Services/SubscriptionService.php`):**

```php
class SubscriptionService
{
    /**
     * Buat langganan baru
     * Semua logika kompleks ada di sini!
     */
    public function createSubscription(array $data, User $user): Subscription
    {
        // 1. Cek apakah user sudah punya subscription aktif
        $existing = Subscription::where('user_id', $user->id)
                                ->where('status', 'active')
                                ->first();
        if ($existing) {
            throw new \Exception('Sudah punya langganan aktif');
        }

        // 2. Hitung harga
        $price = $this->calculatePrice($data['plan']);

        // 3. Hitung tanggal berakhir
        $endDate = now()->addMonths($data['duration']);

        // 4. Buat subscription
        $subscription = Subscription::create([
            'user_id'    => $user->id,
            'plan'       => $data['plan'],
            'price'      => $price,
            'start_date' => now(),
            'end_date'   => $endDate,
            'status'     => 'pending',
        ]);

        // 5. Kirim notifikasi
        $this->sendNotification($user, $subscription);

        return $subscription;
    }

    private function calculatePrice(string $plan): int
    {
        return match($plan) {
            'regular' => 99000,
            'premium' => 199000,
            default   => 0,
        };
    }
}
```

**File Controller (jadi BERSIH!):**

```php
class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    // Inject service lewat constructor
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function store(StoreSubscriptionRequest $request)
    {
        // Controller jadi pendek! Logika ada di Service
        $subscription = $this->subscriptionService->createSubscription(
            $request->validated(),
            $request->user()
        );

        return $this->createdResponse($subscription, 'Langganan berhasil dibuat');
    }
}
```

---

### File Service yang Tersedia

| File                      | Fungsi                                        |
| ------------------------- | --------------------------------------------- |
| `SubscriptionService.php` | Kelola langganan (buat, perpanjang, batalkan) |
| `EnrollmentService.php`   | Kelola pendaftaran kursus                     |
| `ReviewService.php`       | Kelola review/ulasan                          |
| `OrganizationService.php` | Kelola organisasi                             |
| `MidtransService.php`     | Integrasi payment gateway Midtrans            |

---

### Alur Controller → Service

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│   CONTROLLER    │      │   SERVICE       │      │   DATABASE      │
│   (Terima req)  │ ──▶  │   (Logika)      │ ──▶  │   (Simpan)      │
│                 │      │                 │      │                 │
│ - Validasi      │      │ - Proses bisnis │      │ - Insert        │
│ - Panggil       │      │ - Hitung-hitung │      │ - Update        │
│   service       │      │ - Kirim email   │      │ - Delete        │
│ - Return resp   │      │ - Return hasil  │      │                 │
└─────────────────┘      └─────────────────┘      └─────────────────┘
```

---

## 🛡️ Memahami POLICY

### Apa Itu Policy?

**Policy** adalah class untuk **aturan otorisasi** (siapa boleh melakukan apa). Policy menjawab pertanyaan seperti:

-   "Apakah user ini boleh edit data ini?"
-   "Apakah user ini boleh hapus data ini?"
-   "Apakah user ini boleh lihat data ini?"

**Lokasi:** `app/Policies/`

**Analoginya begini:**

-   **Middleware** = Satpam di pintu gerbang (cek apakah punya kartu akses/login)
-   **Policy** = Satpam di ruangan (cek apakah boleh masuk ruangan TERTENTU)

Middleware cek: "Apakah sudah login?"
Policy cek: "Apakah boleh edit data INI?"

---

### Kapan Pakai Policy?

Gunakan Policy ketika:

1. Perlu cek **kepemilikan data** (apakah ini data milik user tersebut?)
2. Perlu cek **role/peran** (apakah user adalah admin/mentor/student?)
3. Ada **aturan akses** yang berbeda untuk setiap user

---

### Contoh Tanpa Policy (Kurang Rapi)

```php
// Cek otorisasi manual di controller (berantakan!)
public function update(Request $request, $id)
{
    $course = Course::findOrFail($id);

    // Cek manual: apakah user boleh edit?
    if (auth()->user()->role !== 'admin' &&
        auth()->user()->role !== 'mentor') {
        return $this->forbiddenResponse('Anda tidak punya akses');
    }

    // Cek lagi: apakah mentor ini yang buat kursus ini?
    if (auth()->user()->role === 'mentor' &&
        $course->mentor_id !== auth()->id()) {
        return $this->forbiddenResponse('Ini bukan kursus Anda');
    }

    // Baru update...
    $course->update($request->validated());

    return $this->successResponse($course);
}
```

---

### Contoh Dengan Policy (Lebih Rapi)

**File Policy (`app/Policies/CoursePolicy.php`):**

```php
class CoursePolicy
{
    /**
     * Apakah user boleh UPDATE kursus ini?
     */
    public function update(User $user, Course $course): bool
    {
        // Admin boleh update semua kursus
        if ($user->role === 'admin') {
            return true;
        }

        // Mentor hanya boleh update kursus miliknya
        if ($user->role === 'mentor') {
            return $course->mentor_id === $user->id;
        }

        // Selain itu, tidak boleh
        return false;
    }

    /**
     * Apakah user boleh DELETE kursus ini?
     */
    public function delete(User $user, Course $course): bool
    {
        // Hanya admin yang boleh hapus
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh CREATE kursus baru?
     */
    public function create(User $user): bool
    {
        // Admin dan mentor boleh buat kursus
        return in_array($user->role, ['admin', 'mentor']);
    }
}
```

**File Controller (jadi BERSIH!):**

```php
public function update(UpdateCourseRequest $request, $id)
{
    $course = Course::findOrFail($id);

    // Satu baris! Cek otomatis pakai Policy
    $this->authorize('update', $course);

    // Jika sampai sini, berarti BOLEH update
    $course->update($request->validated());

    return $this->successResponse($course, 'Kursus berhasil diupdate');
}
```

---

### Cara Kerja Policy

```
┌─────────────────────────────────────────────────────────────────┐
│                         REQUEST MASUK                            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  1. MIDDLEWARE (auth:api)                                        │
│     ➜ Cek: Apakah sudah LOGIN?                                   │
│     ➜ Jika belum → Error 401 (Unauthorized)                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. REQUEST CLASS                                                │
│     ➜ Cek: Apakah DATA yang dikirim VALID?                       │
│     ➜ Jika tidak valid → Error 422 (Validation Error)            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. POLICY (di dalam Controller)                                 │
│     ➜ Cek: Apakah user BOLEH melakukan aksi ini?                 │
│     ➜ Jika tidak boleh → Error 403 (Forbidden)                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  4. SERVICE                                                      │
│     ➜ Proses logika bisnis yang kompleks                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. RESPONSE (Resource)                                          │
│     ➜ Format dan kirim hasil ke user                             │
└─────────────────────────────────────────────────────────────────┘
```

---

### File Policy yang Tersedia

| File                         | Fungsi                            |
| ---------------------------- | --------------------------------- |
| `AchievementPolicy.php`      | Aturan akses untuk prestasi       |
| `ArticlePolicy.php`          | Aturan akses untuk artikel        |
| `CoursePolicy.php`           | Aturan akses untuk kursus         |
| `EnrollmentPolicy.php`       | Aturan akses untuk enrollment     |
| `ExperiencePolicy.php`       | Aturan akses untuk pengalaman     |
| `MentoringSessionPolicy.php` | Aturan akses untuk mentoring      |
| `OrganizationPolicy.php`     | Aturan akses untuk organisasi     |
| `ReviewPolicy.php`           | Aturan akses untuk review         |
| `ScholarshipPolicy.php`      | Aturan akses untuk beasiswa       |
| `SubscriptionPolicy.php`     | Aturan akses untuk langganan      |
| `TransactionPolicy.php`      | Aturan akses untuk transaksi      |
| `UserPolicy.php`             | Aturan akses untuk manajemen user |

---

### File Service yang Tersedia

| File                      | Fungsi                             |
| ------------------------- | ---------------------------------- |
| `AchievementService.php`  | Logika bisnis untuk prestasi       |
| `ArticleService.php`      | Logika bisnis untuk artikel        |
| `CourseService.php`       | Logika bisnis untuk kursus         |
| `EnrollmentService.php`   | Logika bisnis untuk enrollment     |
| `ExperienceService.php`   | Logika bisnis untuk pengalaman     |
| `MentoringService.php`    | Logika bisnis untuk mentoring      |
| `MidtransService.php`     | Integrasi pembayaran Midtrans      |
| `OrganizationService.php` | Logika bisnis untuk organisasi     |
| `ReviewService.php`       | Logika bisnis untuk review         |
| `ScholarshipService.php`  | Logika bisnis untuk beasiswa       |
| `SubscriptionService.php` | Logika bisnis untuk langganan      |
| `TransactionService.php`  | Logika bisnis untuk transaksi      |
| `UserService.php`         | Logika bisnis untuk manajemen user |

---

### Cara Pakai Policy di Controller

```php
// Cara 1: Pakai $this->authorize()
public function update(Request $request, $id)
{
    $course = Course::findOrFail($id);

    // Jika tidak boleh, otomatis throw error 403
    $this->authorize('update', $course);

    // Lanjut proses...
}

// Cara 2: Pakai Gate::allows() - lebih fleksibel
public function update(Request $request, $id)
{
    $course = Course::findOrFail($id);

    if (!Gate::allows('update', $course)) {
        return $this->forbiddenResponse('Anda tidak punya akses');
    }

    // Lanjut proses...
}

// Cara 3: Pakai user()->can() - untuk kondisional
public function show($id)
{
    $course = Course::findOrFail($id);

    // Cek apakah boleh edit, untuk tampilkan tombol edit
    $canEdit = auth()->user()->can('update', $course);

    return $this->successResponse([
        'course'   => $course,
        'can_edit' => $canEdit,
    ]);
}
```

---

### Perbandingan Middleware vs Policy

| Aspek           | Middleware                | Policy                          |
| --------------- | ------------------------- | ------------------------------- |
| **Lokasi**      | `app/Http/Middleware/`    | `app/Policies/`                 |
| **Kapan jalan** | Sebelum masuk controller  | Di dalam controller             |
| **Cek apa**     | Status umum (login, role) | Akses ke data SPESIFIK          |
| **Contoh**      | "Apakah sudah login?"     | "Apakah boleh edit KURSUS INI?" |
| **Error code**  | 401 (Unauthorized)        | 403 (Forbidden)                 |

---

### Keuntungan Pakai Service & Policy

| Keuntungan             | Penjelasan                                                    |
| ---------------------- | ------------------------------------------------------------- |
| **Controller bersih**  | Controller hanya jadi "penghubung", logika ada di tempat lain |
| **Mudah di-test**      | Service dan Policy bisa di-unit test terpisah                 |
| **Bisa dipakai ulang** | Satu Service/Policy bisa dipanggil dari banyak controller     |
| **Konsisten**          | Aturan akses sama di semua tempat                             |
| **Mudah diubah**       | Mau ubah aturan? Edit di satu tempat saja                     |

---

## 📊 Ringkasan: Siapa Ngapain?

```
┌────────────────────────────────────────────────────────────────────────┐
│                         ARSITEKTUR APLIKASI                             │
├────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   REQUEST          →  Validasi input (format, tipe data)                │
│   ════════                                                              │
│                                                                         │
│   MIDDLEWARE       →  Cek status umum (login? role?)                    │
│   ══════════                                                            │
│                                                                         │
│   POLICY           →  Cek izin akses data spesifik                      │
│   ══════                                                                │
│                                                                         │
│   CONTROLLER       →  Terima request, panggil service, return response  │
│   ══════════                                                            │
│                                                                         │
│   SERVICE          →  Logika bisnis kompleks                            │
│   ═══════                                                               │
│                                                                         │
│   MODEL            →  Representasi tabel database                       │
│   ═════                                                                 │
│                                                                         │
│   RESOURCE         →  Format output/response                            │
│   ════════                                                              │
│                                                                         │
└────────────────────────────────────────────────────────────────────────┘
```

---

## ❓ FAQ (Pertanyaan Umum)

**Q: Apa bedanya GET, POST, PUT, DELETE?**

-   GET = Ambil data (baca)
-   POST = Kirim data baru (buat)
-   PUT = Update data yang sudah ada
-   DELETE = Hapus data

**Q: Kenapa dapat error 401?**

-   Artinya kamu belum login atau token sudah expired

**Q: Kenapa dapat error 422?**

-   Artinya data yang kamu kirim tidak valid (contoh: email format salah)

**Q: Kenapa dapat error 404?**

-   Artinya data yang kamu cari tidak ditemukan

---

## 📞 Butuh Bantuan?

Jika ada yang tidak dipahami, baca ulang komentar di dalam file controller.
Semua sudah ditulis dalam Bahasa Indonesia agar mudah dipahami.

**Selamat belajar! 🎉**

---

## 📖 DOKUMENTASI LENGKAP: SERVICES

### 1. AchievementService

**Lokasi:** `app/Services/AchievementService.php`

**Fungsi:** Mengelola prestasi/pencapaian user

| Method                  | Parameter                                 | Return        | Deskripsi                                |
| ----------------------- | ----------------------------------------- | ------------- | ---------------------------------------- |
| `getUserAchievements()` | `int $userId`, `array $filters`           | `Paginator`   | Ambil daftar prestasi user dengan filter |
| `createAchievement()`   | `User $user`, `array $data`               | `Achievement` | Buat prestasi baru                       |
| `updateAchievement()`   | `Achievement $achievement`, `array $data` | `Achievement` | Update prestasi                          |
| `deleteAchievement()`   | `Achievement $achievement`                | `bool`        | Hapus prestasi                           |
| `getStatistics()`       | `int $userId`                             | `array`       | Ambil statistik prestasi user            |

**Contoh Penggunaan:**

```php
// Di Controller
$achievements = $this->achievementService->getUserAchievements(
    Auth::id(),
    ['type' => 'competition', 'level' => 'national']
);

$achievement = $this->achievementService->createAchievement(
    Auth::user(),
    $request->validated()
);
```

---

### 2. ArticleService

**Lokasi:** `app/Services/ArticleService.php`

**Fungsi:** Mengelola artikel/blog

| Method                    | Parameter                                                     | Return      | Deskripsi                                   |
| ------------------------- | ------------------------------------------------------------- | ----------- | ------------------------------------------- |
| `getArticles()`           | `array $filters`                                              | `Paginator` | Ambil daftar artikel dengan filter          |
| `getArticleWithDetails()` | `int $id`                                                     | `Article`   | Ambil detail artikel + increment views      |
| `createArticle()`         | `User $author`, `array $data`, `?UploadedFile $thumbnail`     | `Article`   | Buat artikel baru                           |
| `updateArticle()`         | `Article $article`, `array $data`, `?UploadedFile $thumbnail` | `Article`   | Update artikel                              |
| `deleteArticle()`         | `Article $article`                                            | `bool`      | Hapus artikel + file thumbnail              |
| `publishArticle()`        | `Article $article`                                            | `Article`   | Publish artikel (set status published)      |
| `getStatistics()`         | -                                                             | `array`     | Statistik artikel (total, published, draft) |

**Contoh Penggunaan:**

```php
// Ambil artikel dengan filter
$articles = $this->articleService->getArticles([
    'status' => 'published',
    'category' => 'technology',
    'search' => 'laravel'
]);

// Buat artikel dengan thumbnail
$article = $this->articleService->createArticle(
    Auth::user(),
    $request->validated(),
    $request->file('thumbnail')
);
```

---

### 3. CourseService

**Lokasi:** `app/Services/CourseService.php`

**Fungsi:** Mengelola kursus dan bootcamp

| Method                   | Parameter                                               | Return      | Deskripsi                         |
| ------------------------ | ------------------------------------------------------- | ----------- | --------------------------------- |
| `getCourses()`           | `array $filters`                                        | `Paginator` | Ambil daftar kursus dengan filter |
| `getCourseWithDetails()` | `int $id`                                               | `Course`    | Ambil detail kursus + relasi      |
| `createCourse()`         | `array $data`                                           | `Course`    | Buat kursus baru                  |
| `updateCourse()`         | `Course $course`, `array $data`, `?UploadedFile $video` | `Course`    | Update kursus                     |
| `deleteCourse()`         | `Course $course`                                        | `bool`      | Hapus kursus + file video         |

**Filter yang tersedia:**

-   `type` - Tipe kursus (course, bootcamp)
-   `level` - Level (beginner, intermediate, advanced)
-   `access_type` - Tipe akses (free, paid, subscription)
-   `search` - Cari di title & description

**Contoh Penggunaan:**

```php
// Ambil kursus gratis level pemula
$courses = $this->courseService->getCourses([
    'level' => 'beginner',
    'access_type' => 'free'
]);

// Update kursus dengan video baru
$course = $this->courseService->updateCourse(
    $course,
    $request->validated(),
    $request->file('video_file')
);
```

---

### 4. EnrollmentService

**Lokasi:** `app/Services/EnrollmentService.php`

**Fungsi:** Mengelola pendaftaran kursus

| Method                 | Parameter                                 | Return       | Deskripsi                    |
| ---------------------- | ----------------------------------------- | ------------ | ---------------------------- |
| `getUserEnrollments()` | `int $userId`, `array $filters`           | `Paginator`  | Ambil daftar enrollment user |
| `enrollCourse()`       | `User $user`, `int $courseId`             | `array`      | Daftarkan user ke kursus     |
| `updateProgress()`     | `Enrollment $enrollment`, `int $progress` | `Enrollment` | Update progress belajar      |
| `markComplete()`       | `Enrollment $enrollment`                  | `Enrollment` | Tandai kursus selesai        |
| `getStatistics()`      | `int $userId`                             | `array`      | Statistik enrollment user    |

**Contoh Penggunaan:**

```php
// Daftarkan user ke kursus
$result = $this->enrollmentService->enrollCourse(Auth::user(), $courseId);

if (!$result['success']) {
    return $this->errorResponse($result['message']);
}

// Update progress
$enrollment = $this->enrollmentService->updateProgress($enrollment, 75);

// Tandai selesai
$enrollment = $this->enrollmentService->markComplete($enrollment);
```

---

### 5. ExperienceService

**Lokasi:** `app/Services/ExperienceService.php`

**Fungsi:** Mengelola pengalaman kerja/pendidikan user

| Method                      | Parameter                               | Return       | Deskripsi                        |
| --------------------------- | --------------------------------------- | ------------ | -------------------------------- |
| `getUserExperiences()`      | `int $userId`, `array $filters`         | `Paginator`  | Ambil daftar pengalaman user     |
| `createExperience()`        | `User $user`, `array $data`             | `Experience` | Buat pengalaman baru             |
| `updateExperience()`        | `Experience $experience`, `array $data` | `Experience` | Update pengalaman                |
| `deleteExperience()`        | `Experience $experience`                | `bool`       | Hapus pengalaman                 |
| `getWorkExperiences()`      | `int $userId`                           | `Collection` | Ambil pengalaman kerja saja      |
| `getEducationExperiences()` | `int $userId`                           | `Collection` | Ambil pengalaman pendidikan saja |
| `getStatistics()`           | `int $userId`                           | `array`      | Statistik pengalaman user        |

**Contoh Penggunaan:**

```php
// Ambil pengalaman kerja
$workExperiences = $this->experienceService->getWorkExperiences(Auth::id());

// Statistik pengalaman
$stats = $this->experienceService->getStatistics(Auth::id());
// Output: ['total' => 5, 'work' => 2, 'education' => 2, 'volunteer' => 1, ...]
```

---

### 6. MentoringService

**Lokasi:** `app/Services/MentoringService.php`

**Fungsi:** Mengelola sesi mentoring

| Method                | Parameter                                     | Return             | Deskripsi                   |
| --------------------- | --------------------------------------------- | ------------------ | --------------------------- |
| `getSessions()`       | `User $user`, `array $filters`                | `Paginator`        | Ambil daftar sesi mentoring |
| `createSession()`     | `User $user`, `array $data`                   | `MentoringSession` | Buat sesi baru              |
| `updateSession()`     | `MentoringSession $session`, `array $data`    | `MentoringSession` | Update sesi                 |
| `updateStatus()`      | `MentoringSession $session`, `string $status` | `MentoringSession` | Update status sesi          |
| `giveFeedback()`      | `MentoringSession $session`, `array $data`    | `MentoringSession` | Berikan feedback            |
| `getMentorSchedule()` | `int $mentorId`, `array $filters`             | `array`            | Ambil jadwal mentor         |

**Status yang tersedia:**

-   `pending` - Menunggu konfirmasi
-   `confirmed` - Sudah dikonfirmasi
-   `completed` - Sudah selesai
-   `cancelled` - Dibatalkan

**Contoh Penggunaan:**

```php
// Buat sesi mentoring
$session = $this->mentoringService->createSession(Auth::user(), [
    'mentor_id' => 5,
    'type' => 'academic',
    'session_date' => '2024-01-15 10:00:00',
    'duration' => 60,
    'topic' => 'Review CV'
]);

// Update status oleh mentor
$session = $this->mentoringService->updateStatus($session, 'confirmed');

// Berikan feedback setelah selesai
$session = $this->mentoringService->giveFeedback($session, [
    'rating' => 5,
    'feedback' => 'Sangat membantu!'
]);
```

---

### 7. ScholarshipService

**Lokasi:** `app/Services/ScholarshipService.php`

**Fungsi:** Mengelola beasiswa dan lamaran

| Method                      | Parameter                                                               | Return                   | Deskripsi                           |
| --------------------------- | ----------------------------------------------------------------------- | ------------------------ | ----------------------------------- |
| `getScholarships()`         | `array $filters`                                                        | `Paginator`              | Ambil daftar beasiswa dengan filter |
| `createScholarship()`       | `array $data`                                                           | `Scholarship`            | Buat beasiswa baru                  |
| `updateScholarship()`       | `Scholarship $scholarship`, `array $data`                               | `Scholarship`            | Update beasiswa                     |
| `deleteScholarship()`       | `Scholarship $scholarship`                                              | `bool`                   | Hapus beasiswa                      |
| `applyScholarship()`        | `User $user`, `Scholarship $scholarship`, `array $data`, `array $files` | `array`                  | Melamar beasiswa                    |
| `getUserApplications()`     | `int $userId`                                                           | `Paginator`              | Ambil lamaran user                  |
| `updateApplicationStatus()` | `ScholarshipApplication $app`, `string $status`                         | `ScholarshipApplication` | Update status lamaran               |

**Filter yang tersedia:**

-   `status` - Status beasiswa (open, closed)
-   `location` - Lokasi beasiswa
-   `study_field` - Bidang studi
-   `search` - Cari di nama & deskripsi

**Contoh Penggunaan:**

```php
// Melamar beasiswa dengan upload dokumen
$result = $this->scholarshipService->applyScholarship(
    Auth::user(),
    $scholarship,
    $request->validated(),
    [
        'motivation_letter' => $request->file('motivation_letter'),
        'cv_path' => $request->file('cv'),
        'transcript_path' => $request->file('transcript')
    ]
);

// Update status lamaran (admin)
$application = $this->scholarshipService->updateApplicationStatus(
    $application,
    'accepted'
);
```

---

### 8. SubscriptionService

**Lokasi:** `app/Services/SubscriptionService.php`

**Fungsi:** Mengelola langganan premium

| Method                    | Parameter                       | Return          | Deskripsi                   |
| ------------------------- | ------------------------------- | --------------- | --------------------------- |
| `getUserSubscriptions()`  | `int $userId`                   | `Paginator`     | Ambil daftar langganan user |
| `createSubscription()`    | `User $user`, `array $data`     | `array`         | Buat langganan baru         |
| `cancelSubscription()`    | `Subscription $subscription`    | `Subscription`  | Batalkan langganan          |
| `renewSubscription()`     | `Subscription $subscription`    | `Subscription`  | Perpanjang langganan        |
| `getActiveSubscription()` | `int $userId`                   | `?Subscription` | Ambil langganan aktif user  |
| `checkAccess()`           | `User $user`, `string $feature` | `bool`          | Cek akses fitur premium     |

**Plan yang tersedia:**

-   `regular` - Rp 99.000/bulan
-   `premium` - Rp 199.000/bulan

**Contoh Penggunaan:**

```php
// Buat langganan
$result = $this->subscriptionService->createSubscription(Auth::user(), [
    'plan' => 'premium'
]);

// Cek akses fitur premium
if ($this->subscriptionService->checkAccess(Auth::user(), 'download_certificate')) {
    // User boleh download
}

// Ambil langganan aktif
$activeSubscription = $this->subscriptionService->getActiveSubscription(Auth::id());
```

---

### 9. TransactionService

**Lokasi:** `app/Services/TransactionService.php`

**Fungsi:** Mengelola transaksi pembayaran

| Method                            | Parameter                                                | Return        | Deskripsi                     |
| --------------------------------- | -------------------------------------------------------- | ------------- | ----------------------------- |
| `getUserTransactions()`           | `User $user`, `array $filters`                           | `Paginator`   | Ambil daftar transaksi user   |
| `createCourseTransaction()`       | `User $user`, `Course $course`, `array $data`            | `array`       | Buat transaksi kursus         |
| `createSubscriptionTransaction()` | `User $user`, `array $data`                              | `array`       | Buat transaksi langganan      |
| `createMentoringTransaction()`    | `User $user`, `MentoringSession $session`, `array $data` | `array`       | Buat transaksi mentoring      |
| `uploadPaymentProof()`            | `Transaction $transaction`, `UploadedFile $file`         | `Transaction` | Upload bukti bayar            |
| `confirmPayment()`                | `Transaction $transaction`                               | `Transaction` | Konfirmasi pembayaran (admin) |
| `requestRefund()`                 | `Transaction $transaction`, `string $reason`             | `Transaction` | Ajukan refund                 |
| `getStatistics()`                 | -                                                        | `array`       | Statistik transaksi           |

**Payment method yang tersedia:**

-   `qris` - QRIS
-   `bank_transfer` - Transfer Bank
-   `virtual_account` - Virtual Account
-   `credit_card` - Kartu Kredit
-   `manual` - Transfer Manual

**Contoh Penggunaan:**

```php
// Buat transaksi kursus
$result = $this->transactionService->createCourseTransaction(
    Auth::user(),
    $course,
    ['payment_method' => 'bank_transfer']
);

// Response includes Midtrans snap_token for payment
// $result['data']['snap_token']

// Upload bukti bayar (untuk manual transfer)
$transaction = $this->transactionService->uploadPaymentProof(
    $transaction,
    $request->file('payment_proof')
);

// Admin konfirmasi pembayaran
$transaction = $this->transactionService->confirmPayment($transaction);
```

---

### 10. UserService

**Lokasi:** `app/Services/UserService.php`

**Fungsi:** Mengelola user (untuk admin)

| Method                 | Parameter                      | Return      | Deskripsi                       |
| ---------------------- | ------------------------------ | ----------- | ------------------------------- |
| `getUsers()`           | `array $filters`               | `Paginator` | Ambil daftar user dengan filter |
| `getUserWithDetails()` | `int $id`                      | `User`      | Ambil detail user + relasi      |
| `createUser()`         | `array $data`                  | `User`      | Buat user baru                  |
| `updateUser()`         | `User $user`, `array $data`    | `User`      | Update user                     |
| `deleteUser()`         | `User $user`                   | `bool`      | Hapus user                      |
| `updateStatus()`       | `User $user`, `string $status` | `User`      | Update status user              |
| `suspendUser()`        | `User $user`                   | `User`      | Suspend user                    |
| `activateUser()`       | `User $user`                   | `User`      | Aktifkan user                   |
| `getMentors()`         | `array $filters`               | `Paginator` | Ambil daftar mentor             |
| `getStatistics()`      | -                              | `array`     | Statistik user                  |

**Status user:**

-   `active` - Aktif
-   `inactive` - Tidak aktif
-   `suspended` - Ditangguhkan

**Contoh Penggunaan:**

```php
// Ambil user dengan filter
$users = $this->userService->getUsers([
    'role' => 'student',
    'status' => 'active',
    'search' => 'john'
]);

// Suspend user
$user = $this->userService->suspendUser($user);

// Statistik user
$stats = $this->userService->getStatistics();
// Output: ['total' => 100, 'admins' => 2, 'students' => 80, 'mentors' => 18, ...]
```

---

### 11. MidtransService

**Lokasi:** `app/Services/MidtransService.php`

**Fungsi:** Integrasi payment gateway Midtrans

| Method                     | Parameter                                                           | Return  | Deskripsi                     |
| -------------------------- | ------------------------------------------------------------------- | ------- | ----------------------------- |
| `buildTransactionParams()` | `string $orderId`, `int $amount`, `array $items`, `array $customer` | `array` | Build parameter transaksi     |
| `createTransaction()`      | `array $params`                                                     | `array` | Buat transaksi di Midtrans    |
| `getTransactionStatus()`   | `string $orderId`                                                   | `array` | Cek status transaksi          |
| `handleNotification()`     | `array $notification`                                               | `array` | Handle callback dari Midtrans |

**Contoh Penggunaan:**

```php
// Build parameter
$params = $this->midtransService->buildTransactionParams(
    'TRX-123456',
    150000,
    [
        ['id' => 1, 'price' => 150000, 'quantity' => 1, 'name' => 'Kursus Laravel']
    ],
    ['first_name' => 'John', 'email' => 'john@example.com', 'phone' => '08123456789']
);

// Buat transaksi
$response = $this->midtransService->createTransaction($params);
// $response['snap_token'] - untuk frontend
// $response['redirect_url'] - untuk redirect ke Midtrans
```

---

## 📖 DOKUMENTASI LENGKAP: POLICIES

### 1. AchievementPolicy

**Lokasi:** `app/Policies/AchievementPolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh   | Deskripsi             |
| --------- | ------------------ | --------------------- |
| `viewAny` | Semua user login   | Lihat daftar prestasi |
| `view`    | Pemilik atau Admin | Lihat detail prestasi |
| `create`  | Semua user login   | Buat prestasi baru    |
| `update`  | Pemilik saja       | Update prestasi       |
| `delete`  | Pemilik atau Admin | Hapus prestasi        |

**Contoh:**

```php
// Di Controller
$this->authorize('update', $achievement);
// Hanya pemilik achievement yang bisa update
```

---

### 2. ArticlePolicy

**Lokasi:** `app/Policies/ArticlePolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh       | Deskripsi            |
| --------- | ---------------------- | -------------------- |
| `viewAny` | Semua (termasuk guest) | Lihat daftar artikel |
| `view`    | Semua (termasuk guest) | Lihat detail artikel |
| `create`  | Admin atau Mentor      | Buat artikel baru    |
| `update`  | Penulis atau Admin     | Update artikel       |
| `delete`  | Penulis atau Admin     | Hapus artikel        |
| `publish` | Admin saja             | Publish artikel      |

---

### 3. CoursePolicy

**Lokasi:** `app/Policies/CoursePolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh       | Deskripsi           |
| --------- | ---------------------- | ------------------- |
| `viewAny` | Semua (termasuk guest) | Lihat daftar kursus |
| `view`    | Semua (termasuk guest) | Lihat detail kursus |
| `create`  | Admin saja             | Buat kursus baru    |
| `update`  | Admin saja             | Update kursus       |
| `delete`  | Admin saja             | Hapus kursus        |

---

### 4. EnrollmentPolicy

**Lokasi:** `app/Policies/EnrollmentPolicy.php`

**Aturan Akses:**

| Method           | Siapa yang Boleh   | Deskripsi               |
| ---------------- | ------------------ | ----------------------- |
| `viewAny`        | Semua user login   | Lihat daftar enrollment |
| `view`           | Pemilik atau Admin | Lihat detail enrollment |
| `create`         | Semua user login   | Daftar ke kursus        |
| `update`         | Pemilik atau Admin | Update enrollment       |
| `delete`         | Pemilik atau Admin | Hapus enrollment        |
| `updateProgress` | Pemilik saja       | Update progress belajar |

---

### 5. ExperiencePolicy

**Lokasi:** `app/Policies/ExperiencePolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh   | Deskripsi               |
| --------- | ------------------ | ----------------------- |
| `viewAny` | Semua user login   | Lihat daftar pengalaman |
| `view`    | Pemilik atau Admin | Lihat detail pengalaman |
| `create`  | Semua user login   | Buat pengalaman baru    |
| `update`  | Pemilik saja       | Update pengalaman       |
| `delete`  | Pemilik atau Admin | Hapus pengalaman        |

---

### 6. MentoringSessionPolicy

**Lokasi:** `app/Policies/MentoringSessionPolicy.php`

**Aturan Akses:**

| Method         | Siapa yang Boleh            | Deskripsi          |
| -------------- | --------------------------- | ------------------ |
| `viewAny`      | Semua user login            | Lihat daftar sesi  |
| `view`         | Peserta, Mentor, atau Admin | Lihat detail sesi  |
| `create`       | Semua user login            | Buat sesi baru     |
| `update`       | Peserta, Mentor, atau Admin | Update sesi        |
| `delete`       | Peserta atau Admin          | Hapus sesi         |
| `updateStatus` | Mentor atau Admin           | Update status sesi |
| `giveFeedback` | Peserta atau Mentor         | Berikan feedback   |

---

### 7. OrganizationPolicy

**Lokasi:** `app/Policies/OrganizationPolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh   | Deskripsi               |
| --------- | ------------------ | ----------------------- |
| `viewAny` | Semua user login   | Lihat daftar organisasi |
| `view`    | Pemilik atau Admin | Lihat detail organisasi |
| `create`  | Semua user login   | Buat organisasi baru    |
| `update`  | Pemilik atau Admin | Update organisasi       |
| `delete`  | Pemilik atau Admin | Hapus organisasi        |

---

### 8. ReviewPolicy

**Lokasi:** `app/Policies/ReviewPolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh       | Deskripsi           |
| --------- | ---------------------- | ------------------- |
| `viewAny` | Semua (termasuk guest) | Lihat daftar review |
| `view`    | Semua (termasuk guest) | Lihat detail review |
| `create`  | Semua user login       | Buat review baru    |
| `update`  | Penulis saja           | Update review       |
| `delete`  | Penulis atau Admin     | Hapus review        |

---

### 9. ScholarshipPolicy

**Lokasi:** `app/Policies/ScholarshipPolicy.php`

**Aturan Akses:**

| Method                    | Siapa yang Boleh                    | Deskripsi             |
| ------------------------- | ----------------------------------- | --------------------- |
| `viewAny`                 | Semua (termasuk guest)              | Lihat daftar beasiswa |
| `view`                    | Semua (termasuk guest)              | Lihat detail beasiswa |
| `create`                  | Corporate atau Admin                | Buat beasiswa baru    |
| `update`                  | Corporate atau Admin                | Update beasiswa       |
| `delete`                  | Corporate atau Admin                | Hapus beasiswa        |
| `apply`                   | Semua user login (jika status open) | Melamar beasiswa      |
| `updateApplicationStatus` | Admin saja                          | Update status lamaran |

---

### 10. SubscriptionPolicy

**Lokasi:** `app/Policies/SubscriptionPolicy.php`

**Aturan Akses:**

| Method    | Siapa yang Boleh   | Deskripsi               |
| --------- | ------------------ | ----------------------- |
| `viewAny` | Semua user login   | Lihat daftar langganan  |
| `view`    | Pemilik atau Admin | Lihat detail langganan  |
| `create`  | Semua user login   | Buat langganan baru     |
| `update`  | Pemilik atau Admin | Update langganan        |
| `delete`  | Pemilik atau Admin | Hapus langganan         |
| `upgrade` | Pemilik saja       | Upgrade paket langganan |

---

### 11. TransactionPolicy

**Lokasi:** `app/Policies/TransactionPolicy.php`

**Aturan Akses:**

| Method           | Siapa yang Boleh              | Deskripsi                 |
| ---------------- | ----------------------------- | ------------------------- |
| `viewAny`        | Semua user login              | Lihat daftar transaksi    |
| `view`           | Pemilik atau Admin            | Lihat detail transaksi    |
| `create`         | Semua user login              | Buat transaksi baru       |
| `uploadProof`    | Pemilik (jika status pending) | Upload bukti bayar        |
| `confirmPayment` | Admin saja                    | Konfirmasi pembayaran     |
| `requestRefund`  | Pemilik (jika status paid)    | Ajukan refund             |
| `viewStatistics` | Admin saja                    | Lihat statistik transaksi |

---

### 12. UserPolicy

**Lokasi:** `app/Policies/UserPolicy.php`

**Aturan Akses:**

| Method           | Siapa yang Boleh                | Deskripsi            |
| ---------------- | ------------------------------- | -------------------- |
| `viewAny`        | Admin saja                      | Lihat daftar user    |
| `view`           | Pemilik atau Admin              | Lihat detail user    |
| `create`         | Admin saja                      | Buat user baru       |
| `update`         | Admin saja (bukan diri sendiri) | Update user          |
| `delete`         | Admin saja (bukan diri sendiri) | Hapus user           |
| `suspend`        | Admin saja (bukan diri sendiri) | Suspend user         |
| `activate`       | Admin saja                      | Aktifkan user        |
| `viewStatistics` | Admin saja                      | Lihat statistik user |

---

## 🔧 Cara Register Policy di Laravel 12

**Lokasi:** `app/Providers/AppServiceProvider.php`

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    // Register semua policy
    Gate::policy(Achievement::class, AchievementPolicy::class);
    Gate::policy(Article::class, ArticlePolicy::class);
    Gate::policy(Course::class, CoursePolicy::class);
    Gate::policy(Enrollment::class, EnrollmentPolicy::class);
    Gate::policy(Experience::class, ExperiencePolicy::class);
    Gate::policy(MentoringSession::class, MentoringSessionPolicy::class);
    Gate::policy(Organization::class, OrganizationPolicy::class);
    Gate::policy(Review::class, ReviewPolicy::class);
    Gate::policy(Scholarship::class, ScholarshipPolicy::class);
    Gate::policy(Subscription::class, SubscriptionPolicy::class);
    Gate::policy(Transaction::class, TransactionPolicy::class);
    Gate::policy(User::class, UserPolicy::class);

    // Gate tambahan untuk cek role
    Gate::define('admin', fn($user) => $user->role === 'admin');
    Gate::define('mentor', fn($user) => $user->role === 'mentor');
    Gate::define('corporate', fn($user) => $user->role === 'corporate');
}
```

---

## 📋 Quick Reference: Error Codes

| Code    | Nama                 | Kapan Terjadi                     |
| ------- | -------------------- | --------------------------------- |
| **200** | OK                   | Request berhasil                  |
| **201** | Created              | Data berhasil dibuat              |
| **400** | Bad Request          | Request tidak valid               |
| **401** | Unauthorized         | Belum login / token expired       |
| **403** | Forbidden            | Tidak punya akses (Policy reject) |
| **404** | Not Found            | Data tidak ditemukan              |
| **422** | Unprocessable Entity | Validasi gagal                    |
| **500** | Server Error         | Error di server                   |

---

## 📋 Quick Reference: User Roles

| Role        | Deskripsi        | Bisa Akses                          |
| ----------- | ---------------- | ----------------------------------- |
| `admin`     | Administrator    | Semua fitur                         |
| `mentor`    | Mentor/Pengajar  | Buat artikel, kelola sesi mentoring |
| `student`   | Pelajar/Peserta  | Daftar kursus, melamar beasiswa     |
| `corporate` | Mitra Perusahaan | Buat beasiswa, kelola organisasi    |

---

**Dokumentasi ini dibuat untuk Laravel 12.x** 🚀

```

```
