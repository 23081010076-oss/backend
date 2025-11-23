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
    php artisan migrate
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

### 4.1 User Management

| Method | Endpoint              | Description                                            | Auth |
| :----- | :-------------------- | :----------------------------------------------------- | :--- |
| POST   | `/register`           | Register new user (`role`: student, mentor, corporate) | No   |
| POST   | `/login`              | Login and get JWT token                                | No   |
| POST   | `/auth/logout`        | Invalidate token                                       | Yes  |
| GET    | `/auth/profile`       | Get current user details                               | Yes  |
| PUT    | `/auth/profile`       | Update profile (bio, education, etc.)                  | Yes  |
| POST   | `/auth/profile/photo` | Upload profile photo                                   | Yes  |
| POST   | `/experiences`        | Add work/education experience (Portfolio)              | Yes  |
| POST   | `/achievements`       | Add achievement (Portfolio)                            | Yes  |

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

## 5. Frontend Integration Guide

### CORS Configuration

The backend is configured to accept requests from:

-   `http://localhost:3000` (React Default)
-   `http://localhost:5173` (Vite Default)

### Handling File Uploads

For endpoints requiring file uploads (e.g., Scholarship Application, Profile Photo), use `FormData` in JavaScript:

```javascript
const formData = new FormData();
formData.append("cv_path", fileInput.files[0]);
formData.append("motivation_letter", "I want to join...");

axios.post("/api/scholarships/1/apply", formData, {
    headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
    },
});
```

### Handling Certificates

When a course progress reaches **100%**, the backend automatically generates a PDF certificate. The URL is returned in the `certificate_url` field of the enrollment object.

---

## 6. Testing

A complete API test suite is available in `feature_tests.http`.

1.  Install **REST Client** extension in VS Code.
2.  Open `feature_tests.http`.
3.  Run requests sequentially (Register -> Login -> Create Data -> Transact).

### Endpoints

| Method | Endpoint                     | Description                                               | Auth | Role    |
| :----- | :--------------------------- | :-------------------------------------------------------- | :--- | :------ |
| GET    | `/courses`                   | List all courses                                          | No   | -       |
| GET    | `/courses/{id}`              | Get course details                                        | No   | -       |
| POST   | `/courses`                   | Create a course                                           | Yes  | Admin   |
| PUT    | `/courses/{id}`              | Update a course                                           | Yes  | Admin   |
| DELETE | `/courses/{id}`              | Delete a course                                           | Yes  | Admin   |
| POST   | `/courses/{id}/enroll`       | Enroll in a course                                        | Yes  | Student |
| GET    | `/my-courses`                | List enrolled courses                                     | Yes  | Student |
| PUT    | `/enrollments/{id}/progress` | Update progress (0-100). **Triggers Certificate at 100%** | Yes  | Student |

### Flow: Course Completion

1.  **Enroll:** `POST /courses/{id}/enroll`
2.  **Pay:** (If paid course) See Transactions section.
3.  **Learn:** User watches videos/reads materials.
4.  **Update Progress:** `PUT /enrollments/{id}/progress` with `progress=100`.
5.  **Get Certificate:** Response includes `certificate_url`.

---

## 3. Scholarship Portal

**Features:** Scholarship listing, Application submission, Status tracking.

### Endpoints

| Method | Endpoint                                | Description                             | Auth | Role       |
| :----- | :-------------------------------------- | :-------------------------------------- | :--- | :--------- |
| GET    | `/scholarships`                         | List scholarships                       | No   | -          |
| GET    | `/scholarships/{id}`                    | Get scholarship details                 | No   | -          |
| POST   | `/scholarships`                         | Create scholarship                      | Yes  | Admin/Corp |
| POST   | `/scholarships/{id}/apply`              | Apply for scholarship (Upload CV, etc.) | Yes  | Student    |
| GET    | `/my-applications`                      | List my applications                    | Yes  | Student    |
| PUT    | `/scholarship-applications/{id}/status` | Update application status               | Yes  | Admin/Corp |

---

## 4. My Mentor (Mentoring)

**Features:** Mentor booking, Scheduling, Need Assessment, Coaching Files.

### Endpoints

| Method | Endpoint                            | Description                       | Auth | Role         |
| :----- | :---------------------------------- | :-------------------------------- | :--- | :----------- |
| GET    | `/mentoring-sessions`               | List available sessions           | Yes  | -            |
| POST   | `/mentoring-sessions`               | Create/Request a session          | Yes  | Student      |
| POST   | `/mentoring-sessions/{id}/schedule` | Set schedule (Zoom link, time)    | Yes  | Mentor       |
| PUT    | `/mentoring-sessions/{id}/status`   | Update status (pending/completed) | Yes  | Mentor/Admin |
| GET    | `/my-mentoring-sessions`            | List my sessions                  | Yes  | -            |

### Need Assessment & Coaching Files

| Method | Endpoint                                    | Description          | Auth | Role    |
| :----- | :------------------------------------------ | :------------------- | :--- | :------ |
| GET    | `/mentoring-sessions/{id}/need-assessments` | Get assessment form  | Yes  | -       |
| POST   | `/mentoring-sessions/{id}/need-assessments` | Submit assessment    | Yes  | Student |
| GET    | `/mentoring-sessions/{id}/coaching-files`   | List coaching files  | Yes  | -       |
| POST   | `/mentoring-sessions/{id}/coaching-files`   | Upload coaching file | Yes  | Mentor  |

### Flow: Mentoring

1.  **Request:** Student creates session `POST /mentoring-sessions`.
2.  **Pay:** See Transactions section.
3.  **Assessment:** Student fills `POST .../need-assessments`.
4.  **Schedule:** Mentor sets time/link `POST .../schedule`.
5.  **Coaching:** Mentor uploads files `POST .../coaching-files`.
6.  **Complete:** Mentor updates status `PUT .../status`.

---

## 5. Article & Corporate Services

**Features:** Educational content, Corporate partnership inquiries.

### Endpoints

| Method | Endpoint                    | Description                | Auth | Role       |
| :----- | :-------------------------- | :------------------------- | :--- | :--------- |
| GET    | `/articles`                 | List articles              | No   | -          |
| GET    | `/articles/{id}`            | Read article               | No   | -          |
| POST   | `/articles`                 | Publish article            | Yes  | Admin/Corp |
| POST   | `/corporate-contact`        | Submit partnership inquiry | No   | -          |
| GET    | `/admin/corporate-contacts` | View inquiries             | Yes  | Admin      |

---

## 6. Transactions & Payments

**Features:** Payment processing for Courses, Subscriptions, and Mentoring.

### Endpoints

| Method | Endpoint                                | Description                         | Auth | Role  |
| :----- | :-------------------------------------- | :---------------------------------- | :--- | :---- |
| GET    | `/transactions`                         | List my transactions                | Yes  | -     |
| POST   | `/transactions/courses/{id}`            | Create transaction for Course       | Yes  | -     |
| POST   | `/transactions/subscriptions`           | Create transaction for Subscription | Yes  | -     |
| POST   | `/transactions/mentoring-sessions/{id}` | Create transaction for Mentoring    | Yes  | -     |
| POST   | `/transactions/{id}/payment-proof`      | Upload proof of payment             | Yes  | -     |
| POST   | `/transactions/{id}/confirm`            | Confirm payment (Activate service)  | Yes  | Admin |

---

## 7. Master Data (Profile Components)

**Features:** Manage achievements, experiences, organizations.

### Endpoints

-   `/achievements` (CRUD)
-   `/experiences` (CRUD)
-   `/organizations` (CRUD)
-   `/subscriptions` (CRUD)
-   `/reviews` (CRUD)
