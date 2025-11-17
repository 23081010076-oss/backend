# 🧪 API TESTING GUIDE

Testing manual untuk semua 16 endpoint baru.

---

## 📋 Setup Awal

### 1. Start Server

```bash
cd d:\final project
php artisan serve
# Server running at: http://localhost:8000
```

### 2. Get JWT Token

**Endpoint:**

```bash
POST http://localhost:8000/api/auth/login
```

**Headers:**

```
Content-Type: application/json
```

**Request Body:**

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

**Copy `access_token` untuk semua request berikutnya**

### 3. Setup Header (Semua Request)

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

---

## 1️⃣ SUBSCRIPTION ENDPOINTS

### 1.1 Create Subscription (Single Course)

**Endpoint:**

```bash
POST http://localhost:8000/api/subscriptions
```

**Request Body:**

```json
{
    "package_type": "single_course",
    "duration": 1,
    "duration_unit": "months",
    "courses_ids": [1]
}
```

**Expected Response (201):**

```json
{
    "id": 1,
    "user_id": 1,
    "package_type": "single_course",
    "duration": 1,
    "duration_unit": "months",
    "courses_ids": [1],
    "status": "active",
    "created_at": "2025-11-17T10:00:00Z"
}
```

---

### 1.2 Create Subscription (All-in-One)

**Endpoint:**

```bash
POST http://localhost:8000/api/subscriptions
```

**Request Body:**

```json
{
    "package_type": "all_in_one",
    "duration": 3,
    "duration_unit": "months",
    "courses_ids": [1, 2, 3, 4, 5]
}
```

**Expected Response (201):**

```json
{
    "id": 2,
    "user_id": 1,
    "package_type": "all_in_one",
    "duration": 3,
    "duration_unit": "months",
    "courses_ids": [1, 2, 3, 4, 5],
    "price": 450000,
    "status": "active",
    "created_at": "2025-11-17T10:05:00Z"
}
```

---

### 1.3 Get All Subscriptions

**Endpoint:**

```bash
GET http://localhost:8000/api/subscriptions
```

**Expected Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "package_type": "single_course",
            "duration": 1,
            "duration_unit": "months",
            "courses_ids": [1],
            "status": "active"
        },
        {
            "id": 2,
            "user_id": 1,
            "package_type": "all_in_one",
            "duration": 3,
            "duration_unit": "months",
            "courses_ids": [1, 2, 3, 4, 5],
            "status": "active"
        }
    ]
}
```

---

### 1.4 Get Subscription Detail

**Endpoint:**

```bash
GET http://localhost:8000/api/subscriptions/1
```

**Expected Response (200):**

```json
{
    "id": 1,
    "user_id": 1,
    "package_type": "single_course",
    "duration": 1,
    "duration_unit": "months",
    "courses_ids": [1],
    "status": "active",
    "created_at": "2025-11-17T10:00:00Z",
    "updated_at": "2025-11-17T10:00:00Z"
}
```

---

### 1.5 Update Subscription

**Endpoint:**

```bash
PUT http://localhost:8000/api/subscriptions/1
```

**Request Body:**

```json
{
    "duration": 3,
    "auto_renew": true
}
```

**Expected Response (200):**

```json
{
    "id": 1,
    "user_id": 1,
    "package_type": "single_course",
    "duration": 3,
    "duration_unit": "months",
    "courses_ids": [1],
    "auto_renew": true,
    "status": "active",
    "updated_at": "2025-11-17T10:10:00Z"
}
```

---

### 1.6 Delete Subscription

**Endpoint:**

```bash
DELETE http://localhost:8000/api/subscriptions/1
```

**Expected Response (200):**

```json
{
    "message": "Subscription deleted successfully"
}
```

**Verify:** GET /api/subscriptions/1 → 404 Not Found

---

## 2️⃣ TRANSACTION ENDPOINTS

### 2.1 Create Transaction

**Endpoint:**

```bash
POST http://localhost:8000/api/transactions
```

**Request Body:**

```json
{
    "subscription_id": 2,
    "payment_method": "qris"
}
```

**Expected Response (201):**

```json
{
    "id": 1,
    "subscription_id": 2,
    "user_id": 1,
    "amount": 450000,
    "payment_method": "qris",
    "payment_status": "pending",
    "payment_gateway_id": "inv_123456",
    "payment_date": null,
    "created_at": "2025-11-17T10:15:00Z"
}
```

---

### 2.2 Get All Transactions

**Endpoint:**

```bash
GET http://localhost:8000/api/transactions
```

**Expected Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "subscription_id": 2,
            "user_id": 1,
            "amount": 450000,
            "payment_method": "qris",
            "payment_status": "pending",
            "created_at": "2025-11-17T10:15:00Z"
        }
    ]
}
```

---

### 2.3 Get Transaction Detail

**Endpoint:**

```bash
GET http://localhost:8000/api/transactions/1
```

**Expected Response (200):**

```json
{
    "id": 1,
    "subscription_id": 2,
    "user_id": 1,
    "amount": 450000,
    "payment_method": "qris",
    "payment_status": "pending",
    "payment_gateway_id": "inv_123456",
    "payment_date": null,
    "created_at": "2025-11-17T10:15:00Z",
    "updated_at": "2025-11-17T10:15:00Z"
}
```

---

### 2.4 Upload Payment Proof

**Endpoint:**

```bash
POST http://localhost:8000/api/transactions/1/upload-proof
```

**Headers:**

```
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
```

**Request Body (Form Data):**

```
payment_proof: [Select image file: proof.jpg]
```

**Expected Response (200):**

```json
{
    "message": "Payment proof uploaded successfully",
    "payment_status": "pending",
    "payment_proof": "transactions/proof_123.jpg"
}
```

---

### 2.5 Confirm Payment

**Endpoint:**

```bash
POST http://localhost:8000/api/transactions/1/confirm
```

**Request Body:**

```json
{}
```

**Expected Response (200):**

```json
{
    "message": "Payment confirmed successfully",
    "payment_status": "confirmed",
    "payment_date": "2025-11-17T10:20:00Z"
}
```

---

### 2.6 Process Refund

**Endpoint:**

```bash
POST http://localhost:8000/api/transactions/1/refund
```

**Request Body:**

```json
{
    "refund_reason": "User request - change of plans"
}
```

**Expected Response (200):**

```json
{
    "message": "Refund processed successfully",
    "payment_status": "refunded",
    "refund_date": "2025-11-17T10:25:00Z"
}
```

---

## 3️⃣ NEED ASSESSMENT ENDPOINTS

### Pre-requisite: Get Mentoring Session ID

**Endpoint:**

```bash
GET http://localhost:8000/api/mentoring-sessions
```

**Note:** Use mentoring_session_id from response (or create new one)

---

### 3.1 Submit Need Assessment Form

**Endpoint:**

```bash
POST http://localhost:8000/api/mentoring-sessions/1/need-assessments
```

**Request Body:**

```json
{
    "form_data": {
        "learning_goals": "Menjadi full-stack developer yang kompeten",
        "previous_experience": "Sudah belajar HTML/CSS/JavaScript dasar, tapi belum pernah backend",
        "challenges": "Kesulitan memahami async/await dan database design",
        "expectations": "Ingin dapat job sebagai junior developer dalam 6 bulan"
    }
}
```

**Expected Response (201):**

```json
{
    "id": 1,
    "mentoring_session_id": 1,
    "form_data": {
        "learning_goals": "Menjadi full-stack developer yang kompeten",
        "previous_experience": "Sudah belajar HTML/CSS/JavaScript dasar, tapi belum pernah backend",
        "challenges": "Kesulitan memahami async/await dan database design",
        "expectations": "Ingin dapat job sebagai junior developer dalam 6 bulan"
    },
    "is_completed": false,
    "completed_at": null,
    "created_at": "2025-11-17T10:30:00Z"
}
```

---

### 3.2 Get Need Assessment Form

**Endpoint:**

```bash
GET http://localhost:8000/api/mentoring-sessions/1/need-assessments
```

**Expected Response (200):**

```json
{
    "id": 1,
    "mentoring_session_id": 1,
    "form_data": {
        "learning_goals": "Menjadi full-stack developer yang kompeten",
        "previous_experience": "Sudah belajar HTML/CSS/JavaScript dasar, tapi belum pernah backend",
        "challenges": "Kesulitan memahami async/await dan database design",
        "expectations": "Ingin dapat job sebagai junior developer dalam 6 bulan"
    },
    "is_completed": false,
    "completed_at": null,
    "created_at": "2025-11-17T10:30:00Z"
}
```

---

### 3.3 Mark Assessment as Completed

**Endpoint:**

```bash
PUT http://localhost:8000/api/mentoring-sessions/1/need-assessments/mark-completed
```

**Request Body:**

```json
{}
```

**Expected Response (200):**

```json
{
    "message": "Assessment marked as completed",
    "is_completed": true,
    "completed_at": "2025-11-17T10:35:00Z"
}
```

---

### 3.4 Delete Need Assessment

**Endpoint:**

```bash
DELETE http://localhost:8000/api/mentoring-sessions/1/need-assessments
```

**Expected Response (200):**

```json
{
    "message": "Assessment deleted successfully"
}
```

---

## 4️⃣ COACHING FILES ENDPOINTS

### 4.1 Upload Coaching File

**Endpoint:**

```bash
POST http://localhost:8000/api/mentoring-sessions/1/coaching-files
```

**Headers:**

```
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
```

**Request Body (Form Data):**

```
file_name: "Slide-Week-1-Introduction"
file: [Select file: slide-week-1.pdf]
file_type: "pdf"
uploaded_by: 1
```

**Expected Response (201):**

```json
{
    "id": 1,
    "mentoring_session_id": 1,
    "file_name": "Slide-Week-1-Introduction",
    "file_type": "pdf",
    "file_size": 2048000,
    "file_url": "/storage/coaching-files/1/slide-week-1-introduction.pdf",
    "uploaded_by": {
        "id": 1,
        "name": "John Mentor",
        "email": "john@example.com"
    },
    "created_at": "2025-11-17T10:40:00Z"
}
```

---

### 4.2 Upload Video File

**Endpoint:**

```bash
POST http://localhost:8000/api/mentoring-sessions/1/coaching-files
```

**Request Body (Form Data):**

```
file_name: "Video-Tutorial-AsyncAwait"
file: [Select file: tutorial-async-await.mp4]  (Max 50MB)
file_type: "video"
uploaded_by: 1
```

**Expected Response (201):**

```json
{
    "id": 2,
    "mentoring_session_id": 1,
    "file_name": "Video-Tutorial-AsyncAwait",
    "file_type": "video",
    "file_size": 25000000,
    "file_url": "/storage/coaching-files/1/video-tutorial-asyncawait.mp4",
    "uploaded_by": {
        "id": 1,
        "name": "John Mentor",
        "email": "john@example.com"
    },
    "created_at": "2025-11-17T10:45:00Z"
}
```

---

### 4.3 Get All Coaching Files

**Endpoint:**

```bash
GET http://localhost:8000/api/mentoring-sessions/1/coaching-files
```

**Expected Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "mentoring_session_id": 1,
            "file_name": "Slide-Week-1-Introduction",
            "file_type": "pdf",
            "file_size": 2048000,
            "file_url": "/storage/coaching-files/1/slide-week-1-introduction.pdf"
        },
        {
            "id": 2,
            "mentoring_session_id": 1,
            "file_name": "Video-Tutorial-AsyncAwait",
            "file_type": "video",
            "file_size": 25000000,
            "file_url": "/storage/coaching-files/1/video-tutorial-asyncawait.mp4"
        }
    ]
}
```

---

### 4.4 Get Single File Detail

**Endpoint:**

```bash
GET http://localhost:8000/api/mentoring-sessions/1/coaching-files/1
```

**Expected Response (200):**

```json
{
    "id": 1,
    "mentoring_session_id": 1,
    "file_name": "Slide-Week-1-Introduction",
    "file_type": "pdf",
    "file_size": 2048000,
    "file_url": "/storage/coaching-files/1/slide-week-1-introduction.pdf",
    "uploaded_by": {
        "id": 1,
        "name": "John Mentor",
        "email": "john@example.com"
    },
    "created_at": "2025-11-17T10:40:00Z"
}
```

---

### 4.5 Download File

**Endpoint:**

```bash
GET http://localhost:8000/api/mentoring-sessions/1/coaching-files/1/download
```

**Expected Response:**

-   File binary downloaded
-   Response Headers:
    ```
    Content-Type: application/pdf
    Content-Disposition: attachment; filename="slide-week-1-introduction.pdf"
    ```

---

### 4.6 Delete Single File

**Endpoint:**

```bash
DELETE http://localhost:8000/api/mentoring-sessions/1/coaching-files/1
```

**Expected Response (200):**

```json
{
    "message": "File deleted successfully"
}
```

---

### 4.7 Delete All Files

**Endpoint:**

```bash
DELETE http://localhost:8000/api/mentoring-sessions/1/coaching-files
```

**Expected Response (200):**

```json
{
    "message": "All coaching files deleted successfully"
}
```

---

## 5️⃣ PROGRESS REPORT ENDPOINTS

### Pre-requisite: Get Enrollment ID

**Endpoint:**

```bash
GET http://localhost:8000/api/enrollments
```

**Note:** Use enrollment_id dari response (atau create new)

---

### 5.1 Create Progress Report

**Endpoint:**

```bash
POST http://localhost:8000/api/progress-reports
```

**Request Body:**

```json
{
    "enrollment_id": 1,
    "report_date": "2025-11-17",
    "progress_percentage": 45,
    "notes": "Sudah paham konsep async/await dan promises. Minggu depan: advanced patterns dan error handling",
    "attachment_url": "https://example.com/docs/progress-week1.pdf",
    "frequency": 14
}
```

**Expected Response (201):**

```json
{
    "id": 1,
    "enrollment_id": 1,
    "report_date": "2025-11-17",
    "progress_percentage": 45,
    "notes": "Sudah paham konsep async/await dan promises. Minggu depan: advanced patterns dan error handling",
    "attachment_url": "https://example.com/docs/progress-week1.pdf",
    "next_report_date": "2025-12-01",
    "frequency": 14,
    "created_at": "2025-11-17T11:00:00Z"
}
```

---

### 5.2 Get All Progress Reports

**Endpoint:**

```bash
GET http://localhost:8000/api/progress-reports
```

**Expected Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "enrollment_id": 1,
            "report_date": "2025-11-17",
            "progress_percentage": 45,
            "notes": "Sudah paham konsep async/await dan promises...",
            "next_report_date": "2025-12-01",
            "frequency": 14,
            "is_due": false
        },
        {
            "id": 2,
            "enrollment_id": 1,
            "report_date": "2025-12-01",
            "progress_percentage": 60,
            "notes": "Sudah belajar advanced patterns...",
            "next_report_date": "2025-12-15",
            "frequency": 14,
            "is_due": false
        }
    ]
}
```

---

### 5.3 Get Progress Report Detail

**Endpoint:**

```bash
GET http://localhost:8000/api/progress-reports/1
```

**Expected Response (200):**

```json
{
    "id": 1,
    "enrollment_id": 1,
    "report_date": "2025-11-17",
    "progress_percentage": 45,
    "notes": "Sudah paham konsep async/await dan promises. Minggu depan: advanced patterns dan error handling",
    "attachment_url": "https://example.com/docs/progress-week1.pdf",
    "next_report_date": "2025-12-01",
    "frequency": 14,
    "is_due": false,
    "enrollment": {
        "id": 1,
        "user_id": 5,
        "course_id": 2,
        "progress": 45,
        "completed": false
    },
    "created_at": "2025-11-17T11:00:00Z"
}
```

---

### 5.4 Update Progress Report

**Endpoint:**

```bash
PUT http://localhost:8000/api/progress-reports/1
```

**Request Body:**

```json
{
    "progress_percentage": 50,
    "notes": "Updated: Sudah mengerti advanced patterns juga"
}
```

**Expected Response (200):**

```json
{
    "message": "Progress report updated successfully",
    "data": {
        "id": 1,
        "enrollment_id": 1,
        "progress_percentage": 50,
        "notes": "Updated: Sudah mengerti advanced patterns juga",
        "next_report_date": "2025-12-01",
        "updated_at": "2025-11-17T11:05:00Z"
    }
}
```

---

### 5.5 Get Reports Per Enrollment

**Endpoint:**

```bash
GET http://localhost:8000/api/enrollments/1/progress-reports
```

**Expected Response (200):**

```json
{
    "enrollment_id": 1,
    "data": [
        {
            "id": 1,
            "report_date": "2025-11-17",
            "progress_percentage": 50,
            "notes": "...",
            "frequency": 14,
            "is_due": false
        },
        {
            "id": 2,
            "report_date": "2025-12-01",
            "progress_percentage": 60,
            "notes": "...",
            "frequency": 14,
            "is_due": false
        }
    ]
}
```

---

### 5.6 Set Report Frequency

**Endpoint:**

```bash
POST http://localhost:8000/api/progress-reports/frequency
```

**Request Body:**

```json
{
    "enrollment_id": 1,
    "frequency": 7
}
```

**Expected Response (200):**

```json
{
    "message": "Report frequency updated successfully",
    "data": {
        "enrollment_id": 1,
        "frequency": 7,
        "frequency_label": "Every 7 days"
    }
}
```

---

### 5.7 Get Due/Overdue Reports

**Endpoint:**

```bash
GET http://localhost:8000/api/progress-reports/due
```

**Expected Response (200):**

```json
{
    "data": [
        {
            "id": 2,
            "enrollment_id": 1,
            "report_date": "2025-12-01",
            "progress_percentage": 60,
            "notes": "...",
            "next_report_date": "2025-11-15",
            "frequency": 14,
            "is_due": true,
            "days_overdue": 2
        }
    ],
    "total_due": 1
}
```

---

### 5.8 Delete Progress Report

**Endpoint:**

```bash
DELETE http://localhost:8000/api/progress-reports/1
```

**Expected Response (200):**

```json
{
    "message": "Progress report deleted successfully"
}
```

---

## 🧪 Test Cases Summary

| No  | Endpoint                                                  | Method | Status Code | Notes               |
| --- | --------------------------------------------------------- | ------ | ----------- | ------------------- |
| 1   | /subscriptions                                            | POST   | 201         | Create (single)     |
| 2   | /subscriptions                                            | POST   | 201         | Create (all-in-one) |
| 3   | /subscriptions                                            | GET    | 200         | List all            |
| 4   | /subscriptions/{id}                                       | GET    | 200         | Detail              |
| 5   | /subscriptions/{id}                                       | PUT    | 200         | Update              |
| 6   | /subscriptions/{id}                                       | DELETE | 200         | Delete              |
| 7   | /transactions                                             | POST   | 201         | Create              |
| 8   | /transactions                                             | GET    | 200         | List all            |
| 9   | /transactions/{id}                                        | GET    | 200         | Detail              |
| 10  | /transactions/{id}/upload-proof                           | POST   | 200         | Upload proof        |
| 11  | /transactions/{id}/confirm                                | POST   | 200         | Confirm payment     |
| 12  | /transactions/{id}/refund                                 | POST   | 200         | Process refund      |
| 13  | /mentoring-sessions/{id}/need-assessments                 | POST   | 201         | Submit form         |
| 14  | /mentoring-sessions/{id}/need-assessments                 | GET    | 200         | Get form            |
| 15  | /mentoring-sessions/{id}/need-assessments/mark-completed  | PUT    | 200         | Mark done           |
| 16  | /mentoring-sessions/{id}/need-assessments                 | DELETE | 200         | Delete form         |
| 17  | /mentoring-sessions/{id}/coaching-files                   | POST   | 201         | Upload file         |
| 18  | /mentoring-sessions/{id}/coaching-files                   | GET    | 200         | List files          |
| 19  | /mentoring-sessions/{id}/coaching-files/{fileId}          | GET    | 200         | File detail         |
| 20  | /mentoring-sessions/{id}/coaching-files/{fileId}/download | GET    | 200         | Download            |
| 21  | /mentoring-sessions/{id}/coaching-files/{fileId}          | DELETE | 200         | Delete file         |
| 22  | /mentoring-sessions/{id}/coaching-files                   | DELETE | 200         | Delete all          |
| 23  | /progress-reports                                         | POST   | 201         | Create report       |
| 24  | /progress-reports                                         | GET    | 200         | List all            |
| 25  | /progress-reports/{id}                                    | GET    | 200         | Detail              |
| 26  | /progress-reports/{id}                                    | PUT    | 200         | Update              |
| 27  | /enrollments/{id}/progress-reports                        | GET    | 200         | Per enrollment      |
| 28  | /progress-reports/frequency                               | POST   | 200         | Set frequency       |
| 29  | /progress-reports/due                                     | GET    | 200         | Get due reports     |
| 30  | /progress-reports/{id}                                    | DELETE | 200         | Delete report       |

**Total: 30 test cases**

---

## ❌ Error Testing

### 401 Unauthorized

```bash
GET http://localhost:8000/api/subscriptions
# No Authorization header
# Expected: 401 Unauthorized
```

### 404 Not Found

```bash
GET http://localhost:8000/api/subscriptions/999
# Expected: 404 Not Found
```

### 422 Validation Error

```bash
POST http://localhost:8000/api/subscriptions
# Request: {}
# Expected: 422 Unprocessable Entity
# Response: {"errors": {"package_type": ["field required"]}}
```

### 413 Payload Too Large

```bash
POST http://localhost:8000/api/mentoring-sessions/1/coaching-files
# File size > 50MB
# Expected: 413 Payload Too Large
```

---

## ✅ Checklist Lengkap

-   [ ] Server running on http://localhost:8000
-   [ ] JWT token obtained from /api/auth/login
-   [ ] All 6 subscriptions tests passed
-   [ ] All 6 transaction tests passed
-   [ ] All 4 need assessment tests passed
-   [ ] All 7 coaching files tests passed
-   [ ] All 8 progress report tests passed
-   [ ] Error handling tests passed
-   [ ] Authorization tests passed
-   [ ] Response format validation passed

---

**Last Updated:** 17 November 2025  
**Total Endpoints:** 30 requests  
**Estimated Testing Time:** 1-2 hours
