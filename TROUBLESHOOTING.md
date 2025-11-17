# ⚠️ TROUBLESHOOTING - Error 404 Not Found

---

## 🔴 ERROR: 404 Not Found pada Login

### Penyebab Utama

❌ **SALAH:** `http://127.0.0.1:8000/api/auth/login`
✅ **BENAR:** `http://127.0.0.1:8000/api/login`

Login endpoint **TIDAK** ada di `/api/auth/login`, tapi di `/api/login`

---

## 📋 Endpoint yang BENAR

### Authentication (Public Routes - Tidak perlu token)

```bash
✅ POST   /api/login              (UNTUK LOGIN)
✅ POST   /api/register           (UNTUK REGISTER)
```

### Auth Management (Protected Routes - Butuh token)

```bash
✅ POST   /api/auth/logout        (DENGAN TOKEN)
✅ GET    /api/auth/profile       (DENGAN TOKEN)
✅ PUT    /api/auth/profile       (DENGAN TOKEN)
✅ POST   /api/auth/profile/photo (DENGAN TOKEN)
```

---

## 🔧 Cara Perbaiki di Postman

### Step 1: Buka Request Login

Di Postman Collection → cari "Login" request

### Step 2: Perbaiki URL

**YANG SALAH:**
```
POST http://127.0.0.1:8000/api/auth/login
```

**YANG BENAR:**
```
POST http://127.0.0.1:8000/api/login
```

### Step 3: Pastikan Body Correct

```json
{
  "email": "user@example.com",
  "password": "password"
}
```

### Step 4: Click Send

---

## ✅ Response Success (200)

```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com"
  }
}
```

---

## ❌ Jika Masih Error 404

### Check 1: Pastikan Server Running

```bash
# Terminal 1: Start server
cd d:\final project
php artisan serve
# Harus menampilkan: "Server running on [http://127.0.0.1:8000]"
```

### Check 2: Pastikan Database sudah di-migrate

```bash
# Terminal 2: Run migration
cd d:\final project
php artisan migrate
```

### Check 3: Pastikan Data User Ada

```bash
# Terminal 2: Seed database (optional)
cd d:\final project
php artisan db:seed
```

### Check 4: Test dengan CURL (Windows PowerShell)

```powershell
curl -X POST http://127.0.0.1:8000/api/login `
  -H "Content-Type: application/json" `
  -d '{"email":"user@example.com","password":"password"}'
```

### Check 5: Cek Routes yang Terdaftar

```bash
php artisan route:list | grep -E "(login|auth)"
```

---

## 🚀 Correct Flow for Testing

### 1. Start Server (Terminal 1)
```bash
cd d:\final project
php artisan serve
```

### 2. Run Migration (Terminal 2)
```bash
cd d:\final project
php artisan migrate
```

### 3. Login di Postman

**Request:**
```
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "access_token": "YOUR_TOKEN_HERE",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### 4. Copy Token untuk Request Lain

Dalam Postman:
- Buka environment atau set di Headers
- Key: `Authorization`
- Value: `Bearer YOUR_TOKEN_HERE`

---

## 📝 Correct Collection Structure

```
✅ Authentication
  ├─ POST /api/login           (✅ BENAR - Public)
  ├─ POST /api/register        (✅ BENAR - Public)
  └─ (Auth Management di tab "Settings" atau terpisah)

✅ Subscriptions
  ├─ POST   /api/subscriptions
  ├─ GET    /api/subscriptions
  ├─ GET    /api/subscriptions/{id}
  ├─ PUT    /api/subscriptions/{id}
  └─ DELETE /api/subscriptions/{id}

✅ Transactions
  ├─ POST   /api/transactions
  ├─ GET    /api/transactions
  ├─ GET    /api/transactions/{id}
  ├─ POST   /api/transactions/{id}/upload-proof
  ├─ POST   /api/transactions/{id}/confirm
  └─ POST   /api/transactions/{id}/refund

✅ Need Assessments
  ├─ POST   /api/mentoring-sessions/{id}/need-assessments
  ├─ GET    /api/mentoring-sessions/{id}/need-assessments
  ├─ PUT    /api/mentoring-sessions/{id}/need-assessments/mark-completed
  └─ DELETE /api/mentoring-sessions/{id}/need-assessments

✅ Coaching Files
  ├─ POST   /api/mentoring-sessions/{id}/coaching-files
  ├─ GET    /api/mentoring-sessions/{id}/coaching-files
  ├─ GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}
  ├─ GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}/download
  ├─ DELETE /api/mentoring-sessions/{id}/coaching-files/{fileId}
  └─ DELETE /api/mentoring-sessions/{id}/coaching-files

✅ Progress Reports
  ├─ POST   /api/progress-reports
  ├─ GET    /api/progress-reports
  ├─ GET    /api/progress-reports/{id}
  ├─ PUT    /api/progress-reports/{id}
  ├─ DELETE /api/progress-reports/{id}
  ├─ GET    /api/progress-reports/due
  ├─ GET    /api/enrollments/{id}/progress-reports
  └─ POST   /api/progress-reports/frequency
```

---

## ✅ Quick Checklist

- [ ] Server running: `php artisan serve`
- [ ] Database migrated: `php artisan migrate`
- [ ] Login URL benar: `/api/login` (BUKAN `/api/auth/login`)
- [ ] Request body punya email & password
- [ ] Response punya access_token
- [ ] Copy token ke Authorization header
- [ ] Semua protected routes punya token
- [ ] Response format konsisten

---

## 🎯 Jika Masih Tidak Berhasil

Coba debug dengan CURL manual:

### Windows PowerShell:
```powershell
# Test login
$response = curl -X POST http://127.0.0.1:8000/api/login `
  -H "Content-Type: application/json" `
  -d '{"email":"user@example.com","password":"password"}'

$response | ConvertFrom-Json
```

### Atau dengan REST Client Extension di VS Code

Buat file `test.http`:
```http
### Login
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

### Get Subscriptions
@token = YOUR_TOKEN_FROM_LOGIN
GET http://127.0.0.1:8000/api/subscriptions
Authorization: Bearer {{token}}
```

---

## 📞 Common Errors & Solutions

| Error | Penyebab | Solusi |
|-------|---------|--------|
| 404 Not Found | URL salah | Gunakan `/api/login` bukan `/api/auth/login` |
| 401 Unauthorized | Token missing/invalid | Pastikan header `Authorization: Bearer TOKEN` |
| 422 Validation Error | Data tidak valid | Check required fields (email, password) |
| 500 Internal Error | Server error | Check `storage/logs/laravel.log` |
| Connection refused | Server tidak running | `php artisan serve` |

---

**Last Updated:** 17 November 2025  
**Status:** Ready to Test ✅
