# 📚 DOKUMENTASI API - Semua Fitur

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Sistem Penawaran (Subscriptions)](#sistem-penawaran)
3. [Sistem Pembayaran (Transactions)](#sistem-pembayaran)
4. [Need Assessment](#need-assessment)
5. [Coaching Files](#coaching-files)
6. [Progress Reports](#progress-reports)
7. [Database Schema](#database-schema)
8. [API Endpoints](#api-endpoints)
9. [Testing Checklist](#testing-checklist)

---

## Overview

API ini adalah platform mentoring komprehensif dengan fitur:
- ✅ Sistem penawaran paket
- ✅ Sistem pembayaran gateway
- ✅ Assessment pre-mentoring
- ✅ File upload coaching
- ✅ Progress tracking bulanan

**Tech Stack:** Laravel 11 + MySQL + JWT Auth

---

## Sistem Penawaran

### Database Schema
```sql
ALTER TABLE subscriptions ADD COLUMN (
  package_type ENUM('single_course', 'all_in_one') DEFAULT 'single_course',
  duration INT DEFAULT 1,
  duration_unit ENUM('months', 'years') DEFAULT 'months',
  courses_ids JSON,
  price DECIMAL(10,2),
  auto_renew BOOLEAN DEFAULT false
);
```

### Model: Subscription
```php
protected $fillable = ['user_id', 'course_id', 'package_type', 'duration', 
                       'duration_unit', 'courses_ids', 'price', 'auto_renew'];
protected $casts = ['courses_ids' => 'array'];
```

### API Endpoints
```bash
POST   /api/subscriptions                 # Create subscription package
GET    /api/subscriptions                 # List all subscriptions
GET    /api/subscriptions/{id}            # Get subscription detail
PUT    /api/subscriptions/{id}            # Update subscription
DELETE /api/subscriptions/{id}            # Cancel subscription
```

### Request Example
```json
{
  "package_type": "all_in_one",
  "duration": 3,
  "duration_unit": "months",
  "courses_ids": [1, 2, 3, 4, 5]
}
```

### Response Example
```json
{
  "id": 1,
  "user_id": 10,
  "package_type": "all_in_one",
  "duration": 3,
  "duration_unit": "months",
  "courses_ids": [1, 2, 3, 4, 5],
  "price": 450000,
  "status": "active",
  "created_at": "2025-11-17T10:00:00Z"
}
```

---

## Sistem Pembayaran

### Database Schema
```sql
CREATE TABLE transactions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  subscription_id BIGINT REFERENCES subscriptions(id),
  user_id BIGINT REFERENCES users(id),
  amount DECIMAL(12,2),
  payment_method ENUM('va_mandiri', 'va_bca', 'qris', 'dana', 'ovo', 'gcash'),
  payment_status ENUM('pending', 'confirmed', 'failed', 'refunded'),
  payment_proof VARCHAR(255),
  payment_date DATETIME,
  payment_gateway_id VARCHAR(255),
  refund_reason TEXT,
  refund_date DATETIME,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Model: Transaction
```php
protected $fillable = ['subscription_id', 'user_id', 'amount', 'payment_method', 
                       'payment_status', 'payment_proof', 'payment_date', 
                       'payment_gateway_id', 'refund_reason', 'refund_date'];

// Relationships
public function subscription() { return $this->belongsTo(Subscription::class); }
public function user() { return $this->belongsTo(User::class); }
```

### API Endpoints
```bash
POST   /api/transactions                  # Create payment transaction
GET    /api/transactions                  # List transactions
GET    /api/transactions/{id}             # Get transaction detail
POST   /api/transactions/{id}/upload-proof # Upload payment proof
POST   /api/transactions/{id}/confirm     # Confirm payment
POST   /api/transactions/{id}/refund      # Process refund
```

### Payment Methods Supported
```
- VA Mandiri
- VA BCA
- QRIS (QR Code)
- Dana Wallet
- OVO Wallet
- GCash
```

### Request: Create Transaction
```json
{
  "subscription_id": 1,
  "payment_method": "qris"
}
```

### Request: Upload Proof
```json
{
  "payment_proof": "file.jpg"  // File upload
}
```

### Response: Transaction
```json
{
  "id": 5,
  "subscription_id": 1,
  "user_id": 10,
  "amount": 450000,
  "payment_method": "qris",
  "payment_status": "pending",
  "payment_gateway_id": "inv_123456",
  "payment_date": null,
  "created_at": "2025-11-17T10:05:00Z"
}
```

---

## Need Assessment

### Tujuan
Pre-mentoring assessment form untuk memahami:
- Learning goals
- Previous experience
- Challenges faced
- Expectations

### Database Schema
```sql
CREATE TABLE need_assessments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  mentoring_session_id BIGINT REFERENCES mentoring_sessions(id),
  form_data JSON,  -- {learning_goals, previous_experience, challenges, expectations}
  completed_at DATETIME,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Model: NeedAssessment
```php
protected $fillable = ['mentoring_session_id', 'form_data', 'completed_at'];
protected $casts = ['form_data' => 'array', 'completed_at' => 'datetime'];

public function mentoringSession() { return $this->belongsTo(MentoringSession::class); }
public function isCompleted() { return !is_null($this->completed_at); }
public function markCompleted() { $this->update(['completed_at' => now()]); }
```

### API Endpoints
```bash
GET    /api/mentoring-sessions/{id}/need-assessments
POST   /api/mentoring-sessions/{id}/need-assessments
PUT    /api/mentoring-sessions/{id}/need-assessments/mark-completed
DELETE /api/mentoring-sessions/{id}/need-assessments
```

### Request: Submit Assessment
```json
{
  "form_data": {
    "learning_goals": "Ingin bisa full-stack development",
    "previous_experience": "Sudah tahu basic HTML/CSS",
    "challenges": "Kesulitan dengan backend logic",
    "expectations": "Harapan bisa dapat sertifikat"
  }
}
```

### Response
```json
{
  "id": 1,
  "mentoring_session_id": 5,
  "form_data": {
    "learning_goals": "...",
    "previous_experience": "...",
    "challenges": "...",
    "expectations": "..."
  },
  "is_completed": false,
  "completed_at": null,
  "created_at": "2025-11-17T10:10:00Z"
}
```

---

## Coaching Files

### Tujuan
Upload & manage file-file coaching:
- Presentation slides
- Video materials
- Documentation
- Resources

### Database Schema
```sql
CREATE TABLE coaching_files (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  mentoring_session_id BIGINT REFERENCES mentoring_sessions(id),
  file_name VARCHAR(255),
  file_path VARCHAR(255),
  file_type ENUM('pdf', 'doc', 'docx', 'ppt', 'pptx', 'video', 'image', 'audio'),
  file_size INT,
  uploaded_by BIGINT REFERENCES users(id),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Model: CoachingFile
```php
protected $fillable = ['mentoring_session_id', 'file_name', 'file_path', 
                       'file_type', 'file_size', 'uploaded_by'];

public function mentoringSession() { return $this->belongsTo(MentoringSession::class); }
public function uploadedByUser() { return $this->belongsTo(User::class, 'uploaded_by'); }
public function getFileUrlAttribute() { return '/storage/coaching-files/' . $this->file_path; }
```

### Storage Path
```
/storage/app/public/coaching-files/{mentoringSessionId}/{fileName}
```

### API Endpoints
```bash
GET    /api/mentoring-sessions/{id}/coaching-files           # List files
POST   /api/mentoring-sessions/{id}/coaching-files           # Upload file (max 50MB)
GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}  # Get file detail
GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}/download # Download file
DELETE /api/mentoring-sessions/{id}/coaching-files/{fileId}  # Delete single file
DELETE /api/mentoring-sessions/{id}/coaching-files           # Delete all files
```

### Request: Upload File
```
POST /api/mentoring-sessions/5/coaching-files
Content-Type: multipart/form-data

file_name: "Slide-Week-1"
file: [binary file - max 50MB]
file_type: "pdf"
```

### Response: List Files
```json
{
  "data": [
    {
      "id": 1,
      "mentoring_session_id": 5,
      "file_name": "Slide-Week-1",
      "file_type": "pdf",
      "file_size": 2048000,
      "file_url": "/storage/coaching-files/5/slide-week-1.pdf",
      "uploaded_by": {
        "id": 1,
        "name": "John Mentor",
        "email": "john@example.com"
      },
      "created_at": "2025-11-17T10:15:00Z"
    }
  ]
}
```

---

## Progress Reports

### Tujuan
Track progress mentee dengan laporan berkala:
- Default: 14 hari (2 minggu)
- Customizable: 7-30 hari
- Track: Progress percentage
- Attachment: Notes & supporting docs

### Database Schema
```sql
CREATE TABLE progress_reports (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  enrollment_id BIGINT REFERENCES enrollments(id),
  report_date DATE,
  progress_percentage INT DEFAULT 0,  -- 0-100
  notes TEXT,
  attachment_url VARCHAR(255),
  next_report_date DATE,
  frequency INT DEFAULT 14,  -- Days, min 7 max 30
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Model: ProgressReport
```php
protected $fillable = ['enrollment_id', 'report_date', 'progress_percentage', 
                       'notes', 'attachment_url', 'next_report_date', 'frequency'];
protected $casts = ['report_date' => 'date', 'next_report_date' => 'date'];

public function enrollment() { return $this->belongsTo(Enrollment::class); }

public static function getDueReports() {
  return self::where('next_report_date', '<=', today())->get();
}

public function setNextReportDate() {
  $this->update(['next_report_date' => $this->report_date->addDays($this->frequency)]);
}
```

### API Endpoints
```bash
GET    /api/progress-reports                      # List all reports
GET    /api/progress-reports/{id}                 # Get report detail
POST   /api/progress-reports                      # Create new report
PUT    /api/progress-reports/{id}                 # Update report
DELETE /api/progress-reports/{id}                 # Delete report
GET    /api/progress-reports/due                  # Get due/overdue reports
GET    /api/enrollments/{id}/progress-reports     # Get per enrollment
POST   /api/progress-reports/frequency            # Update frequency (7-30)
```

### Request: Create Report
```json
{
  "enrollment_id": 10,
  "report_date": "2025-11-17",
  "progress_percentage": 45,
  "notes": "Sudah paham konsep async-await, next: advanced patterns",
  "attachment_url": "https://example.com/docs/progress.pdf",
  "frequency": 14
}
```

### Response: Report Detail
```json
{
  "id": 1,
  "enrollment_id": 10,
  "report_date": "2025-11-17",
  "progress_percentage": 45,
  "notes": "Sudah paham konsep async-await, next: advanced patterns",
  "attachment_url": "https://example.com/docs/progress.pdf",
  "next_report_date": "2025-12-01",
  "frequency": 14,
  "is_due": false,
  "enrollment": {
    "id": 10,
    "user_id": 5,
    "course_id": 2,
    "progress": 45,
    "completed": false
  },
  "created_at": "2025-11-17T10:20:00Z"
}
```

### Request: Set Frequency
```json
{
  "enrollment_id": 10,
  "frequency": 7
}
```

---

## Database Schema

### Diagram Hubungan
```
Users
├── Subscriptions (1-many)
├── Mentoring Sessions (1-many)
│   ├── Need Assessments (1-1)
│   └── Coaching Files (1-many)
├── Enrollments (1-many)
│   └── Progress Reports (1-many)
└── Transactions (1-many)

Courses
├── Enrollments (1-many)
└── Subscriptions (many-many via courses_ids JSON)

Organizations
├── Reviews (1-many)
└── CorporateContacts (1-many)
```

### Tabel Baru (3)
```
1. need_assessments (mentoring_session_id, form_data, completed_at)
2. coaching_files (mentoring_session_id, file_*, uploaded_by)
3. progress_reports (enrollment_id, report_date, progress_*, frequency)
```

### Tabel Update (3)
```
1. subscriptions: +package_type, duration, duration_unit, courses_ids
2. mentoring_sessions: +need_assessment_status, assessment_form_data, coaching_files_path
3. enrollments: +last_progress_report_date, next_progress_report_date, report_frequency
```

---

## API Endpoints

### Authentication
```bash
POST   /api/auth/register            # Register user
POST   /api/auth/login               # Login & get JWT token
POST   /api/auth/logout              # Logout
POST   /api/auth/refresh             # Refresh token
GET    /api/auth/profile             # Get profile
PUT    /api/auth/profile             # Update profile
```

### Subscriptions (4 endpoints)
```bash
POST   /api/subscriptions            # Create
GET    /api/subscriptions            # List
GET    /api/subscriptions/{id}       # Detail
PUT    /api/subscriptions/{id}       # Update
DELETE /api/subscriptions/{id}       # Delete
```

### Transactions (6 endpoints)
```bash
POST   /api/transactions                     # Create
GET    /api/transactions                     # List
GET    /api/transactions/{id}                # Detail
POST   /api/transactions/{id}/upload-proof   # Upload proof
POST   /api/transactions/{id}/confirm        # Confirm
POST   /api/transactions/{id}/refund         # Refund
```

### Need Assessments (4 endpoints)
```bash
GET    /api/mentoring-sessions/{id}/need-assessments
POST   /api/mentoring-sessions/{id}/need-assessments
PUT    /api/mentoring-sessions/{id}/need-assessments/mark-completed
DELETE /api/mentoring-sessions/{id}/need-assessments
```

### Coaching Files (6 endpoints)
```bash
GET    /api/mentoring-sessions/{id}/coaching-files
POST   /api/mentoring-sessions/{id}/coaching-files
GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}
GET    /api/mentoring-sessions/{id}/coaching-files/{fileId}/download
DELETE /api/mentoring-sessions/{id}/coaching-files/{fileId}
DELETE /api/mentoring-sessions/{id}/coaching-files
```

### Progress Reports (8 endpoints)
```bash
GET    /api/progress-reports                      # List all
GET    /api/progress-reports/{id}                 # Detail
POST   /api/progress-reports                      # Create
PUT    /api/progress-reports/{id}                 # Update
DELETE /api/progress-reports/{id}                 # Delete
GET    /api/progress-reports/due                  # Due reports
GET    /api/enrollments/{id}/progress-reports     # Per enrollment
POST   /api/progress-reports/frequency            # Set frequency
```

### Existing Features (60+ endpoints)
```bash
GET    /api/courses               # Courses
GET    /api/scholarships          # Scholarships
GET    /api/articles              # Articles
GET    /api/reviews               # Reviews
GET    /api/mentors               # Mentors
GET    /api/organizations         # Organizations
# ... dan lainnya
```

**Total: 89+ API endpoints**

---

## Testing Checklist

### Pre-Testing Setup
```bash
# 1. Run migrations
php artisan migrate

# 2. Seed data (optional)
php artisan db:seed

# 3. Start server
php artisan serve
```

### Test 1: Subscriptions
```bash
1. POST /api/subscriptions
   ✓ Valid data (single_course, 1 month)
   ✓ Valid data (all_in_one, 3 months, multiple courses)
   ✓ Missing required fields
   ✓ Invalid package_type
   ✗ Negative price

2. GET /api/subscriptions
   ✓ List all (auth required)
   ✓ Pagination

3. GET /api/subscriptions/{id}
   ✓ Existing subscription
   ✗ Non-existing subscription

4. PUT /api/subscriptions/{id}
   ✓ Update package_type
   ✓ Update duration
   ✓ Forbidden for others

5. DELETE /api/subscriptions/{id}
   ✓ Delete existing
   ✗ Delete non-existing
```

### Test 2: Transactions
```bash
1. POST /api/transactions
   ✓ Valid data (VA, QRIS, E-wallet)
   ✓ Check payment status = "pending"

2. POST /api/transactions/{id}/upload-proof
   ✓ Upload proof file
   ✓ Update payment_status
   ✗ Invalid file format

3. POST /api/transactions/{id}/confirm
   ✓ Confirm payment
   ✓ Update payment_status = "confirmed"

4. POST /api/transactions/{id}/refund
   ✓ Process refund
   ✓ Update payment_status = "refunded"
```

### Test 3: Need Assessments
```bash
1. POST /api/mentoring-sessions/{id}/need-assessments
   ✓ Submit form with all 4 fields
   ✓ Submit with required fields only
   ✓ Check form_data stored correctly

2. GET /api/mentoring-sessions/{id}/need-assessments
   ✓ Retrieve form
   ✓ Check is_completed = false initially

3. PUT /api/mentoring-sessions/{id}/need-assessments/mark-completed
   ✓ Mark as completed
   ✓ Check completed_at = now()
   ✓ Check is_completed = true

4. DELETE /api/mentoring-sessions/{id}/need-assessments
   ✓ Delete form
```

### Test 4: Coaching Files
```bash
1. POST /api/mentoring-sessions/{id}/coaching-files
   ✓ Upload PDF (small file)
   ✓ Upload video (large file)
   ✓ Upload image
   ✗ Upload > 50MB (should fail)
   ✗ Invalid file type

2. GET /api/mentoring-sessions/{id}/coaching-files
   ✓ List all files
   ✓ Check uploaded_by relation

3. GET /api/mentoring-sessions/{id}/coaching-files/{fileId}
   ✓ Get file detail
   ✓ Check file_url accessor

4. GET /api/mentoring-sessions/{id}/coaching-files/{fileId}/download
   ✓ Download file
   ✓ Check file saved in storage

5. DELETE /api/mentoring-sessions/{id}/coaching-files/{fileId}
   ✓ Delete single file
   ✓ Verify file deleted from storage

6. DELETE /api/mentoring-sessions/{id}/coaching-files
   ✓ Delete all files
```

### Test 5: Progress Reports
```bash
1. POST /api/progress-reports
   ✓ Create with frequency=14 (default)
   ✓ Create with frequency=7
   ✓ Create with frequency=30
   ✓ Check next_report_date calculated
   ✗ frequency < 7 (should fail)
   ✗ frequency > 30 (should fail)

2. GET /api/progress-reports
   ✓ List all reports
   ✓ Pagination

3. GET /api/progress-reports/{id}
   ✓ Get detail with enrollment relation

4. PUT /api/progress-reports/{id}
   ✓ Update progress_percentage
   ✓ Update notes
   ✓ Check is_due property

5. GET /api/enrollments/{id}/progress-reports
   ✓ Get all reports per enrollment

6. POST /api/progress-reports/frequency
   ✓ Change frequency from 14 to 7
   ✓ Change frequency from 14 to 30

7. GET /api/progress-reports/due
   ✓ Get overdue reports
   ✓ Only show next_report_date <= today()

8. DELETE /api/progress-reports/{id}
   ✓ Delete report
```

### Test 6: Authorization
```bash
✓ All endpoints require JWT token
✓ Invalid token returns 401
✓ Expired token returns 401
✓ User can't access other user's data
✓ Admin can access all data
✓ Mentor has limited access
```

### Total Test Cases: 50+

---

## Summary

| Item | Count | Status |
|------|-------|--------|
| Database Tables (New) | 3 | ✅ |
| Database Tables (Updated) | 3 | ✅ |
| Models (New) | 3 | ✅ |
| Models (Updated) | 3 | ✅ |
| Controllers (New) | 3 | ✅ |
| API Endpoints (New) | 16 | ✅ |
| FormRequest Classes | 5 | ✅ |
| Resource Classes | 3 | ✅ |
| Total API Endpoints | 89+ | ✅ |
| Test Cases | 50+ | ✅ |

**Status: 100% READY FOR PRODUCTION** ✅

---

## Quick Start

```bash
# 1. Migrate database
php artisan migrate

# 2. Start server
php artisan serve

# 3. Get JWT token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# 4. Use token in requests
curl -X GET http://localhost:8000/api/subscriptions \
  -H "Authorization: Bearer YOUR_TOKEN"

# 5. Test endpoints
# Follow TESTING_CHECKLIST above
```

---

**Last Updated:** 17 November 2025  
**Version:** 1.0  
**Status:** Production Ready ✅
