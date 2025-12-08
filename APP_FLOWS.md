# Application Architecture & User Flows

This document provides a comprehensive technical overview of the application's features, detailing the interaction between users, the frontend, and the backend API services.

## Table of Contents
1. [Authentication & User Profile](#1-authentication--user-profile)
2. [Portfolio & Experience Management](#2-portfolio--experience-management)
3. [Learning Management System (LMS)](#3-learning-management-system-lms)
4. [Course Management (Admin/Instructor)](#4-course-management-admininstructor)
5. [Subscription & Payment System](#5-subscription--payment-system)
6. [Mentoring System](#6-mentoring-system)
7. [Article & Blog System](#7-article--blog-system)
8. [Corporate Services](#8-corporate-services)

---

## 1. Authentication & User Profile

### Registration & Login Logic
Handles user onboarding and session management using JWT.

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant Auth as AuthController
    participant Google as Google OAuth
    participant DB as Database

    Note over User, DB: Traditional Login
    User->>FE: Login (Email, Password)
    FE->>Auth: POST /auth/login
    Auth->>DB: Validate Credentials
    
    alt Invalid
        Auth-->>FE: 401 Unauthorized
    else Valid
        Auth->>Auth: Generate JWT Token
        Auth-->>FE: 200 OK (Token + User Data)
    end

    Note over User, DB: Google OAuth
    User->>FE: Click "Login with Google"
    FE->>Auth: GET /auth/google/url
    Auth-->>FE: Return Redirect URL
    FE->>Google: Redirect User
    User->>Google: Approve Access
    Google->>FE: Callback?code=xyz
    FE->>Auth: POST /auth/google/callback (code)
    Auth->>Google: Exchange Code for Profile
    Auth->>DB: Find/Create User
    Auth-->>FE: Return JWT Token
```

---

## 2. Portfolio & Experience Management

Allows users to build their professional profile by adding work experience and education.

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant Exp as ExperienceController
    participant S3 as Storage (Public)
    participant DB as Database

    User->>FE: View Profile
    FE->>Exp: GET /experiences/user/{id}
    Exp-->>FE: Return List

    Note over User, DB: Add Work Experience
    User->>FE: Add Experience (Title, Company, Date)
    FE->>Exp: POST /experiences
    Exp->>DB: Save Record
    Exp-->>FE: 201 Created

    Note over User, DB: Upload Certificate
    User->>FE: Upload File (PDF/Image)
    FE->>Exp: POST /experiences/{id}/certificate
    Exp->>S3: Store File
    Exp->>DB: Update certificate_url
    Exp-->>FE: 200 OK (File URL)
```

---

## 3. Learning Management System (LMS)

The core learning flow for students.

### Course Enrollment & Learning Journey
```mermaid
stateDiagram-v2
    [*] --> BrowseCourses
    BrowseCourses --> CourseDetail : Select Course
    
    state check_access <<choice>>
    CourseDetail --> check_access : Click Enroll/Start
    
    check_access --> EnrollmentSuccess : Access Granted (Free/Subscribed)
    check_access --> AccessDenied : Access Denied
    
    AccessDenied --> SubscriptionFlow : Buy Subscription
    
    EnrollmentSuccess --> LearningMode
    
    state LearningMode {
        [*] --> ViewMaterial
        ViewMaterial --> MarkComplete : Finish Video/Reading
        MarkComplete --> CheckProgress : Auto-Calculate
        CheckProgress --> ViewMaterial : Next Item
        CheckProgress --> CourseCompleted : Progress 100%
    }
    
    CourseCompleted --> Certificate : Auto-Generated (PDF)
    Certificate --> [*]
```

---

## 4. Course Management (Admin/Instructor)

Flow for creating and managing educational content.

```mermaid
graph TD
    A[Admin/Instructor] -->|Create Course| B[Course Basic Info]
    B -->|Define| C{Course Type}
    C -->|Regular| D[Standard Curriculum]
    C -->|Bootcamp| E[Intensive Curriculum]
    
    D & E --> F[Add Sections]
    F --> G[Add Materials]
    G -->|Video URL + Duration| H[Curriculum Item]
    
    H --> I[Review Course]
    I -->|Publish| J[(Database)]
    J -->|Visible to| K[Students]
```

---

## 5. Subscription & Payment System

Handles billing, upgrades, and Midtrans integration.

### Purchase Flow
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant Sub as SubscriptionController
    participant Midtrans as Payment Gateway
    participant Webhook as MidtransWebhook

    User->>FE: Select Plan (Regular/Premium)
    FE->>Sub: POST /subscriptions/purchase
    Sub->>Sub: Create Pending Transaction
    Sub->>Midtrans: Request Snap Token
    Midtrans-->>Sub: Token & Redirect URL
    Sub-->>FE: Payment Data
    
    FE->>Midtrans: Open Payment Popup
    User->>Midtrans: Complete Payment
    
    par Async Webhook
        Midtrans->>Webhook: POST /webhooks/midtrans
        Webhook->>Webhook: Validate Signature
        Webhook->>DB: Update Transaction (PAID)
        Webhook->>DB: Create/Update Subscription (Active)
    and Frontend Check
        FE->>Sub: Poll Status
        Sub-->>FE: "Paid"
    end
```

---

## 6. Mentoring System

Connects students with mentors for personalized guidance.

### Booking & Session Flow
```mermaid
sequenceDiagram
    actor Student
    participant FE as Frontend
    participant API as Backend API
    participant Mentor

    Student->>FE: Request Mentoring Session
    FE->>API: POST /mentoring/book
    API-->>FE: Session Created (Pending)
    
    Note over Student, API: Assessment
    Student->>FE: Fill Need Assessment
    FE->>API: POST /mentoring-sessions/{id}/need-assessments
    Note right of FE: Goals, Challenges, Expectations
    API->>DB: Save Assessment
    
    Note over Student, Mentor: Session Time
    Mentor->>API: GET /mentoring-sessions/{id}/need-assessments
    API-->>Mentor: Show Student Goals
    
    Note right of Mentor: Conduct Session
    
    Mentor->>API: PUT /../mark-completed
    API-->>Mentor: Assessment & Session Closed
```

---

## 7. Scholarship Portal

Flow for students applying for financial aid.

```mermaid
graph TD
    A[Student] -->|Browse| B[Scholarship List]
    B -->|Select| C[Scholarship Detail]
    C -->|Click Apply| D[Application Form]
    
    D -->|Upload| E[Documents (CV, Transcript)]
    D -->|Submit| F[Backend API]
    F -->|Store| G[(Database)]
    F -->|Notify| H[Admin]
    
    H -->|Review| I{Decision}
    I -->|Accept| J[Update Status: Accepted]
    I -->|Reject| K[Update Status: Rejected]
    
    J & K -->|Notification| L[Student Dashboard]
```

---

## 8. Article & Blog System

Content management system for educational articles.

```mermaid
graph LR
    A[Admin/Mentor] -->|Write| B(Create Article)
    B -->|Upload| C[Cover Image]
    B -->|Tag| D[Category]
    D -->|Publish| E[(Database)]
    
    F[Public User] -->|Search/Filter| G{Article List}
    E --> G
    G -->|Click| H[Read Article]
    H -->|View| I[Author Profile]
```

---

## 8. Corporate Services

Flow for companies to request partnerships or training.

```mermaid
sequenceDiagram
    actor Company
    participant FE as Frontend
    participant Corp as CorporateContactController
    participant Admin

    Company->>FE: Fill Contact Form (Name, Email, Message)
    FE->>Corp: POST /corporate-contacts
    Corp->>DB: Store Inquiry (Status: New)
    Corp-->>FE: Success Message

    Note over Admin: Dashboard Logic
    Admin->>Corp: GET /corporate-contacts
    Corp-->>Admin: List Inquiries
    
    Admin->>Company: Contact via Email/Phone
    Admin->>Corp: PUT /corporate-contacts/{id}/status
    Note right of Admin: Update to 'Contacted'/'Closed'
```
