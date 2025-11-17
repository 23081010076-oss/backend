# 🧪 API TESTING - Quick Start Guide

File-file testing untuk menguji semua 16 endpoint baru.

---

## 📁 File Testing yang Tersedia

### 1. **API_TESTING.md** (Recommended)
- Dokumentasi lengkap dengan contoh request/response
- Best untuk reference dan manual testing
- Format: Markdown dengan HTTP examples

### 2. **Mentoring_API_Collection.postman_collection.json**
- Collection untuk Postman app
- Best untuk visual testing
- Sudah terstruktur per fitur

### 3. **test-api.sh**
- Bash script untuk Linux/Mac
- 30 curl commands siap pakai
- Best untuk automated testing

### 4. **test-api.bat**
- Batch script untuk Windows PowerShell
- 30 curl commands untuk Windows
- Best untuk automated testing di Windows

---

## 🚀 Quick Start

### Method 1: Postman (Paling Mudah)

**Step 1:** Install Postman
```bash
Download dari: https://www.postman.com/downloads/
```

**Step 2:** Import Collection
```
Postman → File → Import → Pilih "Mentoring_API_Collection.postman_collection.json"
```

**Step 3:** Get JWT Token
```
1. Klik folder "🔐 Authentication"
2. Klik "Login"
3. Send
4. Copy access_token dari response
5. Paste ke environment variable atau setiap header
```

**Step 4:** Test Endpoints
```
1. Klik endpoint yang ingin ditest
2. Ganti parameter sesuai kebutuhan
3. Click "Send"
```

---

### Method 2: Manual dengan curl (Terminal/CMD)

**Step 1:** Start server
```bash
cd d:\final project
php artisan serve
# Running at: http://localhost:8000
```

**Step 2:** Get JWT Token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"password\"}"
```

**Copy access_token** dari response

**Step 3:** Test Endpoint
```bash
curl -X GET http://localhost:8000/api/subscriptions \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json"
```

---

### Method 3: Script Automated

**Windows:**
```bash
# Edit test-api.bat
# Ubah: SET TOKEN=YOUR_JWT_TOKEN dengan token anda

# Jalankan:
test-api.bat
```

**Linux/Mac:**
```bash
# Edit test-api.sh
# Ubah: TOKEN="YOUR_JWT_TOKEN" dengan token anda

# Jalankan:
bash test-api.sh
```

---

## 📝 Testing Checklist

### Pre-Testing
- [ ] Server running: `php artisan serve`
- [ ] JWT token obtained from login
- [ ] Postman installed (if using Postman method)

### Testing 5 Fitur

#### 1. Subscriptions ✅
- [ ] POST - Create (single_course)
- [ ] POST - Create (all_in_one)
- [ ] GET - List all
- [ ] GET - Detail
- [ ] PUT - Update
- [ ] DELETE - Delete

#### 2. Transactions ✅
- [ ] POST - Create
- [ ] GET - List all
- [ ] GET - Detail
- [ ] POST - Upload proof
- [ ] POST - Confirm payment
- [ ] POST - Process refund

#### 3. Need Assessments ✅
- [ ] POST - Submit form
- [ ] GET - Get form
- [ ] PUT - Mark completed
- [ ] DELETE - Delete form

#### 4. Coaching Files ✅
- [ ] POST - Upload file
- [ ] GET - List files
- [ ] GET - File detail
- [ ] GET - Download file
- [ ] DELETE - Delete single
- [ ] DELETE - Delete all

#### 5. Progress Reports ✅
- [ ] POST - Create report
- [ ] GET - List all
- [ ] GET - Detail
- [ ] PUT - Update report
- [ ] GET - Per enrollment
- [ ] POST - Set frequency
- [ ] GET - Due reports
- [ ] DELETE - Delete report

### Error Testing
- [ ] 401 Unauthorized (no token)
- [ ] 404 Not Found (invalid ID)
- [ ] 422 Validation Error (invalid data)
- [ ] 413 Payload Too Large (file > 50MB)

---

## 📊 Expected Results

### Success Response (201/200)
```json
{
  "id": 1,
  "data": {...},
  "message": "Success",
  "status": "success"
}
```

### Error Response (4xx/5xx)
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

---

## 🔑 Important Notes

### Authorization Header
Semua endpoint (kecuali login/register) memerlukan:
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Content-Type
Gunakan untuk semua JSON request:
```
Content-Type: application/json
```

### File Upload
Gunakan `multipart/form-data` untuk file:
```
POST /api/mentoring-sessions/1/coaching-files
Content-Type: multipart/form-data

- file_name: "Slide-Week-1"
- file: [binary file]
- file_type: "pdf"
- uploaded_by: 1
```

### Frequency Validation
Progress report frequency harus:
```
Minimum: 7 hari
Maximum: 30 hari
Default: 14 hari (2 minggu)
```

---

## 🐛 Troubleshooting

### Error: "Unauthorized (401)"
- ✅ Pastikan JWT token valid
- ✅ Token belum expired
- ✅ Header format benar: `Authorization: Bearer TOKEN`

### Error: "Not Found (404)"
- ✅ Pastikan ID resource ada
- ✅ URL path benar
- ✅ Server running

### Error: "Validation Error (422)"
- ✅ Cek required fields
- ✅ Cek data type (string/number/array)
- ✅ Lihat pesan error untuk detail

### Error: "File Too Large (413)"
- ✅ Max file size: 50MB
- ✅ Compress file sebelum upload

### Curl not found
- **Windows:** Install dari https://curl.se/download.html
- **Linux:** `sudo apt-get install curl`
- **Mac:** `brew install curl`

---

## 📚 Reference

### API Base URL
```
http://localhost:8000/api
```

### Authentication
```bash
POST /auth/login
POST /auth/logout
POST /auth/refresh
GET /auth/profile
```

### Subscriptions (6 endpoints)
```
POST   /subscriptions              # Create
GET    /subscriptions              # List
GET    /subscriptions/{id}         # Detail
PUT    /subscriptions/{id}         # Update
DELETE /subscriptions/{id}         # Delete
```

### Transactions (6 endpoints)
```
POST   /transactions                     # Create
GET    /transactions                     # List
GET    /transactions/{id}                # Detail
POST   /transactions/{id}/upload-proof   # Upload proof
POST   /transactions/{id}/confirm        # Confirm
POST   /transactions/{id}/refund         # Refund
```

### Need Assessments (4 endpoints)
```
POST   /mentoring-sessions/{id}/need-assessments
GET    /mentoring-sessions/{id}/need-assessments
PUT    /mentoring-sessions/{id}/need-assessments/mark-completed
DELETE /mentoring-sessions/{id}/need-assessments
```

### Coaching Files (6 endpoints)
```
POST   /mentoring-sessions/{id}/coaching-files
GET    /mentoring-sessions/{id}/coaching-files
GET    /mentoring-sessions/{id}/coaching-files/{fileId}
GET    /mentoring-sessions/{id}/coaching-files/{fileId}/download
DELETE /mentoring-sessions/{id}/coaching-files/{fileId}
DELETE /mentoring-sessions/{id}/coaching-files
```

### Progress Reports (8 endpoints)
```
POST   /progress-reports                      # Create
GET    /progress-reports                      # List
GET    /progress-reports/{id}                 # Detail
PUT    /progress-reports/{id}                 # Update
DELETE /progress-reports/{id}                 # Delete
GET    /progress-reports/due                  # Due reports
GET    /enrollments/{id}/progress-reports     # Per enrollment
POST   /progress-reports/frequency            # Set frequency
```

---

## ✅ Success Indicators

Semua test passed jika:
- ✅ Subscriptions CRUD fully working
- ✅ Transactions & payment flow working
- ✅ Assessment form dapat disimpan
- ✅ File upload/download working
- ✅ Progress tracking per 2 minggu
- ✅ All response format consistent
- ✅ Authorization working properly
- ✅ Error handling correct

---

## 🎯 Next Steps

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Execute Testing**
   - Pilih salah satu method (Postman/curl/script)
   - Follow testing checklist

3. **Verify Results**
   - All 30+ endpoints respond correctly
   - Data saved to database properly
   - Error handling works

4. **Deploy**
   - After testing passed
   - Push to production

---

**Last Updated:** 17 November 2025  
**Status:** Ready for Testing ✅  
**Total Test Cases:** 30+ requests  
**Estimated Time:** 1-2 hours
