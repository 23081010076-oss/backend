# Application User Flows

This document outlines the primary user flows within the application, visualizing the interaction between users, the frontend, and the backend services.

## 1. Authentication Authorization

### Registration & Login Logic
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as Backend API
    participant DB as Database

    User->>FE: Register (Name, Email, Password, Role)
    FE->>API: POST /auth/register
    API->>DB: Create User (Inactive)
    API-->>FE: Success (Verify Email)
    
    User->>FE: Login (Email, Password)
    FE->>API: POST /auth/login
    API->>DB: Validate Credentials
    
    alt Invalid
        API-->>FE: 401 Unauthorized
    else Valid
        API->>API: Generate Token
        API-->>FE: 200 OK (Token + User Data)
        FE->>FE: Store Token
    end
```

### Google OAuth Flow
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as Backend API
    participant Google as Google Auth

    User->>FE: Click "Login with Google"
    FE->>API: GET /auth/google/url
    API-->>FE: Return Google Redirect URL
    FE->>Google: Redirect User
    User->>Google: Approve Access
    Google->>FE: Redirect Callback?code=xyz
    FE->>API: POST /auth/google/callback (code)
    API->>Google: Exchange Code for Profile
    API->>API: Find or Create User
    API-->>FE: Return Token
```

---

## 2. Learning Management System (LMS)

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
        MarkComplete --> CheckProgress
        CheckProgress --> ViewMaterial : Next Item
        CheckProgress --> CourseCompleted : Progress 100%
    }
    
    CourseCompleted --> Certificate : Auto-Generated
    Certificate --> [*]
```

---

## 3. Subscription & Payment System

### Subscription Purchase Flow
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as Backend API
    participant Midtrans as Payment Gateway

    User->>FE: Select Plan (Regular/Premium)
    FE->>API: POST /subscriptions/purchase
    API->>API: Validate Request
    API->>Midtrans: Create Transaction (Snap Token)
    Midtrans-->>API: Return Token & Redirect URL
    API-->>FE: Return Payment Data
    
    FE->>Midtrans: Open Payment Popup
    User->>Midtrans: Complete Payment
    
    par Async Webhook
        Midtrans->>API: POST /webhooks/midtrans
        API->>API: Validate Signature
        API->>API: Update Transaction Status
        API->>API: Activate Subscription
    and Frontend Check
        FE->>API: Check Status
        API-->>FE: "Paid"
    end
    
    FE->>User: Show Success Message
```

---

## 4. Mentoring System

### Booking & Session Flow
```mermaid
sequenceDiagram
    actor Student
    actor Mentor
    participant API as Backend API

    Student->>API: GET /mentors (Browse)
    Student->>API: POST /mentoring/book (Select Slot)
    
    alt Paid Session
        API->>Student: Request Payment (Midtrans Flow)
        Student->>API: Payment Success
    end
    
    API->>Mentor: Notify New Booking
    
    Note over Student, Mentor: Session Time
    
    Student->>API: GET /mentoring/session/{id}
    Mentor->>API: GET /mentoring/session/{id}
    
    API-->>Student: Return Meeting Link/Details
    API-->>Mentor: Return Meeting Link/Details
```

---

## 5. Specialized Features

### Scholarship Application
```mermaid
graph LR
    A[User] -->|Submit| B(Scholarship Form)
    B -->|POST| C[Backend API]
    C -->|Store| D[(Database)]
    E[Admin] -->|Review| D
    E -->|Update Status| F{Decision}
    F -->|Approved| G[Notify User]
    F -->|Rejected| H[Notify User]
```

### Mentoring Need Assessment
```mermaid
sequenceDiagram
    actor Student
    participant FE as Frontend
    participant API as Backend API
    participant Mentor

    Student->>FE: Request Mentoring Session
    FE->>API: POST /mentoring/book
    API-->>FE: Session Created (Pending)
    
    Student->>FE: Fill Need Assessment
    FE->>API: POST /mentoring-sessions/{id}/need-assessments
    Note right of FE: Goals, Challenges, Expectations
    
    API->>API: Validates Student Access
    API-->>FE: Assessment Saved
    
    Mentor->>API: GET /mentoring-sessions/{id}/need-assessments
    API-->>Mentor: Show Student Goals
    
    Note over Student, Mentor: Session Conducted
    
    Mentor->>API: PUT /../mark-completed
    API-->>Mentor: Assessment & Session Closed
```
