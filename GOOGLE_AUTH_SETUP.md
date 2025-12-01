# Google Authentication - Setup Guide

## Langkah 1: Mendapatkan Google OAuth Credentials

1. **Buka Google Cloud Console**
   - Kunjungi: https://console.cloud.google.com/

2. **Buat Project Baru** (atau pilih yang sudah ada)
   - Klik "Select a project" di bagian atas
   - Klik "New Project"
   - Beri nama project Anda (contoh: "Learning Platform")
   - Klik "Create"

3. **Aktifkan Google+ API**
   - Di sidebar, pilih "APIs & Services" > "Library"
   - Cari "Google+ API"
   - Klik dan pilih "Enable"

4. **Buat OAuth 2.0 Credentials**
   - Di sidebar, pilih "APIs & Services" > "Credentials"
   - Klik "Create Credentials" > "OAuth client ID"
   - Jika diminta, konfigurasi OAuth consent screen terlebih dahulu:
     - User Type: External
     - App name: Nama aplikasi Anda
     - User support email: Email Anda
     - Developer contact: Email Anda
     - Klik "Save and Continue"
   
5. **Konfigurasi OAuth Client ID**
   - Application type: **Web application**
   - Name: "Learning Platform Web Client"
   - Authorized JavaScript origins:
     ```
     http://localhost:8000
     http://localhost:3000
     ```
   - Authorized redirect URIs:
     ```
     http://localhost:8000/api/auth/google/callback
     ```
   - Klik "Create"

6. **Copy Credentials**
   - Setelah dibuat, Anda akan melihat:
     - **Client ID**: `xxxxx.apps.googleusercontent.com`
     - **Client Secret**: `xxxxxx`
   - Copy kedua nilai ini

---

## Langkah 2: Konfigurasi Backend (.env)

Buka file `.env` Anda dan tambahkan konfigurasi berikut:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Frontend URL (opsional, untuk redirect setelah login)
FRONTEND_URL=http://localhost:3000

# Google Auth Response Type
# json = return JSON response dengan token (default)
# redirect = redirect ke frontend dengan token di URL
GOOGLE_AUTH_RESPONSE_TYPE=json

# Google Allowed Email Domains (opsional)
# Kosongkan untuk mengizinkan semua domain
# Contoh: gmail.com,student.university.ac.id
GOOGLE_ALLOWED_DOMAINS=
```

**Ganti:**
- `your-client-id.apps.googleusercontent.com` dengan Client ID Anda
- `your-client-secret` dengan Client Secret Anda

---

## Langkah 3: Testing

### A. Testing dengan Browser

1. **Start Laravel Server**
   ```bash
   php artisan serve
   ```

2. **Akses Google Login**
   - Buka browser dan kunjungi:
     ```
     http://localhost:8000/api/auth/google/redirect
     ```

3. **Login dengan Google**
   - Anda akan di-redirect ke halaman login Google
   - Pilih akun Google Anda
   - Berikan izin yang diminta

4. **Verifikasi Response**
   - Setelah berhasil, Anda akan melihat JSON response:
     ```json
     {
       "status": "success",
       "message": "User logged in successfully",
       "user": {
         "id": 1,
         "name": "Your Name",
         "email": "your-email@gmail.com",
         "google_id": "123456789",
         "avatar": "https://...",
         "role": "student"
       },
       "authorization": {
         "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
         "type": "bearer"
       }
     }
     ```

5. **Copy Token**
   - Copy nilai `authorization.token`

6. **Test Token**
   - Gunakan token untuk mengakses endpoint protected:
     ```bash
     curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/auth/me
     ```

### B. Testing dengan Postman

1. **Import Collection**
   - Import Postman collection yang sudah ada

2. **Test Google Login Redirect**
   - Request: `GET /api/auth/google/redirect`
   - Response akan berisi redirect URL
   - Copy URL dan buka di browser

3. **Login dan Get Token**
   - Setelah login di browser, copy token dari response

4. **Set Token di Postman**
   - Di Postman environment, set variable:
     - Key: `token`
     - Value: `<paste token here>`

5. **Test Protected Endpoints**
   - Coba endpoint seperti `/api/auth/me`
   - Token akan otomatis digunakan

### C. Testing dengan PHPUnit

```bash
# Run semua tests
php artisan test

# Run hanya Google Auth tests
php artisan test --filter=GoogleAuthTest

# Run specific test
php artisan test --filter=test_google_callback_creates_new_user
```

---

## Fitur-Fitur Google Auth

### 1. **Auto User Creation**
- User baru otomatis dibuat saat pertama kali login dengan Google
- Default role: `student`
- Password: random secure string

### 2. **Account Linking**
- Jika user sudah terdaftar dengan email yang sama, Google ID akan di-link ke akun tersebut
- User bisa login dengan email/password atau Google

### 3. **Avatar Sync**
- Avatar dari Google otomatis disimpan dan di-update

### 4. **Email Domain Validation** (Opsional)
- Batasi login hanya untuk domain email tertentu
- Contoh: hanya `@university.edu` atau `@company.com`
- Set di `.env`:
  ```env
  GOOGLE_ALLOWED_DOMAINS=university.edu,company.com
  ```

### 5. **Configurable Response Type**

**Mode 1: JSON Response (Default)**
```env
GOOGLE_AUTH_RESPONSE_TYPE=json
```
- Return JSON dengan token
- Cocok untuk API-first approach
- Frontend harus handle redirect sendiri

**Mode 2: Frontend Redirect**
```env
GOOGLE_AUTH_RESPONSE_TYPE=redirect
FRONTEND_URL=http://localhost:3000
```
- Otomatis redirect ke frontend dengan token di URL
- Frontend dapat extract token dari URL parameter
- Contoh redirect: `http://localhost:3000/auth/callback?token=xxx`

---

## Error Handling

### Error 1: "Google OAuth configuration error"
**Penyebab:** GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, atau GOOGLE_REDIRECT_URI belum di-set

**Solusi:**
1. Pastikan semua konfigurasi sudah ada di `.env`
2. Restart Laravel server: `php artisan serve`
3. Clear config cache: `php artisan config:clear`

### Error 2: "Email domain not allowed"
**Penyebab:** Email domain tidak ada dalam whitelist

**Solusi:**
1. Tambahkan domain ke `GOOGLE_ALLOWED_DOMAINS` di `.env`
2. Atau kosongkan `GOOGLE_ALLOWED_DOMAINS` untuk allow semua domain

### Error 3: "redirect_uri_mismatch"
**Penyebab:** Redirect URI tidak match dengan yang di-set di Google Console

**Solusi:**
1. Buka Google Cloud Console > Credentials
2. Edit OAuth 2.0 Client ID
3. Pastikan Authorized redirect URIs berisi:
   ```
   http://localhost:8000/api/auth/google/callback
   ```
4. Pastikan `GOOGLE_REDIRECT_URI` di `.env` sama persis

---

## Logging

Semua aktivitas Google Auth di-log untuk debugging:

**Success Login:**
```
[INFO] Google login successful for user: user@gmail.com
```

**New User Created:**
```
[INFO] Created new user from Google: newuser@gmail.com
```

**Account Linked:**
```
[INFO] Linked Google account to existing user: existing@gmail.com
```

**Unauthorized Domain:**
```
[WARNING] Google login attempt with unauthorized domain: user@unauthorized.com
```

**Errors:**
```
[ERROR] Google OAuth Callback Error: <error message>
```

Cek log di: `storage/logs/laravel.log`

---

## Frontend Integration

### React Example (JSON Response Mode)

```javascript
// Login button
const handleGoogleLogin = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect';
};

// Callback page (handle response)
// Note: Dengan JSON mode, Anda perlu handle ini secara manual
// Atau gunakan redirect mode (lihat di bawah)
```

### React Example (Redirect Mode)

```javascript
// 1. Set di .env backend:
// GOOGLE_AUTH_RESPONSE_TYPE=redirect
// FRONTEND_URL=http://localhost:3000

// 2. Login button
const handleGoogleLogin = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect';
};

// 3. Callback page component (src/pages/AuthCallback.jsx)
import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';

function AuthCallback() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();

  useEffect(() => {
    const token = searchParams.get('token');
    
    if (token) {
      // Save token
      localStorage.setItem('token', token);
      
      // Redirect to dashboard
      navigate('/dashboard');
    } else {
      // Error handling
      navigate('/login?error=google_auth_failed');
    }
  }, [searchParams, navigate]);

  return <div>Loading...</div>;
}

export default AuthCallback;

// 4. Add route
// <Route path="/auth/callback" element={<AuthCallback />} />
```

---

## Security Best Practices

1. **Jangan commit `.env`** - File ini sudah di-gitignore
2. **Gunakan HTTPS di production** - Update redirect URI ke `https://`
3. **Validasi email domain** - Jika aplikasi internal, batasi domain
4. **Rotate secrets** - Ganti Client Secret secara berkala
5. **Monitor logs** - Cek aktivitas mencurigakan di logs

---

## Production Deployment

Saat deploy ke production:

1. **Update Redirect URI di Google Console**
   ```
   https://yourdomain.com/api/auth/google/callback
   ```

2. **Update .env di Production**
   ```env
   APP_URL=https://yourdomain.com
   GOOGLE_REDIRECT_URI=https://yourdomain.com/api/auth/google/callback
   FRONTEND_URL=https://yourdomain.com
   ```

3. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

---

## Troubleshooting

### Debug Mode

Untuk debugging lebih detail, set di `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Test Konfigurasi

```bash
# Check config
php artisan tinker
>>> config('services.google')

# Should output:
# [
#   "client_id" => "your-client-id",
#   "client_secret" => "your-client-secret",
#   "redirect" => "http://localhost:8000/api/auth/google/callback",
#   ...
# ]
```

### Clear All Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Support

Jika masih ada masalah:
1. Cek `storage/logs/laravel.log` untuk error details
2. Pastikan semua dependencies ter-install: `composer install`
3. Pastikan database migration sudah running: `php artisan migrate`
4. Test dengan `php artisan test --filter=GoogleAuthTest`
