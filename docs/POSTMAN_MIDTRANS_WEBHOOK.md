# 📝 Cara Test Midtrans Webhook di Postman

## ✅ Quick Guide

### Step 1: Generate Signature

Jalankan command ini di terminal:
```bash
php artisan midtrans:signature
```

### Step 2: Copy Payload

Command akan output payload lengkap seperti ini:
```json
{
    "order_id": "TRX20251202ABC123",
    "transaction_status": "settlement",
    "status_code": "200",
    "gross_amount": "500000",
    "signature_key": "abc123def456...",
    "payment_type": "qris",
    "transaction_time": "2025-12-02 18:00:00",
    "fraud_status": "accept"
}
```

### Step 3: Test di Postman

1. Buka Postman collection: **Student App Flow**
2. Cari endpoint: **00. Public / Auth → Public Lists → Simulate Midtrans Webhook**
3. **Replace** body dengan payload dari Step 2
4. Klik **Send**

### Step 4: Expected Response

**Success**:
```json
{
    "sukses": true,
    "pesan": "Notification processed successfully",
    "data": null
}
```

---

## 🎯 Update Postman Collection

Untuk update endpoint Midtrans webhook di Postman:

1. **Nama endpoint**: `Simulate Midtrans Webhook`
2. **Method**: `POST`
3. **URL**: `{{baseUrl}}/midtrans/webhook`
4. **Body**: Paste payload dari `php artisan midtrans:signature`

---

## 📋 Note untuk Postman

Tambahkan di description endpoint:

```
CARA GENERATE SIGNATURE:
1. Jalankan: php artisan midtrans:signature
2. Copy complete payload dari output
3. Paste ke Body request ini
4. Send request

Signature harus valid, tidak bisa hardcode!
```

---

**File `generate_midtrans_signature.php` sudah dihapus.**  
**Gunakan Artisan command saja!** ✅
