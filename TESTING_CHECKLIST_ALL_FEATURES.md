# 🧪 TESTING CHECKLIST - Semua Fitur Requirement

**Status: READY FOR TESTING**

Gunakan checklist ini untuk memverifikasi semua fitur bekerja dengan baik sebelum deployment.

---

## 📦 PRE-TEST SETUP

-   [ ] Database migrated: `php artisan migrate`
-   [ ] Storage linked: `php artisan storage:link`
-   [ ] Cache cleared: `php artisan cache:clear`
-   [ ] Config cleared: `php artisan config:clear`
-   [ ] Database backup created
-   [ ] Server running: `php artisan serve` (atau production server)

---

## 1️⃣ SISTEM PENAWARAN (Package Selection Testing)

### 1.1 Create Subscription dengan Single Course

```
POST /api/subscriptions
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json

{
    "package_type": "single_course",
    "duration": 3,
    "duration_unit": "months",
    "courses_ids": [1, 2, 3]
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] Data tersimpan di database
-   [ ] `courses_ids` berbentuk JSON array
-   [ ] `package_type` = "single_course"
-   [ ] `duration` = 3
-   [ ] `duration_unit` = "months"

### 1.2 Create Subscription dengan All-In-One

```
POST /api/subscriptions
Authorization: Bearer {JWT_TOKEN}

{
    "package_type": "all_in_one",
    "duration": 1,
    "duration_unit": "years",
    "courses_ids": []
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] `package_type` = "all_in_one"
-   [ ] `courses_ids` bisa kosong untuk all-in-one
-   [ ] `duration` = 1, `duration_unit` = "years"

### 1.3 Get Subscription Detail

```
GET /api/subscriptions/{subscription_id}
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Menampilkan package_type, duration, courses_ids
-   [ ] Data match dengan yang disimpan

### 1.4 Validation Testing

```
POST /api/subscriptions (dengan data invalid)

{
    "package_type": "invalid_type",
    "duration": 100,
    "courses_ids": [9999]
}
```

**Verifikasi:**

-   [ ] Response status: 422 Unprocessable Entity
-   [ ] Error messages muncul untuk invalid fields
-   [ ] Courses yang tidak ada ditolak

---

## 2️⃣ SISTEM PEMBAYARAN (Payment Gateway Testing)

### 2.1 Create Course Transaction

```
POST /api/transactions/courses/{course_id}
Authorization: Bearer {JWT_TOKEN}

{
    "payment_method": "va"
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] Transaction ID created
-   [ ] `transactionable_type` = "App\\Models\\Course"
-   [ ] `payment_status` = "pending"

### 2.2 Create Subscription Transaction

```
POST /api/transactions/subscription
Authorization: Bearer {JWT_TOKEN}

{
    "subscription_id": 1,
    "payment_method": "qris"
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] `transactionable_type` = "App\\Models\\Subscription"
-   [ ] Payment method tersimpan

### 2.3 Upload Payment Proof

```
POST /api/transactions/{transaction_id}/upload-proof
Authorization: Bearer {JWT_TOKEN}
Content-Type: multipart/form-data

proof_file: [binary image file]
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] `proof_url` tersimpan di database
-   [ ] File tersimpan di storage

### 2.4 Request Refund

```
POST /api/transactions/{transaction_id}/refund
Authorization: Bearer {JWT_TOKEN}

{
    "reason": "Batal ambil course"
}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] `refund_status` updated
-   [ ] Reason tersimpan

### 2.5 Admin Confirm Payment

```
POST /api/transactions/{transaction_id}/confirm
Authorization: Bearer {ADMIN_JWT_TOKEN}

{
    "status": "completed"
}
```

**Verifikasi:**

-   [ ] Response status: 200 OK (jika admin)
-   [ ] `payment_status` = "completed"
-   [ ] Non-admin mendapat 403 Forbidden

---

## 3️⃣ MENTORING ASSESSMENT (Need Assessment Testing)

### 3.1 Submit Need Assessment Form

```
POST /api/mentoring-sessions/{session_id}/need-assessments
Authorization: Bearer {JWT_TOKEN}

{
    "form_data": {
        "learning_goals": "Master Laravel frameworks",
        "previous_experience": "2 years with PHP",
        "challenges": "Database optimization concepts",
        "expectations": "Learn industry best practices"
    }
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] `form_data` tersimpan sebagai JSON
-   [ ] `completed_at` = null (belum completed)
-   [ ] Mentoring session status updated

### 3.2 Get Need Assessment

```
GET /api/mentoring-sessions/{session_id}/need-assessments
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] `form_data` ditampilkan lengkap
-   [ ] Field `is_completed` muncul
-   [ ] User bisa melihat form

### 3.3 Mark Assessment Complete

```
PUT /api/mentoring-sessions/{session_id}/need-assessments/mark-completed
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] `completed_at` terisi dengan timestamp
-   [ ] `is_completed` = true
-   [ ] MentoringSession `need_assessment_status` = "completed"

### 3.4 Delete Assessment

```
DELETE /api/mentoring-sessions/{session_id}/need-assessments
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Record dihapus dari database
-   [ ] Mentoring session status reset ke "pending"

### 3.5 Validation Testing

```
POST /api/mentoring-sessions/{session_id}/need-assessments

{
    "form_data": {
        "learning_goals": "",  // Required!
        "previous_experience": "short text"
    }
}
```

**Verifikasi:**

-   [ ] Response status: 422
-   [ ] Error: "learning_goals is required"
-   [ ] Error: "challenges is required"
-   [ ] Error: "expectations is required"

---

## 4️⃣ COACHING FILES (File Upload & Management)

### 4.1 Upload Coaching File

```
POST /api/mentoring-sessions/{session_id}/coaching-files
Authorization: Bearer {JWT_TOKEN}
Content-Type: multipart/form-data

file: [binary PDF file]
file_name: "Module_1_Laravel_Basics"
file_type: "pdf"
uploaded_by: 2
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] File saved ke `/storage/app/public/coaching-files/`
-   [ ] Record created di database
-   [ ] `file_url` generated correctly
-   [ ] `uploaded_by` = 2

### 4.2 List Coaching Files

```
GET /api/mentoring-sessions/{session_id}/coaching-files
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Array of files ditampilkan
-   [ ] Field `file_url` tersedia untuk download
-   [ ] `count` menunjukkan jumlah file

### 4.3 Get File Details

```
GET /api/mentoring-sessions/{session_id}/coaching-files/{file_id}
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] File details lengkap
-   [ ] Uploader info embedded
-   [ ] File URL valid

### 4.4 Download File

```
GET /api/mentoring-sessions/{session_id}/coaching-files/{file_id}/download
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response: File binary (application/pdf)
-   [ ] Content-Disposition header present
-   [ ] File dapat di-download
-   [ ] File integrity maintained

### 4.5 Delete Single File

```
DELETE /api/mentoring-sessions/{session_id}/coaching-files/{file_id}
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Record dihapus dari database
-   [ ] File dihapus dari storage
-   [ ] Message: "File deleted successfully"

### 4.6 Delete All Files

```
DELETE /api/mentoring-sessions/{session_id}/coaching-files
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Semua file untuk session dihapus
-   [ ] `deleted_count` muncul di response
-   [ ] Storage cleanup successful

### 4.7 File Upload Validation

Test dengan berbagai skenario:

**Skenario A: File terlalu besar (> 50MB)**

```
POST /api/mentoring-sessions/{session_id}/coaching-files
(Upload file 100MB)
```

-   [ ] Response status: 422
-   [ ] Error message tentang file size

**Skenario B: File type tidak support**

```
POST /api/mentoring-sessions/{session_id}/coaching-files
(Upload .exe atau .zip file)
```

-   [ ] Response status: 422
-   [ ] Error message tentang file type

**Skenario C: No file provided**

```
POST /api/mentoring-sessions/{session_id}/coaching-files
(No file field)
```

-   [ ] Response status: 422
-   [ ] Error: "File is required"

---

## 5️⃣ SCHOLARSHIP PORTAL (Portal Testing)

### 5.1 Get Scholarships List (Portal)

```
GET /api/scholarships
Authorization: Optional (public endpoint)
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] List of scholarships ditampilkan
-   [ ] Pagination working
-   [ ] Non-authenticated user bisa access

### 5.2 Get Scholarship Detail

```
GET /api/scholarships/{scholarship_id}
Authorization: Optional
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Scholarship details complete:
    -   [ ] Title
    -   [ ] Description
    -   [ ] Amount
    -   [ ] Requirements
    -   [ ] Deadline
-   [ ] Organization embedded (profil company):
    -   [ ] Company name
    -   [ ] Industry
    -   [ ] Logo URL
    -   [ ] Website
    -   [ ] Contact info

### 5.3 Get Organization Profile

```
GET /api/organizations/{org_id}
Authorization: Optional
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Full company profile ditampilkan
-   [ ] Related scholarships available (optional)

### 5.4 Navigation Flow Test

```
User journey:
1. GET /api/scholarships          → See list of scholarships
2. GET /api/scholarships/{id}     → See scholarship + company info
3. GET /api/organizations/{id}    → See full company profile
```

**Verifikasi:**

-   [ ] Flow logical & smooth
-   [ ] All links accessible
-   [ ] Data consistency

---

## 6️⃣ PROGRESS REPORT (Bi-Weekly Testing)

### 6.1 Submit Progress Report

```
POST /api/progress-reports
Authorization: Bearer {JWT_TOKEN}

{
    "enrollment_id": 1,
    "report_date": "2025-11-17",
    "progress_percentage": 50,
    "notes": "Student sudah menyelesaikan 2 dari 4 modules. Progres bagus.",
    "attachment_url": "https://example.com/evidence.pdf",
    "frequency": 14
}
```

**Verifikasi:**

-   [ ] Response status: 201 Created
-   [ ] Report saved ke database
-   [ ] `next_report_date` auto-calculated (14 hari kemudian)
-   [ ] Enrollment updated dengan:
    -   [ ] `progress` = 50
    -   [ ] `last_progress_report_date` = 2025-11-17
    -   [ ] `next_progress_report_date` = 2025-12-01
    -   [ ] `report_frequency` = 14

### 6.2 Get Report Detail

```
GET /api/progress-reports/{report_id}
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Report details complete
-   [ ] Enrollment info embedded
-   [ ] `is_due` calculated correctly

### 6.3 List All Reports

```
GET /api/progress-reports
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Reports pagination working
-   [ ] Can filter by enrollment_id: `?enrollment_id=1`
-   [ ] Can filter by user_id: `?user_id=3`

### 6.4 Get Reports by Enrollment

```
GET /api/enrollments/{enrollment_id}/progress-reports
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Only reports for that enrollment shown
-   [ ] Count accurate
-   [ ] Ordered by report_date descending

### 6.5 Change Report Frequency

```
POST /api/progress-reports/frequency
Authorization: Bearer {JWT_TOKEN}

{
    "enrollment_id": 1,
    "frequency": 7
}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Enrollment `report_frequency` updated to 7
-   [ ] Future reports akan use frequency 7 hari
-   [ ] Message: "Report frequency updated successfully"

### 6.6 Get Due Reports

```
GET /api/progress-reports/due
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Only reports dengan `next_report_date` <= today ditampilkan
-   [ ] Useful untuk identify mana reports yang sudah jatuh tempo

### 6.7 Update Progress Report

```
PUT /api/progress-reports/{report_id}
Authorization: Bearer {JWT_TOKEN}

{
    "progress_percentage": 75,
    "notes": "Updated progress notes",
    "attachment_url": "https://example.com/new-evidence.pdf"
}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Report updated
-   [ ] Enrollment progress_percentage also updated
-   [ ] Updated_at timestamp changed

### 6.8 Delete Progress Report

```
DELETE /api/progress-reports/{report_id}
Authorization: Bearer {JWT_TOKEN}
```

**Verifikasi:**

-   [ ] Response status: 200 OK
-   [ ] Report deleted from database
-   [ ] Message: "Progress report deleted successfully"

### 6.9 Customizable Frequency Testing

**Test: 1 Week Frequency (7 hari)**

```
POST /api/progress-reports
{
    "enrollment_id": 2,
    "report_date": "2025-11-17",
    "progress_percentage": 60,
    "frequency": 7
}
```

-   [ ] next_report_date = 2025-11-24 (7 hari kemudian)

**Test: 2 Weeks Frequency (14 hari - default)**

```
POST /api/progress-reports
{
    "enrollment_id": 3,
    "report_date": "2025-11-17",
    "progress_percentage": 60,
    "frequency": 14
}
```

-   [ ] next_report_date = 2025-12-01 (14 hari kemudian)

**Test: 3 Weeks Frequency (21 hari)**

```
POST /api/progress-reports
{
    "enrollment_id": 4,
    "report_date": "2025-11-17",
    "progress_percentage": 60,
    "frequency": 21
}
```

-   [ ] next_report_date = 2025-12-08 (21 hari kemudian)

**Test: 4 Weeks Frequency (30 hari)**

```
POST /api/progress-reports
{
    "enrollment_id": 5,
    "report_date": "2025-11-17",
    "progress_percentage": 60,
    "frequency": 30
}
```

-   [ ] next_report_date = 2025-12-17 (30 hari kemudian)

### 6.10 Validation Testing

**Invalid frequency (< 7 hari)**

-   [ ] Error returned
-   [ ] Message: "Frequency must be at least 7 days"

**Invalid frequency (> 30 hari)**

-   [ ] Error returned
-   [ ] Message: "Frequency cannot exceed 30 days"

**Missing required fields**

-   [ ] Error for enrollment_id
-   [ ] Error for progress_percentage
-   [ ] Error for notes
-   [ ] Error for report_date

---

## 7️⃣ INTEGRATION TESTING (Customer Journey)

### Full Flow: Register → Payment → Assessment → Progress

**Step 1: User Registration**

```
POST /api/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

-   [ ] User created

**Step 2: Login & Get Token**

```
POST /api/login
{
    "email": "john@example.com",
    "password": "password123"
}
```

-   [ ] JWT token received

**Step 3: Choose Subscription Package**

```
POST /api/subscriptions
{
    "package_type": "single_course",
    "duration": 3,
    "courses_ids": [1, 2]
}
```

-   [ ] Subscription created

**Step 4: Create Payment Transaction**

```
POST /api/transactions/subscription
{
    "subscription_id": 1,
    "payment_method": "va"
}
```

-   [ ] Transaction created, awaiting payment

**Step 5: Create Mentoring Session**

```
POST /api/mentoring-sessions
{
    "mentor_id": 2,
    "schedule": "2025-11-20 14:00",
    "type": "one-on-one"
}
```

-   [ ] Session scheduled

**Step 6: Submit Need Assessment**

```
POST /api/mentoring-sessions/{id}/need-assessments
{
    "form_data": {
        "learning_goals": "...",
        "previous_experience": "...",
        "challenges": "...",
        "expectations": "..."
    }
}
```

-   [ ] Assessment submitted

**Step 7: Mentor Uploads Coaching Files**

```
POST /api/mentoring-sessions/{id}/coaching-files
(upload file)
```

-   [ ] File uploaded

**Step 8: Student Accesses Coaching Files**

```
GET /api/mentoring-sessions/{id}/coaching-files
```

-   [ ] Files listed

**Step 9: Track Progress with Reports**

```
POST /api/progress-reports
{
    "enrollment_id": 1,
    "progress_percentage": 50,
    "notes": "...",
    "frequency": 14
}
```

-   [ ] Report created

**Step 10: Check Progress Over Time**

```
GET /api/enrollments/{id}/progress-reports
```

-   [ ] Multiple reports tracked

---

## ✅ FINAL VERIFICATION

### All Endpoints Working?

-   [ ] Sistem Penawaran: 5/5 endpoints ✓
-   [ ] Sistem Pembayaran: 6/6 endpoints ✓
-   [ ] Need Assessment: 4/4 endpoints ✓
-   [ ] Coaching Files: 6/6 endpoints ✓
-   [ ] Scholarship Portal: 4/4 endpoints ✓
-   [ ] Progress Report: 8/8 endpoints ✓

### Database?

-   [ ] All migrations run successfully
-   [ ] Tables exist & have data
-   [ ] Foreign keys working
-   [ ] JSON fields save correctly
-   [ ] Timestamps updated properly

### Validation?

-   [ ] All FormRequest validations working
-   [ ] Invalid data rejected properly
-   [ ] Error messages clear
-   [ ] Status codes correct (201, 200, 422, 404, 500)

### Authorization?

-   [ ] Authentication required where needed
-   [ ] JWT token validation working
-   [ ] Role-based access working (admin endpoints)
-   [ ] User can only access own data

### Files & Storage?

-   [ ] File upload working
-   [ ] Storage directory created
-   [ ] File paths correct
-   [ ] File download working
-   [ ] File deletion removes from storage

### Response Format?

-   [ ] All responses use Resource classes
-   [ ] Consistent JSON structure
-   [ ] Relationships embedded properly
-   [ ] Pagination working

---

## 📝 NOTES & ISSUES TRACKING

### Issues Found:

(List any issues during testing)

1. Issue: ****\_\_\_****
    - Severity: Low/Medium/High
    - Status: Open/In Progress/Fixed
    - Notes: ****\_\_\_****

### Additional Observations:

(Any other notes)

---

## 🎉 TEST RESULTS SUMMARY

**Date Tested:** ****\_\_****  
**Tester Name:** ****\_\_****  
**Environment:** Development/Staging/Production

**Overall Status:**

-   [ ] ✅ ALL TESTS PASSED - Ready for Deployment
-   [ ] ⚠️ SOME TESTS FAILED - See issues above
-   [ ] ❌ MAJOR ISSUES - Do not deploy

**Recommendation:** ****\_\_\_****

---

Generated: 2025-11-17
Status: Ready for Testing
Total Test Cases: 50+
