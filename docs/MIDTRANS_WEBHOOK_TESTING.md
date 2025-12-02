# 🔐 Midtrans Webhook Testing Guide

## ❌ Error: "Invalid signature"

Error ini muncul karena **signature verification gagal**. Midtrans webhook memerlukan signature yang valid untuk security.

---

## 📝 Cara Kerja Signature

Midtrans generate signature dengan formula:
```
signature = SHA512(order_id + status_code + gross_amount + server_key)
```

Contoh:
```php
$signature = hash('sha512', 
    'TRX123' . '200' . '500000' . 'your-server-key'
);
```

---

## ✅ Solusi 1: Generate Signature yang Valid

### Step 1: Generate Signature

Jalankan script helper:
```bash
php generate_midtrans_signature.php
```

Output akan memberikan:
- Signature yang valid
- Complete payload untuk Postman

### Step 2: Test di Postman

**Method**: `POST`  
**URL**: `http://127.0.0.1:8000/api/midtrans/webhook`

**Headers**:
```
Content-Type: application/json
Accept: application/json
```

**Body** (raw JSON):
```json
{
    "order_id": "TRX20251202ABC123",
    "transaction_status": "settlement",
    "status_code": "200",
    "gross_amount": "500000",
    "signature_key": "generated_signature_from_script",
    "payment_type": "qris",
    "transaction_time": "2025-12-02 18:00:00",
    "fraud_status": "accept"
}
```

---

## ✅ Solusi 2: Disable Verification (Development Only)

**⚠️ WARNING**: Hanya untuk development! Jangan di production!

### Option A: Comment Verification

Edit `app/Http/Controllers/Api/MidtransWebhookController.php`:

```php
public function handleNotification(Request $request): JsonResponse
{
    try {
        $notification = $request->all();
        
        Log::info('Midtrans Notification Received', $notification);
        
        // TEMPORARY: Skip signature verification for testing
        // TODO: Re-enable before production!
        /*
        if (!$this->midtransService->verifySignature($notification)) {
            Log::warning('Invalid Midtrans signature', $notification);
            return $this->forbiddenResponse('Invalid signature');
        }
        */
        
        $transactionCode = $notification['order_id'];
        // ... rest of code
    }
}
```

### Option B: Environment Variable

Tambahkan di `.env`:
```env
MIDTRANS_SKIP_SIGNATURE_CHECK=true
```

Lalu update controller:
```php
// Verify signature (skip if in development)
if (!config('app.debug') || !env('MIDTRANS_SKIP_SIGNATURE_CHECK')) {
    if (!$this->midtransService->verifySignature($notification)) {
        return $this->forbiddenResponse('Invalid signature');
    }
}
```

---

## 🧪 Testing Webhook

### Test Case 1: Payment Success
```json
{
    "order_id": "TRX20251202001",
    "transaction_status": "settlement",
    "status_code": "200",
    "gross_amount": "500000",
    "signature_key": "YOUR_GENERATED_SIGNATURE",
    "payment_type": "qris"
}
```

**Expected**: Transaction status updated to "paid"

### Test Case 2: Payment Pending
```json
{
    "order_id": "TRX20251202002",
    "transaction_status": "pending",
    "status_code": "201",
    "gross_amount": "500000",
    "signature_key": "YOUR_GENERATED_SIGNATURE",
    "payment_type": "bank_transfer"
}
```

**Expected**: Transaction status remains "pending"

### Test Case 3: Payment Failed
```json
{
    "order_id": "TRX20251202003",
    "transaction_status": "deny",
    "status_code": "400",
    "gross_amount": "500000",
    "signature_key": "YOUR_GENERATED_SIGNATURE",
    "payment_type": "credit_card"
}
```

**Expected**: Transaction status updated to "failed"

---

## 🔍 Debugging

### Check Logs
```bash
# Lihat log Midtrans
tail -f storage/logs/laravel.log | grep Midtrans
```

### Check Transaction
```bash
php artisan tinker

# Check transaction
$tx = \App\Models\Transaction::where('transaction_code', 'TRX20251202001')->first();
echo $tx->status;
```

---

## 📊 Webhook Flow

```
1. Midtrans → Send webhook
   ↓
2. Verify signature
   ↓ (if valid)
3. Find transaction by order_id
   ↓
4. Map Midtrans status → Our status
   ↓
5. Update transaction
   ↓
6. Execute post-payment actions
   ↓
7. Return success response
```

---

## 🚀 Production Setup

### 1. Set Webhook URL di Midtrans Dashboard

Login ke Midtrans Dashboard → Settings → Configuration:
```
Webhook URL: https://yourdomain.com/api/midtrans/webhook
```

### 2. Pastikan Signature Verification Enabled

```php
// Di MidtransWebhookController
if (!$this->midtransService->verifySignature($notification)) {
    return $this->forbiddenResponse('Invalid signature');
}
```

### 3. Monitor Webhooks

Setup monitoring untuk:
- Failed signature verifications
- Failed transaction updates
- Webhook response time

---

## 📝 Troubleshooting

### Issue: "Invalid signature" terus

**Solusi**:
1. Pastikan `MIDTRANS_SERVER_KEY` di `.env` benar
2. Generate signature ulang dengan script
3. Pastikan `order_id`, `status_code`, `gross_amount` sama persis

### Issue: Transaction not found

**Solusi**:
1. Pastikan `order_id` di webhook sama dengan `transaction_code` di database
2. Check: `SELECT * FROM transactions WHERE transaction_code = 'TRX123';`

### Issue: Status tidak update

**Solusi**:
1. Check logs: `storage/logs/laravel.log`
2. Verify mapping status di `MidtransService::mapTransactionStatus()`

---

## 🎯 Quick Test

```bash
# 1. Generate signature
php generate_midtrans_signature.php

# 2. Copy payload

# 3. Test di Postman
POST http://127.0.0.1:8000/api/midtrans/webhook

# 4. Check logs
tail -f storage/logs/laravel.log

# 5. Verify transaction updated
php artisan tinker
\App\Models\Transaction::latest()->first();
```

---

**Selamat testing!** 🚀
