# 🔐 Troubleshooting Google Login Error

## ❌ Error: "Missing required parameter: code"

Error ini terjadi karena Anda mencoba mengakses endpoint `/auth/google/callback` **secara langsung** tanpa membawa "kode rahasia" (authorization code) dari Google.

### 🔄 Alur Google Login yang Benar

1. **Frontend/User** akses `/auth/google/redirect`
2. **Backend** redirect user ke halaman Login Google
3. **User** login di Google
4. **Google** redirect balik ke `/auth/google/callback` dengan membawa parameter `?code=...`
5. **Backend** tukar `code` tersebut dengan `token` user

**Masalahnya**: Anda langsung lompat ke step 4 tanpa step 1-3, jadi `code` nya kosong.

---

## ✅ Cara Test yang Benar

Karena ini melibatkan browser (halaman login Google), Anda **tidak bisa** test full flow hanya di Postman biasa.

### Cara 1: Test Lewat Browser (Paling Mudah)

1. Buka browser (Chrome/Firefox)
2. Kunjungi URL ini:
   ```
   http://127.0.0.1:8000/api/auth/google/redirect
   ```
3. Anda akan diarahkan ke halaman login Google
4. Login dengan akun Google Anda
5. Jika berhasil, browser akan menampilkan JSON response berisi token:
   ```json
   {
       "status": "success",
       "message": "User logged in successfully",
       "authorization": {
           "token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
           "type": "bearer"
       }
   }
   ```

### Cara 2: Manual Test di Postman (Agak Ribet)

Jika Anda *harus* pakai Postman:

1. **Buka Browser**, kunjungi: `http://127.0.0.1:8000/api/auth/google/redirect`
2. Login Google sampai diarahkan balik.
3. **CEPAT-CEPAT Copy URL** di address bar browser sebelum loading selesai (atau lihat di network tab). URL nya akan terlihat seperti:
   ```
   http://127.0.0.1:8000/api/auth/google/callback?code=4/0AeaYSH...&scope=...
   ```
4. **Copy nilai `code`** (yang panjang sekali itu).
5. **Di Postman**, buat request:
   - Method: `GET`
   - URL: `http://127.0.0.1:8000/api/auth/google/callback`
   - Params: Key `code`, Value `(paste kode tadi)`
6. Send Request.

**Catatan**: Code Google hanya valid **1 kali** dan expired dalam hitungan detik. Jadi Cara 1 jauh lebih direkomendasikan.

---

## ⚙️ Checklist Konfigurasi

Pastikan `.env` Anda sudah benar:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/auth/google/callback
```

Dan pastikan di **Google Cloud Console**:
1. Authorized Redirect URI **harus sama persis** dengan `GOOGLE_REDIRECT_URI` di `.env`.
2. Jika pakai `127.0.0.1`, pastikan di console juga `127.0.0.1` (bukan `localhost`).

---

## 📝 Kesimpulan

Error `Missing required parameter: code` **bukan bug di code Anda**, tapi karena cara testing yang kurang tepat. Gunakan browser untuk test Google Login! 🚀
