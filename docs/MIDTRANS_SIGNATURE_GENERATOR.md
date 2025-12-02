# 🔐 Cara Generate Midtrans Signature untuk Testing

## ✅ Solusi: Gunakan Artisan Command

Saya sudah buat **Artisan command** yang mudah dipakai!

### Cara Pakai

```bash
# Generate signature dengan default values
php artisan midtrans:signature

# Atau dengan custom values
php artisan midtrans:signature TRX123 200 500000
```

### Output

Command akan generate:
- ✅ Signature yang valid
- ✅ Complete payload untuk Postman
- ✅ Instruksi cara pakai

### Contoh Output

```
===========================================
Midtrans Webhook Signature Generator
===========================================

Input Data:
  Order ID      : TRX20251202ABC123
  Status Code   : 200
  Gross Amount  : 500000
  Server Key    : SB-Mid-ser...

Generated Signature:
  abc123def456...

===========================================
Complete Webhook Payload (untuk Postman):
===========================================

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

===========================================
Cara pakai di Postman:
===========================================
1. Method: POST
2. URL: http://127.0.0.1:8000/api/midtrans/webhook
3. Headers:
   - Content-Type: application/json
   - Accept: application/json
4. Body (raw JSON): Copy payload di atas
```

---

## 📝 Step by Step

### 1. Pastikan Server Key Ada

Check file `.env`:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxx
```

### 2. Generate Signature

```bash
php artisan midtrans:signature
```

### 3. Copy Payload

Copy JSON payload dari output command

### 4. Test di Postman

**Method**: `POST`  
**URL**: `http://127.0.0.1:8000/api/midtrans/webhook`

**Headers**:
```
Content-Type: application/json
Accept: application/json
```

**Body** (raw JSON):
Paste payload dari step 2

### 5. Expected Response

**Success** (200):
```json
{
    "sukses": true,
    "pesan": "Notification processed successfully",
    "data": null
}
```

**Error** (403):
```json
{
    "sukses": false,
    "pesan": "Invalid signature",
    "data": null
}
```

---

## 🎯 Custom Values

Generate dengan order ID dan amount tertentu:

```bash
# Syntax
php artisan midtrans:signature {order_id} {status_code} {gross_amount}

# Contoh
php artisan midtrans:signature TRX20251202001 200 1000000
```

---

## 🔍 Troubleshooting

### Error: "MIDTRANS_SERVER_KEY not found"

**Solusi**: Tambahkan di `.env`
```env
MIDTRANS_SERVER_KEY=your-server-key-here
```

Lalu:
```bash
php artisan config:clear
php artisan midtrans:signature
```

### Error: "Invalid signature" di Postman

**Solusi**: 
1. Generate signature ulang
2. Pastikan `order_id`, `status_code`, `gross_amount` sama persis
3. Copy paste payload dengan hati-hati (jangan ada typo)

### Transaction not found

**Solusi**: Buat transaction dulu
```bash
php artisan tinker

# Create dummy transaction
\App\Models\Transaction::create([
    'transaction_code' => 'TRX20251202ABC123',
    'user_id' => 1,
    'transactionable_type' => 'App\Models\Course',
    'transactionable_id' => 1,
    'amount' => 500000,
    'status' => 'pending',
    'payment_method' => 'qris'
]);
```

---

## ✨ Tips

1. **Gunakan order_id yang ada** di database
2. **Gross amount harus match** dengan transaction amount
3. **Generate ulang** setiap kali ganti order_id/amount
4. **Check logs** jika masih error: `storage/logs/laravel.log`

---

**Selamat testing!** 🚀
