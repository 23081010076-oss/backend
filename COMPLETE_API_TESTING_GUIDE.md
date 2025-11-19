# 🧪 Complete API Testing Guide - All Features

**Learning Platform Backend**  
**Base URL:** `http://localhost:8000/api`

---

## 📋 Quick Reference - All Endpoints

### 1️⃣ AUTHENTICATION

```bash
# Register
POST /register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "student"
}

# Login
POST /login
{
  "email": "john@example.com",
  "password": "password123"
}
Response: { "data": { "access_token": "..." } }

# Logout (Protected)
POST /auth/logout
Headers: Authorization: Bearer {token}
```

---

### 2️⃣ USER MANAGEMENT

```bash
# Get Profile
GET /auth/profile
Headers: Authorization: Bearer {token}

# Update Profile
PUT /auth/profile
Headers: Authorization: Bearer {token}
{
  "name": "Jane Doe",
  "phone": "08123456789",
  "institution": "Universitas ABC",
  "major": "Computer Science",
  "education_level": "S1"
}

# Upload Photo
POST /auth/profile/photo
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data
Form: profile_photo={file}

# Get Portfolio
GET /auth/portfolio
Headers: Authorization: Bearer {token}

# Get Activity History
GET /auth/activity-history
Headers: Authorization: Bearer {token}

# Admin: Manage Users
GET    /admin/users              (List users)
POST   /admin/users              (Create user)
GET    /admin/users/{id}         (Detail user)
PUT    /admin/users/{id}         (Update user)
DELETE /admin/users/{id}         (Delete user)
Headers: Authorization: Bearer {admin_token}
Middleware: role:admin
```

---

### 3️⃣ COURSES & E-LEARNING

```bash
# List Courses (Public)
GET /courses?page=1&level=beginner&access_type=premium&search=Laravel

# Get Course Detail (Public)
GET /courses/{id}

# Admin: Create Course
POST /courses
Headers: Authorization: Bearer {admin_token}
Content-Type: multipart/form-data
{
  "title": "Laravel Masterclass",
  "description": "Complete Laravel course",
  "type": "course",              (bootcamp/course)
  "level": "intermediate",        (beginner/intermediate/advanced)
  "duration": "6 weeks",
  "price": 99.99,
  "access_type": "premium",      (free/regular/premium)
  "video_file": @video.mp4,
  "video_duration": "02:30:00"
}

# Admin: Update Course
PUT /courses/{id}
Headers: Authorization: Bearer {admin_token}
{
  "title": "Advanced Laravel",
  "price": 149.99
}

# Admin: Delete Course
DELETE /courses/{id}
Headers: Authorization: Bearer {admin_token}

# User: Enroll Course
POST /courses/{id}/enroll
Headers: Authorization: Bearer {user_token}

# Get My Courses
GET /my-courses
Headers: Authorization: Bearer {user_token}

# List Enrollments
GET /enrollments
Headers: Authorization: Bearer {user_token}

# Get Enrollment Detail
GET /enrollments/{id}
Headers: Authorization: Bearer {user_token}

# Update Progress
PUT /enrollments/{id}/progress
Headers: Authorization: Bearer {user_token}
{
  "progress": 75,
  "completed": false
}
```

---

### 4️⃣ SUBSCRIPTION & PACKAGES

```bash
# List Subscriptions
GET /subscriptions
Headers: Authorization: Bearer {user_token}

# Create Subscription
POST /subscriptions
Headers: Authorization: Bearer {user_token}
{
  "plan": "premium",             (regular/premium)
  "duration": 12,
  "duration_unit": "months",     (weeks/months/years)
  "package_type": "all_in_one",  (single_course/all_in_one)
  "courses_ids": [1, 2, 3]
}

# Get Subscription
GET /subscriptions/{id}
Headers: Authorization: Bearer {user_token}

# Upgrade Subscription
POST /subscriptions/{id}/upgrade
Headers: Authorization: Bearer {user_token}
{
  "plan": "premium"
}

# Delete Subscription
DELETE /subscriptions/{id}
Headers: Authorization: Bearer {user_token}
```

---

### 5️⃣ SCHOLARSHIPS

```bash
# List Scholarships (Public)
GET /scholarships?page=1&study_field=IT&location=Jakarta

# Get Scholarship Detail (Public)
GET /scholarships/{id}

# Admin/Corporate: Create Scholarship
POST /scholarships
Headers: Authorization: Bearer {admin_token}
{
  "organization_id": 1,
  "name": "Full Scholarship",
  "description": "Full funding for deserving students",
  "benefit": "Full tuition + monthly stipend",
  "location": "Jakarta",
  "study_field": "Information Technology",
  "status": "active",            (active/closed/coming_soon)
  "deadline": "2025-12-31",
  "funding_amount": 50000000,
  "requirements": ["GPA >= 3.5", "TOEFL >= 500"]
}

# Admin/Corporate: Update Scholarship
PUT /scholarships/{id}
Headers: Authorization: Bearer {admin_token}
{
  "name": "Updated Scholarship",
  "deadline": "2025-12-31"
}

# Admin/Corporate: Delete Scholarship
DELETE /scholarships/{id}
Headers: Authorization: Bearer {admin_token}

# User: Apply Scholarship
POST /scholarships/{id}/apply
Headers: Authorization: Bearer {user_token}
Content-Type: multipart/form-data
{
  "cv": @cv.pdf,
  "transcript": @transcript.pdf,
  "recommendation_letter": @letter.pdf,
  "motivation_letter": "Why I deserve this scholarship..."
}

# Get My Applications
GET /my-applications
Headers: Authorization: Bearer {user_token}

# Admin/Corporate: View Applications
GET /scholarship-applications
Headers: Authorization: Bearer {admin_token}

# Admin/Corporate: Update Application Status
PUT /scholarship-applications/{id}/status
Headers: Authorization: Bearer {admin_token}
{
  "status": "accepted"           (submitted/review/accepted/rejected)
}

# List Reviews (Public)
GET /reviews

# Create Review
POST /reviews
Headers: Authorization: Bearer {user_token}
{
  "reviewable_id": 1,
  "reviewable_type": "App\\Models\\Scholarship",
  "rating": 5,
  "comment": "Excellent scholarship program!"
}

# Update Review
PUT /reviews/{id}
Headers: Authorization: Bearer {user_token}
{
  "rating": 4,
  "comment": "Updated review"
}

# Delete Review
DELETE /reviews/{id}
Headers: Authorization: Bearer {user_token}
```

---

### 6️⃣ MENTORING SESSIONS

```bash
# List Mentoring Sessions
GET /mentoring-sessions
Headers: Authorization: Bearer {user_token}

# Create Mentoring Session
POST /mentoring-sessions
Headers: Authorization: Bearer {user_token}
{
  "mentor_id": 5,
  "type": "academic",            (academic/life_plan)
  "schedule": "2025-12-01 14:00:00"
}

# Get Session Detail
GET /mentoring-sessions/{id}
Headers: Authorization: Bearer {user_token}

# Update Session
PUT /mentoring-sessions/{id}
Headers: Authorization: Bearer {user_token}
{
  "schedule": "2025-12-02 15:00:00",
  "meeting_link": "https://zoom.us/..."
}

# Delete Session
DELETE /mentoring-sessions/{id}
Headers: Authorization: Bearer {user_token}

# Schedule Session
POST /mentoring-sessions/{id}/schedule
Headers: Authorization: Bearer {user_token}
{
  "schedule": "2025-12-01 10:00:00",
  "meeting_link": "https://meet.google.com/..."
}

# Update Session Status
PUT /mentoring-sessions/{id}/status
Headers: Authorization: Bearer {user_token}
{
  "status": "completed"          (pending/completed/refunded/scheduled)
}

# Get My Mentoring Sessions
GET /my-mentoring-sessions
Headers: Authorization: Bearer {user_token}

# NEED ASSESSMENT (Pre-Mentoring)
# Get Assessment
GET /mentoring-sessions/{sessionId}/need-assessments
Headers: Authorization: Bearer {user_token}

# Create Assessment
POST /mentoring-sessions/{sessionId}/need-assessments
Headers: Authorization: Bearer {user_token}
{
  "form_data": {
    "current_situation": "...",
    "goals": "...",
    "challenges": "...",
    "preferred_topics": "..."
  }
}

# Mark Assessment Completed
PUT /mentoring-sessions/{sessionId}/need-assessments/mark-completed
Headers: Authorization: Bearer {user_token}

# Delete Assessment
DELETE /mentoring-sessions/{sessionId}/need-assessments
Headers: Authorization: Bearer {user_token}

# COACHING FILES
# List Files
GET /mentoring-sessions/{sessionId}/coaching-files
Headers: Authorization: Bearer {user_token}

# Upload File
POST /mentoring-sessions/{sessionId}/coaching-files
Headers: Authorization: Bearer {user_token}
Content-Type: multipart/form-data
{
  "file": @coaching_material.pdf
}

# Get File
GET /mentoring-sessions/{sessionId}/coaching-files/{fileId}
Headers: Authorization: Bearer {user_token}

# Download File
GET /mentoring-sessions/{sessionId}/coaching-files/{fileId}/download
Headers: Authorization: Bearer {user_token}

# Delete File
DELETE /mentoring-sessions/{sessionId}/coaching-files/{fileId}
Headers: Authorization: Bearer {user_token}

# Delete All Files
DELETE /mentoring-sessions/{sessionId}/coaching-files
Headers: Authorization: Bearer {user_token}
```

---

### 7️⃣ TRANSACTIONS & PAYMENTS

```bash
# List My Transactions
GET /transactions?page=1&type=course_enrollment&status=pending
Headers: Authorization: Bearer {user_token}

# Get Transaction Detail
GET /transactions/{id}
Headers: Authorization: Bearer {user_token}

# Create Course Payment
POST /transactions/courses/{courseId}
Headers: Authorization: Bearer {user_token}
{
  "payment_method": "qris"       (qris/bank_transfer/virtual_account/credit_card/manual)
}

# Create Subscription Payment
POST /transactions/subscriptions
Headers: Authorization: Bearer {user_token}
{
  "plan": "premium",
  "payment_method": "bank_transfer"
}

# Create Mentoring Payment
POST /transactions/mentoring-sessions/{sessionId}
Headers: Authorization: Bearer {user_token}
{
  "payment_method": "virtual_account"
}

# Upload Payment Proof (Manual Payment)
POST /transactions/{id}/payment-proof
Headers: Authorization: Bearer {user_token}
Content-Type: multipart/form-data
{
  "payment_proof": @proof.pdf
}

# Request Refund
POST /transactions/{id}/refund
Headers: Authorization: Bearer {user_token}
{
  "reason": "Course tidak sesuai"
}

# Admin: Confirm Payment
POST /transactions/{id}/confirm
Headers: Authorization: Bearer {admin_token}

# Admin: Get Statistics
GET /transactions/statistics
Headers: Authorization: Bearer {admin_token}
```

---

### 8️⃣ ARTICLES & CONTENT

```bash
# List Articles (Public)
GET /articles?page=1&category=edukasi

# Get Article (Public)
GET /articles/{id}

# Admin/Corporate: Create Article
POST /articles
Headers: Authorization: Bearer {user_token}
{
  "title": "Tips Lolos Beasiswa",
  "content": "Berikut tips-tips untuk lolos beasiswa...",
  "category": "beasiswa",        (edukasi/karier/beasiswa/testimoni/etc)
  "featured_image": @image.jpg,
  "status": "published"          (draft/published)
}

# Admin/Corporate: Update Article
PUT /articles/{id}
Headers: Authorization: Bearer {user_token}
{
  "title": "Updated Title",
  "status": "published"
}

# Admin/Corporate: Delete Article
DELETE /articles/{id}
Headers: Authorization: Bearer {user_token}
```

---

### 9️⃣ CORPORATE SERVICES

```bash
# Public: Submit Contact Form
POST /corporate-contact
{
  "name": "PT ABC",
  "email": "contact@ptabc.com",
  "phone": "02123456789",
  "company": "PT ABC Indonesia",
  "message": "Kami ingin berkolaborasi..."
}

# Admin: List Contacts
GET /corporate-contacts
Headers: Authorization: Bearer {admin_token}

# Admin: Get Contact Detail
GET /corporate-contacts/{id}
Headers: Authorization: Bearer {admin_token}

# Admin: Update Status
PUT /corporate-contacts/{id}/status
Headers: Authorization: Bearer {admin_token}
{
  "status": "responded"          (submitted/viewed/responded)
}

# Admin: Delete Contact
DELETE /corporate-contacts/{id}
Headers: Authorization: Bearer {admin_token}
```

---

### 🔟 ORGANIZATIONS

```bash
# List Organizations
GET /organizations
Headers: Authorization: Bearer {user_token}

# Create Organization
POST /organizations
Headers: Authorization: Bearer {user_token}
{
  "name": "Universitas ABC",
  "type": "university",
  "description": "Leading university in Indonesia",
  "location": "Jakarta",
  "website": "https://abc.edu",
  "contact_email": "contact@abc.edu",
  "phone": "021-123456",
  "founded_year": 2000,
  "logo_url": "https://..."
}

# Get Organization
GET /organizations/{id}
Headers: Authorization: Bearer {user_token}

# Update Organization
PUT /organizations/{id}
Headers: Authorization: Bearer {user_token}
{
  "description": "Updated description"
}

# Delete Organization
DELETE /organizations/{id}
Headers: Authorization: Bearer {user_token}
```

---

### 1️⃣1️⃣ ACHIEVEMENTS

```bash
# List Achievements
GET /achievements
Headers: Authorization: Bearer {user_token}

# Create Achievement
POST /achievements
Headers: Authorization: Bearer {user_token}
{
  "title": "Best Student Award",
  "description": "Received best student award",
  "date": "2025-11-15",
  "issuer": "Universitas ABC",
  "certificate_url": "https://..."
}

# Get Achievement
GET /achievements/{id}
Headers: Authorization: Bearer {user_token}

# Update Achievement
PUT /achievements/{id}
Headers: Authorization: Bearer {user_token}
{
  "title": "Updated Title"
}

# Delete Achievement
DELETE /achievements/{id}
Headers: Authorization: Bearer {user_token}
```

---

### 1️⃣2️⃣ EXPERIENCES

```bash
# List Experiences
GET /experiences
Headers: Authorization: Bearer {user_token}

# Create Experience
POST /experiences
Headers: Authorization: Bearer {user_token}
{
  "title": "Software Developer",
  "description": "Developed web applications",
  "organization": "PT XYZ",
  "position": "Junior Developer",
  "start_date": "2024-01-01",
  "end_date": "2025-11-15",
  "is_current": false
}

# Get Experience
GET /experiences/{id}
Headers: Authorization: Bearer {user_token}

# Update Experience
PUT /experiences/{id}
Headers: Authorization: Bearer {user_token}
{
  "is_current": true,
  "end_date": null
}

# Delete Experience
DELETE /experiences/{id}
Headers: Authorization: Bearer {user_token}
```

---

## 🔑 Important Notes

### Headers Required:

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  (untuk protected routes)
```

### Status Codes:

```
200 OK              - Success
201 Created         - Resource created
400 Bad Request     - Invalid data
401 Unauthorized    - Token missing/invalid
403 Forbidden       - Permission denied
404 Not Found       - Resource not found
422 Unprocessable   - Validation failed
500 Server Error    - Internal error
```

### Pagination:

```
GET /courses?page=1&per_page=15
Response includes:
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### Filtering Examples:

```
# Courses
GET /courses?level=beginner&access_type=premium&search=Laravel

# Scholarships
GET /scholarships?study_field=IT&location=Jakarta&status=active

# Transactions
GET /transactions?type=course_enrollment&status=pending

# Mentoring
GET /mentoring-sessions?type=academic
```

### Error Response:

```json
{
    "message": "Error message",
    "errors": {
        "field": ["Error detail"]
    }
}
```

---

## 🧪 Testing Workflow

### Step 1: Register & Login

```bash
1. POST /register → Create account
2. POST /login → Get access_token
3. Copy token untuk next requests
```

### Step 2: Setup Profile

```bash
1. PUT /auth/profile → Update biodata
2. POST /auth/profile/photo → Upload foto
```

### Step 3: Browse & Enroll

```bash
1. GET /courses → List courses
2. GET /courses/{id} → View details
3. POST /courses/{id}/enroll → Enroll
4. POST /transactions/courses/{id} → Create payment
```

### Step 4: Mentoring

```bash
1. GET /mentoring-sessions → List mentors
2. POST /mentoring-sessions → Book session
3. POST /mentoring-sessions/{id}/need-assessments → Fill assessment
4. GET /mentoring-sessions/{id}/coaching-files → Access materials
```

### Step 5: Scholarship

```bash
1. GET /scholarships → List scholarships
2. GET /scholarships/{id} → View details
3. POST /scholarships/{id}/apply → Apply
4. POST /reviews → Give review
```

---

## 📦 Complete Feature Checklist

-   [x] User Registration & Login
-   [x] Profile Management
-   [x] Courses & E-Learning
-   [x] Enrollment & Progress
-   [x] Subscriptions
-   [x] Scholarships & Applications
-   [x] Mentoring Sessions
-   [x] Need Assessment
-   [x] Coaching Files
-   [x] Transactions & Payments
-   [x] Articles & Content
-   [x] Corporate Services
-   [x] Organizations
-   [x] Achievements
-   [x] Experiences
-   [x] Reviews & Ratings
-   [x] Role-based Access
-   [x] Admin Controls

---

**Last Updated:** November 19, 2025  
**All Features:** 41/41 ✅  
**Ready to Test!** 🚀
