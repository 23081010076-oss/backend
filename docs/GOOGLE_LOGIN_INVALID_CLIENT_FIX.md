# 🔐 Solusi Error Google Login: "Akses diblokir: Error Otorisasi"

## ❌ Error: "401: invalid_client"

Dari screenshot yang Anda kirim, error-nya adalah:
> **The OAuth client was not found.**
> Error 401: invalid_client

Artinya: **GOOGLE_CLIENT_ID** yang ada di file `.env` Anda **SALAH** atau **TIDAK DITEMUKAN** di server Google.

---

## ✅ Cara Memperbaiki

### 1. Cek Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
2. Pilih project Anda
3. Masuk ke menu **APIs & Services** > **Credentials**
4. Di bagian **OAuth 2.0 Client IDs**, klik nama client Anda (atau buat baru jika belum ada)
5. **Copy** nilai **Client ID** yang benar. Formatnya biasanya berakhiran `.apps.googleusercontent.com`

### 2. Update File `.env`

Buka file `.env` di project Laravel Anda dan pastikan isinya sama persis dengan yang ada di Google Console:

```env
GOOGLE_CLIENT_ID=paste-client-id-yang-benar-disini
GOOGLE_CLIENT_SECRET=paste-client-secret-disini
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/auth/google/callback
```

**⚠️ PENTING**:
- Jangan ada spasi di awal/akhir
- Jangan pakai tanda kutip jika tidak perlu
- Pastikan tidak ada typo

### 3. Clear Cache

Setelah update `.env`, **WAJIB** jalankan command ini agar perubahan terbaca:

```bash
php artisan config:clear
```

### 4. Coba Lagi

Buka browser dan akses lagi:
`http://127.0.0.1:8000/api/auth/google/redirect`

---

## 🔍 Checklist Tambahan

Jika masih error, pastikan di Google Console bagian **Authorized redirect URIs** sudah ditambahkan:

```
http://127.0.0.1:8000/api/auth/google/callback
```

(Harus sama persis dengan yang di `.env`, termasuk `http` vs `https` dan port-nya)
