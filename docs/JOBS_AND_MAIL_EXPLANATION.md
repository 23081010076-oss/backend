# 📧 Penjelasan Folder Jobs & Mail di Laravel

## 📋 Overview

Folder `Jobs` dan `Mail` adalah bagian dari **Queue System** Laravel yang digunakan untuk menjalankan tugas-tugas berat di **background** (latar belakang) agar tidak memperlambat response aplikasi.

---

## 📁 Folder `app/Mail`

### Fungsi
Folder ini berisi **template email** yang akan dikirim ke user. Setiap file di sini adalah class yang mendefinisikan:
- Subject email
- Isi email (view template)
- Data yang dikirim ke template
- Attachment (jika ada)

### File yang Ada

#### 1. **WelcomeEmail.php**

**Lokasi**: `app/Mail/WelcomeEmail.php`

**Fungsi**: Template email selamat datang yang dikirim saat user register.

**Isi**:
```php
class WelcomeEmail extends Mailable
{
    public User $user;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di Learning Platform!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',  // Template blade
            with: [
                'userName' => $this->user->name,
                'userRole' => $this->user->role,
            ],
        );
    }
}
```

**Cara Kerja**:
1. Menerima data `User`
2. Set subject email
3. Load view template dari `resources/views/emails/welcome.blade.php`
4. Kirim data `userName` dan `userRole` ke template

**Template Email** (`resources/views/emails/welcome.blade.php`):
```html
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Halo {{ $userName }}!</h1>
    <p>Selamat datang di Learning Platform.</p>
    <p>Anda terdaftar sebagai: <strong>{{ $userRole }}</strong></p>
    <p>Silakan login untuk mulai belajar!</p>
</body>
</html>
```

---

## 📁 Folder `app/Jobs`

### Fungsi
Folder ini berisi **background jobs** yang dijalankan secara **asynchronous** (tidak langsung). Jobs ini masuk ke **queue** (antrian) dan diproses oleh **queue worker**.

### Kenapa Pakai Jobs?

**❌ Tanpa Jobs** (Synchronous):
```php
// Di AuthController::register()
public function register(Request $request) {
    $user = User::create([...]);
    
    // Kirim email LANGSUNG (user harus tunggu)
    Mail::to($user->email)->send(new WelcomeEmail($user));
    // ⏱️ User tunggu 3-5 detik sampai email terkirim
    
    return response()->json([...]);
}
```
**Masalah**: User harus menunggu email terkirim (lambat!)

---

**✅ Dengan Jobs** (Asynchronous):
```php
// Di AuthController::register()
public function register(Request $request) {
    $user = User::create([...]);
    
    // Kirim email ke QUEUE (background)
    SendWelcomeEmail::dispatch($user);
    // ⚡ Langsung return response (cepat!)
    
    return response()->json([...]);
}
```
**Keuntungan**: User langsung dapat response, email dikirim di background!

---

### File Jobs yang Ada

#### 1. **SendWelcomeEmail.php**

**Lokasi**: `app/Jobs/SendWelcomeEmail.php`

**Fungsi**: Mengirim email selamat datang di background.

**Fitur**:
- ✅ Auto retry 3x jika gagal
- ✅ Timeout 60 detik
- ✅ Delay 30 detik antar retry
- ✅ Logging sukses/gagal
- ✅ Queue khusus: `emails`

**Code**:
```php
class SendWelcomeEmail implements ShouldQueue
{
    public int $tries = 3;      // Retry 3x
    public int $timeout = 60;   // Max 60 detik
    public int $backoff = 30;   // Delay 30 detik antar retry
    
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('emails');  // Queue khusus email
    }
    
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
        
        Log::info('Welcome email sent', [
            'user_id' => $this->user->id,
        ]);
    }
    
    public function failed(\Throwable $exception): void
    {
        // Jika gagal setelah 3x retry
        Log::critical('Welcome email permanently failed');
    }
}
```

**Cara Pakai**:
```php
// Di controller
SendWelcomeEmail::dispatch($user);  // Masuk queue
```

---

#### 2. **ProcessPaymentCallback.php**

**Lokasi**: `app/Jobs/ProcessPaymentCallback.php`

**Fungsi**: Memproses callback dari Midtrans (payment gateway) di background.

**Fitur**:
- ✅ Auto retry 5x jika gagal
- ✅ Timeout 30 detik
- ✅ Delay 10 detik antar retry
- ✅ Queue khusus: `payments`
- ✅ Auto kirim email konfirmasi jika payment sukses

**Code**:
```php
class ProcessPaymentCallback implements ShouldQueue
{
    public int $tries = 5;      // Retry 5x
    public int $timeout = 30;   // Max 30 detik
    public int $backoff = 10;   // Delay 10 detik
    
    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->onQueue('payments');  // Queue khusus payment
    }
    
    public function handle(): void
    {
        $orderId = $this->payload['order_id'];
        $status = $this->payload['transaction_status'];
        
        $transaction = Transaction::where('order_id', $orderId)->first();
        
        // Update status
        $newStatus = $this->determineStatus($status, $fraudStatus);
        $transaction->update(['status' => $newStatus]);
        
        // Jika payment sukses, kirim email
        if ($newStatus === 'paid') {
            SendNotificationEmail::dispatch($transaction->user, ...);
        }
    }
}
```

**Cara Pakai**:
```php
// Di MidtransWebhookController
ProcessPaymentCallback::dispatch($webhookData);
```

---

#### 3. **SendNotificationEmail.php**

**Lokasi**: `app/Jobs/SendNotificationEmail.php`

**Fungsi**: Mengirim email notifikasi umum (payment success, scholarship accepted, dll).

---

#### 4. **GenerateCertificatePdf.php**

**Lokasi**: `app/Jobs/GenerateCertificatePdf.php`

**Fungsi**: Generate sertifikat PDF di background (proses berat).

---

## 🔄 Cara Kerja Queue System

### 1. **Dispatch Job**
```php
// Di controller
SendWelcomeEmail::dispatch($user);
```

### 2. **Job Masuk Queue**
```
Queue: emails
┌─────────────────────────────┐
│ Job #1: SendWelcomeEmail    │
│ Job #2: SendWelcomeEmail    │
│ Job #3: SendNotificationEmail│
└─────────────────────────────┘
```

### 3. **Queue Worker Proses**
```bash
# Jalankan queue worker
php artisan queue:work
```

Worker akan:
- Ambil job dari queue
- Jalankan method `handle()`
- Jika gagal, retry sesuai `$tries`
- Jika sukses, hapus dari queue

---

## 🎯 Keuntungan Menggunakan Jobs

### 1. **Performance**
- ⚡ Response cepat (tidak tunggu email terkirim)
- ⚡ User experience lebih baik

### 2. **Reliability**
- 🔄 Auto retry jika gagal
- 📝 Logging lengkap
- 🛡️ Error handling

### 3. **Scalability**
- 📊 Bisa jalankan multiple workers
- 🎯 Queue terpisah per jenis (emails, payments)
- ⚙️ Prioritas queue

---

## 📊 Queue Configuration

### Database Queue (Default)

**Config**: `config/queue.php`
```php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],
],
```

**Table**: `jobs` (menyimpan queue)

---

## 🚀 Cara Menjalankan Queue

### Development
```bash
# Jalankan queue worker
php artisan queue:work

# Jalankan dengan auto-reload
php artisan queue:work --tries=3 --timeout=60
```

### Production
```bash
# Jalankan sebagai daemon (background)
php artisan queue:work --daemon

# Atau pakai Supervisor (recommended)
```

---

## 📝 Contoh Penggunaan di Project

### 1. **Register User**
```php
// AuthController::register()
$user = User::create([...]);

// Kirim welcome email (background)
SendWelcomeEmail::dispatch($user);

return response()->json([...]);  // Langsung return
```

### 2. **Payment Webhook**
```php
// MidtransWebhookController
public function handleNotification(Request $request)
{
    $payload = $request->all();
    
    // Proses payment di background
    ProcessPaymentCallback::dispatch($payload);
    
    return response()->json(['success' => true]);
}
```

### 3. **Generate Certificate**
```php
// CourseController
public function completeCourse($courseId)
{
    $enrollment->update(['completed' => true]);
    
    // Generate certificate di background
    GenerateCertificatePdf::dispatch($user, $course);
    
    return response()->json([...]);
}
```

---

## 🔍 Monitoring Queue

### Check Queue Status
```bash
# Lihat jumlah jobs di queue
php artisan queue:monitor

# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Database
```sql
-- Lihat jobs yang sedang di queue
SELECT * FROM jobs;

-- Lihat failed jobs
SELECT * FROM failed_jobs;
```

---

## 📌 Best Practices

### 1. **Gunakan Jobs untuk**:
- ✅ Kirim email
- ✅ Generate PDF/file besar
- ✅ Process payment callback
- ✅ Import/export data
- ✅ Image processing
- ✅ API calls ke third-party

### 2. **Jangan Gunakan Jobs untuk**:
- ❌ Query database sederhana
- ❌ Validasi input
- ❌ Response langsung ke user

### 3. **Tips**:
- Set `$tries` dan `$timeout` yang sesuai
- Gunakan queue terpisah per jenis job
- Logging untuk debugging
- Handle failed jobs dengan baik

---

## 🎓 Kesimpulan

| Folder | Fungsi | Contoh |
|--------|--------|--------|
| **Mail** | Template email | WelcomeEmail, PaymentConfirmation |
| **Jobs** | Background tasks | SendWelcomeEmail, ProcessPayment |

**Workflow**:
1. User register → `SendWelcomeEmail::dispatch($user)`
2. Job masuk queue → Database table `jobs`
3. Queue worker ambil job → Kirim email via `WelcomeEmail`
4. Email terkirim → Job dihapus dari queue

**Keuntungan**: Response cepat, reliable, scalable! 🚀
