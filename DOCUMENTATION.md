# Platform Documentation

## Table of Contents

1. [Project Overview](#project-overview)
2. [Authentication & Authorization](#authentication--authorization)
3. [Core Features](#core-features)
4. [API Endpoints](#api-endpoints)
5. [Database Models](#database-models)
6. [User Roles](#user-roles)

---

## Project Overview

This is a Laravel 12 API-based educational platform that provides comprehensive features for:

-   Online course management and enrollment
-   Scholarship administration and applications
-   Mentoring session scheduling
-   User profiling and experience management
-   Corporate partnerships
-   Article/Blog management
-   Transaction and subscription management

**Tech Stack:**

-   **Framework:** Laravel 12
-   **Authentication:** JWT (Tymon JWT-Auth v2.2)
-   **Database:** SQLite/MySQL
-   **Build Tools:** Vite, NPM
-   **Testing:** PHPUnit
-   **Documentation:** REST API

---

## Authentication & Authorization

### JWT Authentication

The platform uses JWT (JSON Web Tokens) for authentication via the `tymon/jwt-auth` package.

#### Authentication Endpoints

| Method | Endpoint                     | Access        | Description                 |
| ------ | ---------------------------- | ------------- | --------------------------- |
| POST   | `/api/register`              | Public        | Register new user account   |
| POST   | `/api/login`                 | Public        | Login and receive JWT token |
| POST   | `/api/auth/logout`           | Authenticated | Logout and invalidate token |
| GET    | `/api/auth/profile`          | Authenticated | Get current user profile    |
| PUT    | `/api/auth/profile`          | Authenticated | Update user profile         |
| POST   | `/api/auth/profile/photo`    | Authenticated | Upload profile photo        |
| GET    | `/api/auth/portfolio`        | Authenticated | Get user portfolio          |
| GET    | `/api/auth/activity-history` | Authenticated | Get user activity history   |

#### Roles & Permissions

The system supports role-based access control:

-   **user**: Standard user/student
-   **admin**: Platform administrator (full access)
-   **corporate**: Corporate partner (can create scholarships and articles)

---

## Core Features

### 1. **User Management**

Users can manage their profiles with personal and professional information.

#### User Profile Fields

-   `name` - Full name
-   `email` - Email address
-   `password` - Encrypted password
-   `role` - User role (user, admin, corporate)
-   `gender` - Gender
-   `birth_date` - Date of birth
-   `phone` - Phone number
-   `address` - Physical address
-   `institution` - Educational institution
-   `major` - Area of study
-   `education_level` - Education level (high school, bachelor, master, etc.)
-   `bio` - User biography
-   `profile_photo` - Profile picture URL

#### Admin User Management

Admins can create, update, and delete user accounts.

| Method | Endpoint                | Access | Description      |
| ------ | ----------------------- | ------ | ---------------- |
| GET    | `/api/admin/users`      | Admin  | List all users   |
| POST   | `/api/admin/users`      | Admin  | Create new user  |
| GET    | `/api/admin/users/{id}` | Admin  | Get user details |
| PUT    | `/api/admin/users/{id}` | Admin  | Update user      |
| DELETE | `/api/admin/users/{id}` | Admin  | Delete user      |

---

### 2. **Courses & Enrollment**

The platform manages online courses and student enrollments with progress tracking.

#### Course Management

Courses contain the following information:

-   `title` - Course title
-   `description` - Course description
-   `type` - Course type
-   `level` - Difficulty level (beginner, intermediate, advanced)
-   `duration` - Course duration
-   `price` - Course price (decimal)
-   `access_type` - Type of access
-   `certificate_url` - Certificate URL upon completion
-   `video_url` - Video content URL
-   `video_duration` - Total video duration
-   `total_videos` - Number of videos

#### Enrollment Information

-   `progress` - Student progress (0-100%)
-   `completed` - Whether course is completed
-   `certificate_url` - Certificate URL if completed

#### Course Endpoints

| Method | Endpoint                         | Access        | Description                |
| ------ | -------------------------------- | ------------- | -------------------------- |
| GET    | `/api/courses`                   | Public        | List all courses           |
| GET    | `/api/courses/{id}`              | Public        | Get course details         |
| POST   | `/api/courses`                   | Admin         | Create new course          |
| PUT    | `/api/courses/{id}`              | Admin         | Update course              |
| DELETE | `/api/courses/{id}`              | Admin         | Delete course              |
| POST   | `/api/courses/{id}/enroll`       | Authenticated | Enroll in course           |
| GET    | `/api/my-courses`                | Authenticated | Get enrolled courses       |
| GET    | `/api/enrollments`               | Authenticated | List enrollments           |
| PUT    | `/api/enrollments/{id}/progress` | Authenticated | Update enrollment progress |

---

### 3. **Scholarships**

Manage and apply for scholarships through the platform.

#### Scholarship Information

-   `organization_id` - Offering organization
-   `name` - Scholarship name
-   `description` - Detailed description
-   `benefit` - Scholarship benefits
-   `location` - Geographic location
-   `status` - Scholarship status (active, closed, etc.)
-   `deadline` - Application deadline
-   `study_field` - Field of study
-   `funding_amount` - Grant amount (decimal)
-   `requirements` - Application requirements

#### Scholarship Application Status

-   `pending` - Awaiting review
-   `approved` - Application approved
-   `rejected` - Application rejected
-   `awarded` - Scholarship awarded
-   `completed` - Scholarship completed

#### Scholarship Endpoints

| Method | Endpoint                                    | Access          | Description               |
| ------ | ------------------------------------------- | --------------- | ------------------------- |
| GET    | `/api/scholarships`                         | Public          | List all scholarships     |
| GET    | `/api/scholarships/{id}`                    | Public          | Get scholarship details   |
| POST   | `/api/scholarships`                         | Admin/Corporate | Create scholarship        |
| PUT    | `/api/scholarships/{id}`                    | Admin/Corporate | Update scholarship        |
| DELETE | `/api/scholarships/{id}`                    | Admin/Corporate | Delete scholarship        |
| POST   | `/api/scholarships/{id}/apply`              | Authenticated   | Apply for scholarship     |
| GET    | `/api/my-applications`                      | Authenticated   | Get my applications       |
| PUT    | `/api/scholarship-applications/{id}/status` | Admin/Corporate | Update application status |

---

### 4. **Mentoring Sessions**

Connect mentors with mentees for personalized coaching and guidance.

#### Mentoring Session Details

-   `mentor_id` - Mentor user ID
-   `member_id` - Student/mentee user ID
-   `session_id` - Unique session identifier
-   `type` - Session type
-   `schedule` - Scheduled datetime
-   `meeting_link` - Video call link (Zoom, Google Meet, etc.)
-   `payment_method` - Payment method used
-   `status` - Session status (scheduled, completed, cancelled)
-   `need_assessment_status` - Assessment completion status
-   `assessment_form_data` - Assessment form responses
-   `coaching_files_path` - Path to coaching materials

#### Session Statuses

-   `scheduled` - Waiting to begin
-   `in_progress` - Currently running
-   `completed` - Session finished
-   `cancelled` - Session cancelled
-   `rescheduled` - Session rescheduled

#### Mentoring Endpoints

| Method | Endpoint                                | Access        | Description              |
| ------ | --------------------------------------- | ------------- | ------------------------ |
| GET    | `/api/mentoring-sessions`               | Authenticated | List mentoring sessions  |
| POST   | `/api/mentoring-sessions`               | Authenticated | Create mentoring session |
| GET    | `/api/mentoring-sessions/{id}`          | Authenticated | Get session details      |
| PUT    | `/api/mentoring-sessions/{id}`          | Authenticated | Update session           |
| DELETE | `/api/mentoring-sessions/{id}`          | Authenticated | Delete session           |
| POST   | `/api/mentoring-sessions/{id}/schedule` | Authenticated | Schedule session         |
| PUT    | `/api/mentoring-sessions/{id}/status`   | Authenticated | Update session status    |
| GET    | `/api/my-mentoring-sessions`            | Authenticated | Get my sessions          |

---

### 5. **Need Assessment**

Part of mentoring sessions, used to evaluate student needs and goals.

#### Assessment Features

-   Needs evaluation form
-   Form completion tracking
-   Assessment data storage
-   Mark as completed functionality

#### Need Assessment Endpoints

| Method | Endpoint                                                       | Access        | Description       |
| ------ | -------------------------------------------------------------- | ------------- | ----------------- |
| GET    | `/api/mentoring-sessions/{id}/need-assessments`                | Authenticated | View assessment   |
| POST   | `/api/mentoring-sessions/{id}/need-assessments`                | Authenticated | Create assessment |
| PUT    | `/api/mentoring-sessions/{id}/need-assessments/mark-completed` | Authenticated | Mark complete     |
| DELETE | `/api/mentoring-sessions/{id}/need-assessments`                | Authenticated | Delete assessment |

---

### 6. **Coaching Files**

Share coaching materials, resources, and documents with mentees.

#### File Management

-   Multiple file uploads
-   File download functionality
-   Organized by mentoring session
-   Complete file management

#### Coaching Files Endpoints

| Method | Endpoint                                                        | Access        | Description      |
| ------ | --------------------------------------------------------------- | ------------- | ---------------- |
| GET    | `/api/mentoring-sessions/{id}/coaching-files`                   | Authenticated | List files       |
| POST   | `/api/mentoring-sessions/{id}/coaching-files`                   | Authenticated | Upload file      |
| GET    | `/api/mentoring-sessions/{id}/coaching-files/{fileId}`          | Authenticated | Get file         |
| GET    | `/api/mentoring-sessions/{id}/coaching-files/{fileId}/download` | Authenticated | Download file    |
| DELETE | `/api/mentoring-sessions/{id}/coaching-files/{fileId}`          | Authenticated | Delete file      |
| DELETE | `/api/mentoring-sessions/{id}/coaching-files`                   | Authenticated | Delete all files |

---

### 7. **Achievements**

Users can document and showcase their achievements.

#### Achievement Details

-   Achievement title and description
-   Achievement date
-   Certificates or proof
-   Visibility settings
-   Categorization

#### Achievement Endpoints

| Method | Endpoint                 | Access        | Description        |
| ------ | ------------------------ | ------------- | ------------------ |
| GET    | `/api/achievements`      | Authenticated | List achievements  |
| POST   | `/api/achievements`      | Authenticated | Create achievement |
| GET    | `/api/achievements/{id}` | Authenticated | Get achievement    |
| PUT    | `/api/achievements/{id}` | Authenticated | Update achievement |
| DELETE | `/api/achievements/{id}` | Authenticated | Delete achievement |

---

### 8. **Experiences**

Users can document their professional and educational experiences.

#### Experience Information

-   Position/role title
-   Company/organization name
-   Employment dates
-   Description of responsibilities
-   Skills demonstrated
-   Experience type (professional, educational, volunteer)

#### Experience Endpoints

| Method | Endpoint                | Access        | Description       |
| ------ | ----------------------- | ------------- | ----------------- |
| GET    | `/api/experiences`      | Authenticated | List experiences  |
| POST   | `/api/experiences`      | Authenticated | Add experience    |
| GET    | `/api/experiences/{id}` | Authenticated | Get experience    |
| PUT    | `/api/experiences/{id}` | Authenticated | Update experience |
| DELETE | `/api/experiences/{id}` | Authenticated | Delete experience |

---

### 10. **Organizations**

Manage organizations for scholarship offerings and partnerships.

#### Organization Information

-   Organization name
-   Contact information
-   Organization type
-   Location/headquarters
-   Website
-   Description
-   Contact person details

#### Organization Endpoints

| Method | Endpoint                  | Access        | Description         |
| ------ | ------------------------- | ------------- | ------------------- |
| GET    | `/api/organizations`      | Authenticated | List organizations  |
| POST   | `/api/organizations`      | Authenticated | Create organization |
| GET    | `/api/organizations/{id}` | Authenticated | Get organization    |
| PUT    | `/api/organizations/{id}` | Authenticated | Update organization |
| DELETE | `/api/organizations/{id}` | Authenticated | Delete organization |

---

### 11. **Subscriptions**

Premium subscription plans for access to additional features.

#### Subscription Information

-   `user_id` - Subscribing user
-   `plan` - Subscription plan type
-   `start_date` - Subscription start date
-   `end_date` - Subscription end date
-   `status` - Subscription status (active, inactive, expired)
-   `type` - Type of subscription
-   `package_type` - Package tier level
-   `duration` - Duration length
-   `duration_unit` - Unit (days, months, years)
-   `courses_ids` - Associated courses (array)

#### Subscription Statuses

-   `active` - Currently active
-   `inactive` - Not currently active
-   `expired` - Subscription expired
-   `cancelled` - User cancelled
-   `paused` - Temporarily paused

#### Subscription Endpoints

| Method | Endpoint                          | Access        | Description          |
| ------ | --------------------------------- | ------------- | -------------------- |
| GET    | `/api/subscriptions`              | Authenticated | List subscriptions   |
| POST   | `/api/subscriptions`              | Authenticated | Create subscription  |
| GET    | `/api/subscriptions/{id}`         | Authenticated | Get subscription     |
| PUT    | `/api/subscriptions/{id}`         | Authenticated | Update subscription  |
| DELETE | `/api/subscriptions/{id}`         | Authenticated | Cancel subscription  |
| POST   | `/api/subscriptions/{id}/upgrade` | Authenticated | Upgrade subscription |

---

### 12. **Reviews & Ratings**

Users can review courses and scholarships.

#### Review Information

-   `user_id` - Reviewer user
-   `reviewable_id` - ID of reviewed item
-   `reviewable_type` - Type of item reviewed (Course, Scholarship)
-   `rating` - Rating (typically 1-5)
-   `comment` - Text comment/review

#### Review Endpoints

| Method | Endpoint            | Access        | Description   |
| ------ | ------------------- | ------------- | ------------- |
| GET    | `/api/reviews`      | Public        | List reviews  |
| POST   | `/api/reviews`      | Authenticated | Create review |
| GET    | `/api/reviews/{id}` | Public        | Get review    |
| PUT    | `/api/reviews/{id}` | Authenticated | Update review |
| DELETE | `/api/reviews/{id}` | Authenticated | Delete review |

---

### 13. **Articles**

Blog content created by admins and corporate partners.

#### Article Information

-   `author_id` - Author user ID
-   `title` - Article title
-   `content` - Article content (HTML/markdown)
-   `category` - Content category
-   `slug` - URL-friendly slug
-   `featured_image` - Featured image URL
-   `published_at` - Publication date
-   `status` - Article status (draft, published, archived)

#### Article Endpoints

| Method | Endpoint             | Access          | Description    |
| ------ | -------------------- | --------------- | -------------- |
| GET    | `/api/articles`      | Public          | List articles  |
| GET    | `/api/articles/{id}` | Public          | Get article    |
| POST   | `/api/articles`      | Admin/Corporate | Create article |
| PUT    | `/api/articles/{id}` | Admin/Corporate | Update article |
| DELETE | `/api/articles/{id}` | Admin/Corporate | Delete article |

---

### 14. **Transactions**

Payment tracking for courses, subscriptions, and mentoring sessions.

#### Transaction Information

-   `user_id` - User making payment
-   `transaction_code` - Unique transaction code
-   `type` - Transaction type (enrollment, subscription, mentoring)
-   `transactionable_id` - Related entity ID
-   `transactionable_type` - Related entity type
-   `amount` - Transaction amount (decimal)
-   `payment_method` - Payment method (credit card, bank transfer, etc.)
-   `status` - Transaction status (pending, paid, expired, failed)
-   `payment_details` - Payment details (JSON)
-   `payment_proof` - Payment proof/receipt
-   `paid_at` - Payment completion datetime
-   `expired_at` - Payment expiration datetime

#### Transaction Statuses

-   `pending` - Awaiting payment
-   `paid` - Successfully paid
-   `expired` - Payment window closed
-   `failed` - Payment failed
-   `cancelled` - Payment cancelled

#### Transaction Endpoints

| Method | Endpoint                 | Access        | Description        |
| ------ | ------------------------ | ------------- | ------------------ |
| GET    | `/api/transactions`      | Authenticated | List transactions  |
| POST   | `/api/transactions`      | Authenticated | Create transaction |
| GET    | `/api/transactions/{id}` | Authenticated | Get transaction    |

---

### 15. **Corporate Contact**

Handle corporate partnership inquiries.

#### Corporate Contact Information

-   Company name
-   Contact person name
-   Email address
-   Phone number
-   Contact inquiry/message
-   Company size
-   Industry
-   Status (new, reviewing, contacted, closed)

#### Corporate Contact Endpoints

| Method | Endpoint                              | Access | Description    |
| ------ | ------------------------------------- | ------ | -------------- |
| POST   | `/api/corporate-contact`              | Public | Submit inquiry |
| GET    | `/api/corporate-contacts`             | Admin  | List inquiries |
| GET    | `/api/corporate-contacts/{id}`        | Admin  | Get inquiry    |
| PUT    | `/api/corporate-contacts/{id}/status` | Admin  | Update status  |
| DELETE | `/api/corporate-contacts/{id}`        | Admin  | Delete inquiry |

---

## API Endpoints

### Authentication Flow

```
1. Register: POST /api/register
   {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "password123",
     "role": "user"
   }

2. Login: POST /api/login
   {
     "email": "john@example.com",
     "password": "password123"
   }
   Response: { "token": "eyJ0eXAiOiJKV1QiLCJhbGc..." }

3. Use token in header: Authorization: Bearer {token}
```

### Rate Limiting

The API implements standard Laravel rate limiting. Check response headers for:

-   `X-RateLimit-Limit`
-   `X-RateLimit-Remaining`
-   `X-RateLimit-Reset`

### Error Responses

All errors follow a consistent format:

```json
{
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    },
    "status": 422
}
```

Common HTTP Status Codes:

-   `200` - OK
-   `201` - Created
-   `204` - No Content
-   `400` - Bad Request
-   `401` - Unauthorized
-   `403` - Forbidden
-   `404` - Not Found
-   `422` - Validation Error
-   `500` - Server Error

---

## Database Models

### Entity Relationships

```
User
├── Achievements (1:N)
├── Experiences (1:N)
├── Organizations (1:N)
├── Subscriptions (1:N)
├── Enrollments (1:N)
│   └── Course (N:1)
├── ScholarshipApplications (1:N)
│   └── Scholarship (N:1)
├── MentoringSessions (1:N)
│   ├── NeedAssessment (1:1)
│   └── CoachingFiles (1:N)
├── Reviews (1:N)
├── Articles (1:N)
└── Transactions (1:N)

Course
├── Enrollments (1:N)
│   └── Users (N:M through enrollments)
└── Reviews (1:N via polymorphic)

Scholarship
├── ScholarshipApplications (1:N)
├── Organization (N:1)
└── Reviews (1:N via polymorphic)

MentoringSession
├── Mentor (N:1 to User)
├── Student/Member (N:1 to User)
├── NeedAssessment (1:1)
├── CoachingFiles (1:N)
└── Transactions (1:N via polymorphic)

Organization
└── Scholarships (1:N)

Transaction (Polymorphic)
├── Enrollment
├── Subscription
└── MentoringSession
```

### Model Details

#### User Model

Primary model for all users with roles and profile information.

#### Course Model

Represents online courses with video content and pricing.

#### Enrollment Model

Tracks student enrollment in courses with progress.

#### Scholarship Model

Manages scholarship opportunities offered by organizations.

#### ScholarshipApplication Model

Tracks scholarship applications from users.

#### MentoringSession Model

Represents mentoring relationships and sessions.

#### NeedAssessment Model

Stores assessment data for mentoring sessions.

#### CoachingFile Model

Manages coaching materials attached to mentoring sessions.

#### Subscription Model

Tracks user subscription plans and access levels.

#### Achievement Model

User achievements and certifications.

#### Experience Model

Professional and educational experiences.

#### Organization Model

Partner organizations offering scholarships.

#### Article Model

Blog articles by admins and corporate partners.

#### Review Model

User reviews and ratings (polymorphic).

#### Transaction Model

Payment tracking (polymorphic).

#### CorporateContact Model

Corporate partnership inquiries.

---

## User Roles
### Role: User (Student/Member)

**Permissions:**

-   Create and manage personal profile
-   Enroll in courses
-   Track course progress
-   View scholarships
-   Apply for scholarships
-   Schedule mentoring sessions
-   Create reviews
-   Manage personal achievements and experiences
-   View articles
-   Create subscriptions

**Endpoints Access:** Most authenticated endpoints

### Role: Admin

**Permissions:**

-   All user permissions
-   Create and manage courses
-   Create and manage scholarships
-   Manage all users
-   Manage corporate contacts
-   Create and manage articles
-   Update scholarship application statuses
-   View all transactions
-   Manage organization accounts
-   System administration

**Endpoints Access:** All authenticated endpoints + admin-only endpoints

### Role: Corporate

**Permissions:**

-   Create and manage scholarships
-   Manage scholarship applications
-   Create and manage articles
-   View corporate contacts
-   Create partnerships
-   Limited user management for corporate users

**Endpoints Access:** Most authenticated endpoints + corporate-specific endpoints

---

## Installation & Setup

### Requirements

-   PHP 8.2+
-   Composer
-   Node.js & NPM
-   SQLite or MySQL

### Installation Steps

```bash
# Clone repository
git clone <repo-url>
cd final-project

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Generate JWT secret
php artisan jwt:secret

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Environment Variables

Key environment variables in `.env`:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
JWT_SECRET=<auto-generated>
APP_URL=http://localhost:8000
```

---

## Testing

Run tests using PHPUnit:

```bash
php artisan test
```

Run specific test:

```bash
php artisan test tests/Feature/AuthControllerTest.php
```

---

## API Documentation Format

### Example Request

```
POST /api/courses/1/enroll
Authorization: Bearer <token>
Content-Type: application/json
```

### Example Response (Success)

```json
{
    "message": "Successfully enrolled in course",
    "data": {
        "id": 1,
        "user_id": 5,
        "course_id": 1,
        "progress": 0,
        "completed": false,
        "created_at": "2025-11-19T10:30:00Z"
    }
}
```

---

## Security Considerations

1. **JWT Tokens:** Tokens expire after a set time
2. **HTTPS:** Use HTTPS in production
3. **Rate Limiting:** Implemented per IP/user
4. **SQL Injection:** Protected via Eloquent ORM
5. **CSRF Protection:** Enabled for web routes
6. **Password Hashing:** Argon2 hashing used
7. **Role-Based Access:** Enforced at middleware level
8. **File Uploads:** Validated and scanned
9. **Sensitive Data:** Hidden from API responses

---

## Support & Maintenance

For issues or feature requests, please contact the development team.

**Key Contacts:**

-   Admin Email: admin@platform.local
-   Support: support@platform.local

---

**Last Updated:** November 19, 2025
**Version:** 1.0.0
