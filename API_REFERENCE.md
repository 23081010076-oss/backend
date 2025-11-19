# API Reference Guide

## Base URL

```
http://localhost:8000/api
```

## Authentication

All authenticated endpoints require the JWT token in the Authorization header:

```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

---

## Public Endpoints

### Authentication

#### Register User

```http
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"
}
```

**Response (201):**

```json
{
    "message": "User registered successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    }
}
```

#### Login

```http
POST /login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**

```json
{
    "message": "Login successful",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    }
}
```

---

### Courses (Public)

#### List All Courses

```http
GET /courses
```

**Query Parameters:**

-   `page` (integer) - Page number (default: 1)
-   `per_page` (integer) - Items per page (default: 15)
-   `search` (string) - Search courses by title
-   `level` (string) - Filter by level (beginner, intermediate, advanced)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Web Development 101",
            "description": "Learn web development basics",
            "type": "video",
            "level": "beginner",
            "duration": "4 weeks",
            "price": 99.99,
            "access_type": "public",
            "total_videos": 12,
            "created_at": "2025-11-15T10:00:00Z"
        }
    ],
    "pagination": {
        "total": 50,
        "per_page": 15,
        "current_page": 1,
        "last_page": 4
    }
}
```

#### Get Course Details

```http
GET /courses/{id}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "title": "Web Development 101",
        "description": "Learn web development basics",
        "type": "video",
        "level": "beginner",
        "duration": "4 weeks",
        "price": 99.99,
        "access_type": "public",
        "certificate_url": "https://example.com/cert.pdf",
        "video_url": "https://youtube.com/...",
        "video_duration": "180 minutes",
        "total_videos": 12,
        "created_at": "2025-11-15T10:00:00Z"
    }
}
```

---

### Scholarships (Public)

#### List All Scholarships

```http
GET /scholarships
```

**Query Parameters:**

-   `page` (integer)
-   `per_page` (integer)
-   `search` (string)
-   `study_field` (string)
-   `status` (string) - active, closed

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Merit Scholarship 2025",
            "description": "Awarded to top students",
            "study_field": "Technology",
            "funding_amount": 5000.0,
            "deadline": "2025-12-31",
            "status": "active",
            "location": "Jakarta, Indonesia",
            "organization": {
                "id": 1,
                "name": "Tech Foundation"
            }
        }
    ]
}
```

#### Get Scholarship Details

```http
GET /scholarships/{id}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "name": "Merit Scholarship 2025",
        "description": "Awarded to top students",
        "benefit": "Full tuition coverage + allowance",
        "study_field": "Technology",
        "funding_amount": 5000.0,
        "requirements": ["GPA > 3.5", "Valid ID"],
        "deadline": "2025-12-31",
        "status": "active",
        "location": "Jakarta, Indonesia",
        "organization": {
            "id": 1,
            "name": "Tech Foundation",
            "contact_email": "info@techfoundation.org"
        }
    }
}
```

---

### Articles (Public)

#### List Articles

```http
GET /articles
```

**Query Parameters:**

-   `page` (integer)
-   `per_page` (integer)
-   `category` (string)
-   `search` (string)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Getting Started with Laravel",
            "category": "Technology",
            "slug": "getting-started-with-laravel",
            "featured_image": "https://example.com/img.jpg",
            "author": {
                "id": 1,
                "name": "Admin User"
            },
            "published_at": "2025-11-18T10:00:00Z"
        }
    ]
}
```

#### Get Article

```http
GET /articles/{id}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "title": "Getting Started with Laravel",
        "content": "<p>Laravel is a modern PHP framework...</p>",
        "category": "Technology",
        "slug": "getting-started-with-laravel",
        "featured_image": "https://example.com/img.jpg",
        "author": {
            "id": 1,
            "name": "Admin User"
        },
        "published_at": "2025-11-18T10:00:00Z",
        "created_at": "2025-11-18T10:00:00Z"
    }
}
```

---

### Reviews (Public)

#### List Reviews

```http
GET /reviews
```

**Query Parameters:**

-   `page` (integer)
-   `reviewable_type` (string) - Course, Scholarship

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "rating": 5,
            "comment": "Excellent course!",
            "user": {
                "id": 2,
                "name": "Jane Doe"
            },
            "reviewable_type": "Course",
            "created_at": "2025-11-18T10:00:00Z"
        }
    ]
}
```

---

### Corporate Contact (Public)

#### Submit Corporate Inquiry

```http
POST /corporate-contact
Content-Type: application/json

{
  "company_name": "Tech Corp",
  "contact_person_name": "John Manager",
  "email": "john@techcorp.com",
  "phone": "+62812345678",
  "message": "We're interested in partnership",
  "company_size": "100-500",
  "industry": "Technology"
}
```

**Response (201):**

```json
{
    "message": "Corporate inquiry submitted successfully",
    "data": {
        "id": 1,
        "company_name": "Tech Corp",
        "status": "new",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

---

## Authenticated Endpoints

### Authentication & Profile

#### Logout

```http
POST /auth/logout
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "message": "Logout successful"
}
```

#### Get Profile

```http
GET /auth/profile
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "gender": "male",
        "birth_date": "1990-05-15",
        "phone": "+62812345678",
        "address": "Jakarta, Indonesia",
        "institution": "University XYZ",
        "major": "Computer Science",
        "education_level": "bachelor",
        "bio": "Software developer",
        "profile_photo": "https://example.com/photos/1.jpg",
        "created_at": "2025-11-15T10:00:00Z"
    }
}
```

#### Update Profile

```http
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe Updated",
  "bio": "Senior software developer",
  "phone": "+62812345679",
  "institution": "MIT",
  "major": "Computer Science"
}
```

**Response (200):**

```json
{
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        "bio": "Senior software developer",
        "phone": "+62812345679"
    }
}
```

#### Upload Profile Photo

```http
POST /auth/profile/photo
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "photo": <binary file>
}
```

**Response (200):**

```json
{
    "message": "Profile photo uploaded successfully",
    "data": {
        "profile_photo": "https://example.com/photos/1.jpg"
    }
}
```

#### Get Portfolio

```http
GET /auth/portfolio
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": {
        "achievements": [
            {
                "id": 1,
                "title": "Best Student Award",
                "date": "2025-06-15"
            }
        ],
        "experiences": [
            {
                "id": 1,
                "position": "Senior Developer",
                "company": "Tech Corp",
                "start_date": "2023-01-01",
                "end_date": "2025-11-19"
            }
        ],
        "courses_completed": 5,
        "certificates": 3
    }
}
```

#### Get Activity History

```http
GET /auth/activity-history
Authorization: Bearer {token}
```

**Query Parameters:**

-   `limit` (integer) - Default: 50

**Response (200):**

```json
{
    "data": [
        {
            "activity": "enrolled_course",
            "description": "Enrolled in Web Development 101",
            "created_at": "2025-11-19T10:00:00Z"
        },
        {
            "activity": "updated_profile",
            "description": "Updated profile information",
            "created_at": "2025-11-18T15:30:00Z"
        }
    ]
}
```

---

### Courses (Authenticated)

#### Enroll in Course

```http
POST /courses/{courseId}/enroll
Authorization: Bearer {token}
Content-Type: application/json

{}
```

**Response (201):**

```json
{
    "message": "Successfully enrolled in course",
    "data": {
        "id": 1,
        "user_id": 1,
        "course_id": 1,
        "progress": 0,
        "completed": false,
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### Get My Courses

```http
GET /my-courses
Authorization: Bearer {token}
```

**Query Parameters:**

-   `status` (string) - ongoing, completed
-   `page` (integer)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "course": {
                "id": 1,
                "title": "Web Development 101",
                "duration": "4 weeks"
            },
            "progress": 45,
            "completed": false,
            "created_at": "2025-11-15T10:00:00Z"
        }
    ]
}
```

#### Get Enrollment Details

```http
GET /enrollments/{enrollmentId}
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "user_id": 1,
        "course_id": 1,
        "progress": 45,
        "completed": false,
        "certificate_url": null,
        "course": {
            "id": 1,
            "title": "Web Development 101"
        }
    }
}
```

#### Update Enrollment Progress

```http
PUT /enrollments/{enrollmentId}/progress
Authorization: Bearer {token}
Content-Type: application/json

{
  "progress": 75
}
```

**Response (200):**

```json
{
    "message": "Progress updated successfully",
    "data": {
        "id": 1,
        "progress": 75,
        "updated_at": "2025-11-19T10:00:00Z"
    }
}
```

---

### Scholarships (Authenticated)

#### Apply for Scholarship

```http
POST /scholarships/{scholarshipId}/apply
Authorization: Bearer {token}
Content-Type: application/json

{
  "motivation": "I am interested in pursuing technology studies...",
  "supporting_documents": "https://example.com/docs/cv.pdf"
}
```

**Response (201):**

```json
{
    "message": "Application submitted successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "scholarship_id": 1,
        "status": "pending",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### Get My Applications

```http
GET /my-applications
Authorization: Bearer {token}
```

**Query Parameters:**

-   `status` (string) - pending, approved, rejected, awarded
-   `page` (integer)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "scholarship": {
                "id": 1,
                "name": "Merit Scholarship 2025",
                "funding_amount": 5000
            },
            "status": "approved",
            "created_at": "2025-11-15T10:00:00Z"
        }
    ]
}
```

---

### Mentoring Sessions

#### Create Mentoring Session

```http
POST /mentoring-sessions
Authorization: Bearer {token}
Content-Type: application/json

{
  "mentor_id": 2,
  "type": "technical",
  "schedule": "2025-11-25 14:00:00",
  "meeting_link": "https://zoom.us/j/123456",
  "payment_method": "credit_card"
}
```

**Response (201):**

```json
{
    "message": "Mentoring session created",
    "data": {
        "id": 1,
        "mentor_id": 2,
        "member_id": 1,
        "type": "technical",
        "schedule": "2025-11-25T14:00:00Z",
        "status": "scheduled",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### Get My Mentoring Sessions

```http
GET /my-mentoring-sessions
Authorization: Bearer {token}
```

**Query Parameters:**

-   `status` (string) - scheduled, completed, cancelled
-   `page` (integer)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "type": "technical",
            "mentor": {
                "id": 2,
                "name": "Dr. Expert"
            },
            "schedule": "2025-11-25T14:00:00Z",
            "status": "scheduled",
            "meeting_link": "https://zoom.us/j/123456"
        }
    ]
}
```

#### Update Mentoring Session Status

```http
PUT /mentoring-sessions/{sessionId}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "completed"
}
```

**Response (200):**

```json
{
    "message": "Session status updated",
    "data": {
        "id": 1,
        "status": "completed",
        "updated_at": "2025-11-19T10:00:00Z"
    }
}
```

---

### Need Assessment

#### Submit Need Assessment

```http
POST /mentoring-sessions/{sessionId}/need-assessments
Authorization: Bearer {token}
Content-Type: application/json

{
  "goals": "Improve software architecture skills",
  "current_skills": "Basic programming knowledge",
  "challenges": "Understanding design patterns"
}
```

**Response (201):**

```json
{
    "message": "Assessment submitted",
    "data": {
        "id": 1,
        "mentoring_session_id": 1,
        "assessment_form_data": {
            "goals": "Improve software architecture skills"
        }
    }
}
```

#### Get Need Assessment

```http
GET /mentoring-sessions/{sessionId}/need-assessments
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "mentoring_session_id": 1,
        "assessment_form_data": {
            "goals": "Improve software architecture skills",
            "current_skills": "Basic programming knowledge"
        }
    }
}
```

#### Mark Assessment as Completed

```http
PUT /mentoring-sessions/{sessionId}/need-assessments/mark-completed
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "message": "Assessment marked as completed"
}
```

---

### Coaching Files

#### Upload Coaching File

```http
POST /mentoring-sessions/{sessionId}/coaching-files
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "file": <binary file>,
  "description": "Design pattern examples"
}
```

**Response (201):**

```json
{
    "message": "File uploaded successfully",
    "data": {
        "id": 1,
        "mentoring_session_id": 1,
        "file_name": "design_patterns.pdf",
        "description": "Design pattern examples",
        "file_url": "https://example.com/files/coaching/1.pdf",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### List Coaching Files

```http
GET /mentoring-sessions/{sessionId}/coaching-files
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "file_name": "design_patterns.pdf",
            "description": "Design pattern examples",
            "file_url": "https://example.com/files/coaching/1.pdf",
            "created_at": "2025-11-19T10:00:00Z"
        }
    ]
}
```

#### Download Coaching File

```http
GET /mentoring-sessions/{sessionId}/coaching-files/{fileId}/download
Authorization: Bearer {token}
```

**Response:** File download (binary)

#### Delete Coaching File

```http
DELETE /mentoring-sessions/{sessionId}/coaching-files/{fileId}
Authorization: Bearer {token}
```

**Response (204):**
No content

---

### Achievements

#### Create Achievement

```http
POST /achievements
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Laravel Certification",
  "description": "Completed Laravel developer certification",
  "date": "2025-11-15",
  "certificate_url": "https://example.com/cert.pdf"
}
```

**Response (201):**

```json
{
    "message": "Achievement created",
    "data": {
        "id": 1,
        "title": "Laravel Certification",
        "date": "2025-11-15",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### Get Achievements

```http
GET /achievements
Authorization: Bearer {token}
```

**Query Parameters:**

-   `page` (integer)
-   `sort` (string) - date, title

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Laravel Certification",
            "description": "Completed Laravel developer certification",
            "date": "2025-11-15"
        }
    ]
}
```

---

### Experiences

#### Add Experience

```http
POST /experiences
Authorization: Bearer {token}
Content-Type: application/json

{
  "position": "Senior Developer",
  "company": "Tech Corp",
  "start_date": "2023-01-01",
  "end_date": "2025-11-19",
  "description": "Led development of web applications",
  "type": "professional"
}
```

**Response (201):**

```json
{
    "message": "Experience added",
    "data": {
        "id": 1,
        "position": "Senior Developer",
        "company": "Tech Corp"
    }
}
```

#### Get Experiences

```http
GET /experiences
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "position": "Senior Developer",
            "company": "Tech Corp",
            "start_date": "2023-01-01",
            "end_date": "2025-11-19"
        }
    ]
}
```

---

### Subscriptions

#### Create Subscription

```http
POST /subscriptions
Authorization: Bearer {token}
Content-Type: application/json

{
  "plan": "premium",
  "package_type": "annual",
  "duration": 12,
  "duration_unit": "months",
  "courses_ids": [1, 2, 3]
}
```

**Response (201):**

```json
{
    "message": "Subscription created",
    "data": {
        "id": 1,
        "plan": "premium",
        "status": "active",
        "start_date": "2025-11-19",
        "end_date": "2026-11-19"
    }
}
```

#### Get My Subscriptions

```http
GET /subscriptions
Authorization: Bearer {token}
```

**Query Parameters:**

-   `status` (string) - active, inactive, expired

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "plan": "premium",
            "status": "active",
            "start_date": "2025-11-19",
            "end_date": "2026-11-19",
            "courses_count": 3
        }
    ]
}
```

#### Upgrade Subscription

```http
POST /subscriptions/{subscriptionId}/upgrade
Authorization: Bearer {token}
Content-Type: application/json

{
  "plan": "premium_plus"
}
```

**Response (200):**

```json
{
    "message": "Subscription upgraded",
    "data": {
        "id": 1,
        "plan": "premium_plus"
    }
}
```

---

### Reviews

#### Create Review

```http
POST /reviews
Authorization: Bearer {token}
Content-Type: application/json

{
  "reviewable_id": 1,
  "reviewable_type": "Course",
  "rating": 5,
  "comment": "Excellent course with great content!"
}
```

**Response (201):**

```json
{
    "message": "Review created",
    "data": {
        "id": 1,
        "rating": 5,
        "comment": "Excellent course with great content!"
    }
}
```

---

## Admin Endpoints

### User Management (Admin Only)

#### List All Users

```http
GET /admin/users
Authorization: Bearer {admin-token}
```

**Query Parameters:**

-   `role` (string) - user, admin, corporate
-   `search` (string)
-   `page` (integer)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "created_at": "2025-11-15T10:00:00Z"
        }
    ]
}
```

#### Create User (Admin)

```http
POST /admin/users
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "name": "Jane Admin",
  "email": "jane@example.com",
  "password": "password123",
  "role": "admin"
}
```

---

### Course Management (Admin Only)

#### Create Course

```http
POST /courses
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "title": "Advanced JavaScript",
  "description": "Master advanced JavaScript concepts",
  "type": "video",
  "level": "advanced",
  "duration": "6 weeks",
  "price": 199.99,
  "total_videos": 24
}
```

**Response (201):**

```json
{
    "message": "Course created successfully",
    "data": {
        "id": 2,
        "title": "Advanced JavaScript",
        "created_at": "2025-11-19T10:00:00Z"
    }
}
```

#### Update Course

```http
PUT /courses/{courseId}
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "title": "Advanced JavaScript - Updated",
  "price": 189.99
}
```

**Response (200):**

```json
{
    "message": "Course updated successfully",
    "data": {
        "id": 2,
        "title": "Advanced JavaScript - Updated"
    }
}
```

#### Delete Course

```http
DELETE /courses/{courseId}
Authorization: Bearer {admin-token}
```

**Response (204):**
No content

---

### Scholarship Management (Admin Only)

#### Create Scholarship

```http
POST /scholarships
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "organization_id": 1,
  "name": "Tech Excellence Award",
  "description": "For outstanding tech students",
  "benefit": "Full tuition coverage",
  "funding_amount": 10000,
  "deadline": "2025-12-31",
  "study_field": "Technology"
}
```

**Response (201):**

```json
{
    "message": "Scholarship created",
    "data": {
        "id": 2,
        "name": "Tech Excellence Award"
    }
}
```

#### Update Scholarship Application Status

```http
PUT /scholarship-applications/{applicationId}/status
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "status": "approved"
}
```

**Response (200):**

```json
{
    "message": "Application status updated",
    "data": {
        "id": 1,
        "status": "approved"
    }
}
```

---

### Corporate Contact Management

#### List Corporate Contacts

```http
GET /corporate-contacts
Authorization: Bearer {admin-token}
```

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "company_name": "Tech Corp",
            "contact_person_name": "John Manager",
            "email": "john@techcorp.com",
            "status": "new"
        }
    ]
}
```

#### Update Corporate Contact Status

```http
PUT /corporate-contacts/{contactId}/status
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "status": "contacted"
}
```

**Response (200):**

```json
{
    "message": "Status updated",
    "data": {
        "id": 1,
        "status": "contacted"
    }
}
```

---

## Error Handling

### Common Error Responses

#### Validation Error (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

#### Unauthorized (401)

```json
{
    "message": "Unauthenticated."
}
```

#### Forbidden (403)

```json
{
    "message": "This action is unauthorized."
}
```

#### Not Found (404)

```json
{
    "message": "Resource not found."
}
```

#### Server Error (500)

```json
{
    "message": "Server error occurred.",
    "error": "details"
}
```

---

## Pagination

All list endpoints support pagination:

```json
{
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://localhost:8000/api/courses?page=1",
    "last": "http://localhost:8000/api/courses?page=7",
    "next": "http://localhost:8000/api/courses?page=2",
    "prev": null
  }
}
```

---

**Last Updated:** November 19, 2025
**API Version:** v1
