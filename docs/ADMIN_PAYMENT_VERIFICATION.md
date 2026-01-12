# 👨‍💼 Admin Payment Verification Guide

## 📋 Flow Pembayaran

### 1. **QRIS Payment** (Butuh Verifikasi Manual)

```
1. User membeli dengan QRIS
2. QR Code ter-generate otomatis
3. User scan & bayar via QRIS
4. ⚠️ Admin perlu MANUAL konfirmasi → status jadi 'paid'
```

### 2. **Manual / Bank Transfer**

```
1. User transfer ke rekening
2. User upload bukti pembayaran
3. Admin lihat bukti & konfirmasi → status jadi 'paid'
```

---

## 🔧 API Endpoints untuk Admin

### 1. Lihat Transaksi Pending Verification

```http
GET /api/admin/transactions/pending-verification
Authorization: Bearer {admin_token}
```

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "transaction_code": "TRX20251224492817",
      "type": "course_enrollment",
      "amount": "100000.00",
      "payment_method": "qris",
      "status": "pending",
      "payment_proof": null,
      "qr_code_url": "qr-codes/TRX20251224492817.svg",
      "created_at": "2025-12-24T05:54:12.000000Z"
    }
  ]
}
```

---

### 2. Konfirmasi Pembayaran (Mark as Paid)

```http
POST /api/admin/transactions/{id}/confirm
Authorization: Bearer {admin_token}
```

**Untuk QRIS:**

- Admin cek di dashboard payment gateway (Midtrans/duitku)
- Jika sudah bayar, hit endpoint ini
- Status otomatis jadi `'paid'`

**Untuk Manual/Bank:**

- Admin lihat bukti transfer yang diupload user
- Jika valid, hit endpoint ini
- Status otomatis jadi `'paid'`

**Response:**

```json
{
  "sukses": true,
  "pesan": "Pembayaran berhasil dikonfirmasi",
  "data": {
    "id": 1,
    "status": "paid",
    "paid_at": "2025-12-24T06:30:00.000000Z"
  }
}
```

---

### 3. Lihat Semua Transaksi

```http
GET /api/admin/transactions
Authorization: Bearer {admin_token}
```

Filter available:

- `?status=pending` - Hanya pending
- `?status=paid` - Hanya yang sudah bayar
- `?type=course_enrollment` - Hanya transaksi course
- `?payment_method=qris` - Hanya QRIS

---

## 🎯 Checklist Verifikasi Admin

### Untuk QRIS Payment:

- [ ] Cek transaction di list pending verification
- [ ] Buka dashboard Midtrans/payment gateway
- [ ] Cocokkan transaction_code dengan payment di gateway
- [ ] Jika sudah bayar di gateway → `POST /transactions/{id}/confirm`
- [ ] Status berubah jadi 'paid' ✅

### Untuk Manual/Bank Transfer:

- [ ] Cek transaction dengan payment_proof (ada file upload)
- [ ] Download/lihat bukti transfer
- [ ] Cek rekening bank → apakah uang sudah masuk?
- [ ] Cocokkan nominal dan transaction_code
- [ ] Jika valid → `POST /transactions/{id}/confirm`
- [ ] Status berubah jadi 'paid' ✅

---

## 🚨 Important Notes

1. **QRIS tanpa webhook = manual verification**

   - Untuk auto verification, perlu setup Midtrans webhook
   - Saat ini: admin cek manual di dashboard payment gateway

2. **Payment Proof hanya untuk Manual/Bank**

   - QRIS tidak bisa upload proof (otomatis reject)
   - Validasi sudah ada di backend

3. **Status Flow:**

   - `pending` → Menunggu pembayaran
   - `paid` → Sudah bayar (confirmed by admin)
   - `expired` → Lewat 24 jam belum bayar
   - `failed` → Pembayaran gagal

4. **Security:**
   - Hanya admin yang bisa konfirmasi payment
   - Policy: `$user->role === 'admin'`
   - Protected by JWT authentication

---

## 📱 Testing Flow

### Test QRIS Manual Verification:

```bash
# 1. User membeli dengan QRIS (sebagai student)
POST /api/courses/{id}/enroll
{
  "payment_method": "qris"
}
# Response: dapat qr_code_url & qr_string

# 2. Admin lihat pending
GET /api/admin/transactions/pending-verification
# Response: list transaksi pending

# 3. Admin konfirmasi (setelah cek payment gateway)
POST /api/admin/transactions/{id}/confirm
# Response: status jadi 'paid'
```

### Test Manual Transfer:

```bash
# 1. User membeli dengan manual (sebagai student)
POST /api/courses/{id}/enroll
{
  "payment_method": "manual"
}

# 2. User upload bukti
POST /api/transactions/{id}/upload-payment-proof
{
  "payment_proof": {file}
}

# 3. Admin lihat pending
GET /api/admin/transactions/pending-verification
# Response: transaksi dengan payment_proof

# 4. Admin konfirmasi
POST /api/admin/transactions/{id}/confirm
# Response: status jadi 'paid'
```

---

## 🔮 Future Enhancement (Optional)

Jika mau auto verification untuk QRIS:

1. Setup Midtrans webhook URL
2. Buat endpoint `POST /api/webhooks/midtrans`
3. Midtrans hit webhook saat user bayar
4. Status auto update jadi 'paid'
5. Admin tidak perlu manual lagi

**For now: Manual verification by admin is working perfectly! ✅**
