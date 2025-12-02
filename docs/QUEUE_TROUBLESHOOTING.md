# 🔧 Queue Worker Troubleshooting Guide

## ✅ Masalah Terselesaikan

Setelah troubleshooting, ditemukan bahwa:

1. ✅ **Database connection OK** - MySQL running dengan baik
2. ✅ **Migrations OK** - Table `jobs` dan `failed_jobs` sudah ada
3. ✅ **Queue worker bisa jalan** - Dengan flag `--once` berhasil

---

## 🎯 Solusi untuk Development

### Cara 1: Gunakan `--once` (Recommended untuk Testing)

```bash
# Jalankan 1 job lalu stop
php artisan queue:work --once
```

**Keuntungan**:
- ✅ Tidak hang
- ✅ Cocok untuk testing
- ✅ Bisa lihat output langsung

**Cara pakai**:
```bash
# Terminal 1: Jalankan aplikasi
php artisan serve

# Terminal 2: Jalankan queue worker saat ada job
php artisan queue:work --once --verbose
```

---

### Cara 2: Gunakan `sync` Driver (Paling Mudah)

**File**: `.env`
```env
# Ubah dari database ke sync
QUEUE_CONNECTION=sync
```

Lalu restart:
```bash
php artisan config:clear
```

**Keuntungan**:
- ✅ Tidak perlu jalankan queue worker
- ✅ Jobs langsung dijalankan
- ✅ Cocok untuk development

**Kekurangan**:
- ❌ Tidak ada retry
- ❌ Tidak ada background processing
- ❌ Response lebih lambat

---

### Cara 3: Jalankan Queue Worker dengan Timeout

```bash
# Jalankan dengan timeout 60 detik
php artisan queue:work --timeout=60 --tries=3 --max-time=3600
```

**Flags**:
- `--timeout=60`: Max 60 detik per job
- `--tries=3`: Retry 3x jika gagal
- `--max-time=3600`: Worker stop setelah 1 jam
- `--verbose`: Tampilkan detail

---

## 🚀 Recommended Setup untuk Development

### Option A: Sync Driver (Termudah)

**1. Update `.env`**:
```env
QUEUE_CONNECTION=sync
```

**2. Clear config**:
```bash
php artisan config:clear
```

**3. Done!** Jobs akan langsung dijalankan tanpa queue worker.

---

### Option B: Database Queue dengan Manual Run

**1. Biarkan `.env`**:
```env
QUEUE_CONNECTION=database
```

**2. Jalankan queue worker saat testing**:
```bash
# Saat mau test kirim email
php artisan queue:work --once --verbose
```

**3. Lihat jobs di database**:
```bash
php artisan tinker
# Lalu:
DB::table('jobs')->count();  // Lihat jumlah jobs
DB::table('jobs')->get();    // Lihat semua jobs
```

---

## 📊 Monitoring Queue

### Check Jobs
```bash
# Lihat jumlah jobs
php artisan queue:monitor

# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Database Query
```sql
-- Lihat jobs yang pending
SELECT * FROM jobs;

-- Lihat failed jobs
SELECT * FROM failed_jobs;

-- Count jobs
SELECT COUNT(*) FROM jobs;
```

---

## 🎓 Testing Email

### Test Welcome Email

**1. Register user baru**:
```bash
POST http://127.0.0.1:8000/api/register
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "student"
}
```

**2. Check queue** (jika pakai database driver):
```bash
# Lihat jobs
php artisan tinker
DB::table('jobs')->get();
```

**3. Process job**:
```bash
# Jika pakai sync: otomatis terkirim
# Jika pakai database: jalankan worker
php artisan queue:work --once --verbose
```

**4. Check logs**:
```bash
# Lihat log
tail -f storage/logs/laravel.log

# Atau buka file
storage/logs/laravel-2025-12-02.log
```

---

## 🔍 Troubleshooting Common Issues

### Issue 1: Queue Worker Hang

**Solusi**:
```bash
# Gunakan --once
php artisan queue:work --once

# Atau gunakan sync driver
QUEUE_CONNECTION=sync
```

### Issue 2: Jobs Tidak Terproses

**Check**:
```bash
# Lihat jobs di database
php artisan tinker
DB::table('jobs')->count();

# Jika ada jobs, jalankan worker
php artisan queue:work --once
```

### Issue 3: Email Tidak Terkirim

**Check**:
1. **Mail config** di `.env`:
   ```env
   MAIL_MAILER=log  # Untuk development
   ```

2. **Check log**:
   ```bash
   storage/logs/laravel.log
   ```

3. **Test manual**:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```

---

## 📝 Kesimpulan

**Untuk Development, gunakan salah satu**:

1. **Sync Driver** (Paling mudah):
   ```env
   QUEUE_CONNECTION=sync
   ```

2. **Database + Manual Run** (Lebih realistis):
   ```bash
   php artisan queue:work --once --verbose
   ```

3. **Database + Auto Run** (Production-like):
   ```bash
   php artisan queue:work --timeout=60
   ```

**Untuk Production**:
- Gunakan Supervisor atau systemd
- Monitor dengan Laravel Horizon (optional)
- Setup proper logging

---

## 🎯 Next Steps

1. ✅ Pilih salah satu cara di atas
2. ✅ Test dengan register user baru
3. ✅ Check apakah email masuk log
4. ✅ Verify jobs terproses

Selamat mencoba! 🚀
