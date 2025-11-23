# Backend API Documentation & Developer Guide

## 1. Project Overview

This is the backend REST API for the Education & Career Development Platform. It is built using **Laravel 12** and serves as the core logic for User Management, E-Learning, Scholarship Portal, Mentoring, and Corporate Services.

-   **Framework**: Laravel 12
-   **Database**: MySQL
-   **Authentication**: JWT (JSON Web Token)
-   **API Testing**: REST Client (`.http` files)

---

## 2. Installation & Setup

### Prerequisites

-   PHP >= 8.2
-   Composer
-   MySQL

### Steps

1.  **Clone the repository**

    ```bash
    git clone <repository_url>
    cd backend
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    ```

3.  **Environment Configuration**

    -   Copy `.env.example` to `.env`
    -   Configure database credentials in `.env`:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=final_project_db
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Generate Key & Migrate**

    ```bash
    php artisan key:generate
    php artisan jwt:secret
    php artisan migrate --seed
    ```

5.  **Run Server**
    ```bash
    php artisan serve
    ```
    API will be available at: `http://localhost:8000/api`

---

## 3. Authentication Flow

The API uses **Bearer Token** authentication.

1.  **Login** via `POST /api/login` to receive a `token`.
2.  Send this token in the **Authorization** header for all protected routes:
    ```
    Authorization: Bearer <your_access_token>
    ```

**Roles:**

-   `student`: Can enroll in courses, apply for scholarships, book mentors.
-   `mentor`: Can manage mentoring sessions, upload coaching files.
-   `corporate`: Can post scholarships and manage their **Organization Profile** (separate entity from User Profile).
-   `admin`: Full access to manage users, courses, articles.

---

## 4. API Reference

### 4.1 User Management & Profile

| Method | Endpoint                 | Description                                            | Auth |
| :----- | :----------------------- | :----------------------------------------------------- | :--- |
| POST   | `/register`              | Register new user (`role`: student, mentor, corporate) | No   |
| POST   | `/login`                 | Login and get JWT token                                | No   |
| POST   | `/auth/logout`           | Invalidate token                                       | Yes  |
| GET    | `/auth/profile`          | Get current user details                               | Yes  |
| PUT    | `/auth/profile`          | Update profile (bio, education, etc.)                  | Yes  |
| POST   | `/auth/profile/photo`    | Upload profile photo                                   | Yes  |
| POST   | `/auth/profile/cv`       | Upload CV (PDF/Doc)                                    | Yes  |
| GET    | `/auth/portfolio`        | Get complete portfolio (Experiences, Achievements)     | Yes  |
| GET    | `/auth/recommendations`  | Get course recommendations based on major              | Yes  |
| GET    | `/auth/activity-history` | Get summary of user activities                         | Yes  |

### 4.2 E-Learning & Bootcamp

| Method | Endpoint                     | Description                                               | Auth          |
| :----- | :--------------------------- | :-------------------------------------------------------- | :------------ |
| GET    | `/courses`                   | List all courses (filter by type, level)                  | No            |
| POST   | `/courses`                   | Create course (Admin only)                                | Yes (Admin)   |
| POST   | `/subscriptions`             | Buy subscription package (`single_course` / `all_in_one`) | Yes (Student) |
| POST   | `/enrollments/{id}/enroll`   | Enroll in a course                                        | Yes (Student) |
| PUT    | `/enrollments/{id}/progress` | Update learning progress (0-100)                          | Yes (Student) |
| GET    | `/enrollments/my-courses`    | Get enrolled courses                                      | Yes (Student) |

### 4.3 Scholarship Portal

| Method | Endpoint                   | Description                             | Auth            |
| :----- | :------------------------- | :-------------------------------------- | :-------------- |
| GET    | `/scholarships`            | List active scholarships                | No              |
| POST   | `/organizations`           | Create organization profile             | Yes (Corporate) |
| POST   | `/scholarships`            | Post new scholarship                    | Yes (Corporate) |
| POST   | `/scholarships/{id}/apply` | Apply for scholarship (Upload CV, etc.) | Yes (Student)   |
| POST   | `/reviews`                 | Review a scholarship provider           | Yes (Student)   |

### 4.4 My Mentor

| Method | Endpoint                              | Description                          | Auth          |
| :----- | :------------------------------------ | :----------------------------------- | :------------ |
| GET    | `/users?role=mentor`                  | List available mentors               | Yes           |
| POST   | `/mentoring-sessions`                 | Book a mentoring session             | Yes (Student) |
| POST   | `/mentoring-sessions/{id}/assessment` | Submit pre-mentoring need assessment | Yes (Student) |
| POST   | `/coaching-files`                     | Upload coaching materials            | Yes (Mentor)  |

### 4.5 Articles & Corporate Services

| Method | Endpoint              | Description                | Auth        |
| :----- | :-------------------- | :------------------------- | :---------- |
| GET    | `/articles`           | List educational articles  | No          |
| POST   | `/articles`           | Publish article            | Yes (Admin) |
| POST   | `/corporate-contacts` | Submit partnership inquiry | No          |

### 4.6 Transactions

| Method | Endpoint        | Description                   | Auth |
| :----- | :-------------- | :---------------------------- | :--- |
| GET    | `/transactions` | List user transaction history | Yes  |

---

## 5. Detailed Feature Descriptions

### 5.1 User Management

The system supports multiple roles with distinct capabilities.

-   **Student**: The primary learner. Can build a portfolio, enroll in courses, and apply for scholarships.
-   **Mentor**: Professionals who offer guidance. They can manage their availability and upload coaching resources.
-   **Corporate**: Companies or organizations. They manage their **Organization Profile** (separate from the user account) and post scholarship opportunities.
-   **Admin**: Oversees the entire platform, verifies payments, and manages content.

### 5.2 My Profile & Portfolio

A centralized hub for user career development.

-   **CV Upload**: Users can upload their CVs (`/auth/profile/cv`) for easy access during applications.
-   **Portfolio**: Automatically aggregates Experiences (Work/Internship) and Achievements.
-   **Certificates**: Users can upload certificates for their work experiences to validate their skills.
-   **Recommendations**: The system suggests courses based on the user's `major` (field of study).

### 5.3 E-Learning

-   **Subscription Models**: Users can choose between buying a single course or an "All-in-One" subscription for unlimited access.
-   **Progress Tracking**: Granular tracking of course completion.
-   **Automatic Certification**: Upon reaching 100% progress, a certificate URL is generated.

### 5.4 Scholarship Portal

-   **Organization-Centric**: Scholarships are linked to Organizations. This allows students to view the company profile before applying.
-   **Application Tracking**: Students can track the status of their applications (Submitted -> Review -> Accepted/Rejected).

### 5.5 Mentoring

-   **Need Assessment**: A pre-session form ensures mentors understand the student's goals before the meeting.
-   **Coaching Files**: A dedicated file sharing system for mentors to provide resources to their mentees.

---

## 6. Frontend Integration Guide

### CORS Configuration

The backend is configured to accept requests from:

-   `http://localhost:3000` (React Default)
-   `http://localhost:5173` (Vite Default)

### Handling File Uploads

For endpoints requiring file uploads (e.g., Scholarship Application, Profile Photo, CV), use `FormData` in JavaScript:

```javascript
const formData = new FormData();
formData.append("cv", fileInput.files[0]); // For CV upload

axios.post("/api/auth/profile/cv", formData, {
    headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
    },
});
```

### Handling Certificates

When a course progress reaches **100%**, the backend automatically generates a PDF certificate. The URL is returned in the `certificate_url` field of the enrollment object.

---

## 7. Testing

A complete API test suite is available in `feature_tests.http`.

1.  Install **REST Client** extension in VS Code.
2.  Open `feature_tests.http`.
3.  Run requests sequentially (Register -> Login -> Create Data -> Transact).

-   `/organizations` (CRUD)
-   `/subscriptions` (CRUD)
-   `/reviews` (CRUD)
