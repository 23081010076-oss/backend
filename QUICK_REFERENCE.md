# 🚀 QUICK REFERENCE - Learning Platform API

## 📍 Project Info

-   **Name:** Learning Platform API
-   **Framework:** Laravel 11
-   **Database:** MySQL (17 tables, 21 migrations)
-   **Auth:** JWT (tymon/jwt-auth v2.2.1)
-   **Status:** Production Ready ✅

---

## 🎯 6 Main Features

| #   | Feature             | Endpoints | Key Models                             | Type     |
| --- | ------------------- | --------- | -------------------------------------- | -------- |
| 1   | **User Management** | 11        | User, Auth                             | Core     |
| 2   | **E-Learning**      | 10        | Course, Enrollment, Subscription       | Learning |
| 3   | **Scholarship**     | 12        | Scholarship, Application, Organization | Content  |
| 4   | **Mentoring**       | 7         | MentoringSession, Transaction          | Service  |
| 5   | **Articles**        | 7         | Article, Review, CorporateContact      | Content  |
| 6   | **Portfolio**       | 8         | Achievement, Experience, Transaction   | Profile  |

---

## 🔐 Authentication

### Register

```bash
POST /api/register
{
  "name": "John Doe",
  "email": "john@email.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "student"  # student, mentor, admin, corporate
}
```

### Login

```bash
POST /api/login
{
  "email": "john@email.com",
  "password": "password123"
}

Response: { "token": "...", "token_type": "Bearer", "expires_in": 3600 }
```

### Use Token

```bash
Authorization: Bearer {token}
```

### Logout

```bash
POST /api/auth/logout
Header: Authorization: Bearer {token}
```

---

## 👤 Role Types

```
STUDENT     - Belajar, apply scholarship, booking mentoring
MENTOR      - Mengajar, buat session mentoring
ADMIN       - Manage users, courses, scholarship, content
CORPORATE   - Post scholarship, artikel, inquiry
```

---

## 📚 Course Endpoints

```bash
# PUBLIC
GET     /courses                          # List
GET     /courses/{id}                     # Detail

# PROTECTED (JWT Required)
POST    /courses                          # Create (admin)
PUT     /courses/{id}                     # Update (admin)
DELETE  /courses/{id}                     # Delete (admin)

# ENROLLMENT
GET     /enrollments                      # My enrollments
POST    /enrollments                      # Enroll course
PUT     /enrollments/{id}/progress        # Update progress (0-100)
DELETE  /enrollments/{id}                 # Cancel enrollment

# SUBSCRIPTION
GET     /subscriptions                    # My subscriptions
POST    /subscriptions                    # Subscribe
PUT     /subscriptions/{id}               # Upgrade
DELETE  /subscriptions/{id}               # Cancel

# PERSONAL
GET     /my-courses                       # My courses
```

---

## 🎓 Scholarship Endpoints

```bash
# PUBLIC
GET     /scholarships                     # List
GET     /scholarships/{id}                # Detail

# PROTECTED
POST    /scholarships                     # Create (corporate)
PUT     /scholarships/{id}                # Update (corporate)
DELETE  /scholarships/{id}                # Delete (corporate)

# APPLICATION
POST    /scholarships/{id}/apply          # Apply scholarship
GET     /my-scholarship-applications      # My applications
PUT     /scholarship-applications/{id}/status  # Admin approve/reject

# REVIEW
GET     /reviews                          # List reviews (public)
POST    /reviews                          # Create review
PUT     /reviews/{id}                     # Update review
DELETE  /reviews/{id}                     # Delete review
```

---

## 👨‍🏫 Mentoring Endpoints

```bash
# PUBLIC
GET     /mentoring-sessions               # Browse mentors

# PROTECTED
POST    /mentoring-sessions               # Book session
PUT     /mentoring-sessions/{id}          # Update session
DELETE  /mentoring-sessions/{id}          # Cancel session
PUT     /mentoring-sessions/{id}/status   # Update status

# PERSONAL
GET     /my-mentoring-as-student          # Sessions I join
GET     /my-mentoring-as-mentor           # Sessions I teach
```

---

## 📝 Profile Endpoints

```bash
# AUTH
GET     /auth/profile                     # Get profile
PUT     /auth/profile                     # Update profile
POST    /auth/profile/photo               # Upload photo
GET     /auth/portfolio                   # View portfolio
GET     /auth/activity-history            # Activity log
POST    /auth/logout                      # Logout

# PORTFOLIO
GET     /achievements                     # My achievements
POST    /achievements                     # Create
PUT     /achievements/{id}                # Update
DELETE  /achievements/{id}                # Delete

GET     /experiences                      # My experiences
POST    /experiences                      # Create
PUT     /experiences/{id}                 # Update
DELETE  /experiences/{id}                 # Delete

GET     /transactions                     # Transaction history
POST    /transactions/courses/{id}        # Pay for course
POST    /transactions/subscription        # Pay for subscription
POST    /transactions/mentoring-sessions/{id}  # Pay for mentoring
POST    /transactions/{id}/upload-proof   # Upload payment proof
POST    /transactions/{id}/refund         # Request refund
```

---

## 📄 Article Endpoints

```bash
# PUBLIC
GET     /articles                         # List
GET     /articles/{id}                    # Detail

# PROTECTED
POST    /articles                         # Create (admin/corporate)
PUT     /articles/{id}                    # Update (admin/corporate)
DELETE  /articles/{id}                    # Delete (admin/corporate)

# CORPORATE CONTACT
POST    /corporate-contact                # Submit inquiry (public)
GET     /admin/corporate-contacts         # List (admin)
GET     /admin/corporate-contacts/{id}    # Detail (admin)
PUT     /admin/corporate-contacts/{id}/status  # Update status
DELETE  /admin/corporate-contacts/{id}    # Delete
```

---

## 👥 Admin Endpoints

```bash
# USER MANAGEMENT
GET     /admin/users                      # List all users
POST    /admin/users                      # Create user
GET     /admin/users/{id}                 # Get user
PUT     /admin/users/{id}                 # Update user
DELETE  /admin/users/{id}                 # Delete user
```

---

## 🗄️ Database Tables

```
users                      - User accounts
courses                    - Courses/bootcamp
enrollments               - Course enrollment
subscriptions             - Premium subscription
scholarships              - Scholarship listings
scholarship_applications  - Student applications
organizations             - Company/organization
reviews                   - Polymorphic reviews
mentoring_sessions        - Mentor-student sessions
transactions              - Payment records
articles                  - Articles/content
corporate_contacts        - Corporate inquiry form
achievements              - User achievements
experiences               - Work experience
cache                     - Cache data
jobs                      - Queue jobs
```

---

## 🔄 Polymorphic Relationships

### Reviews (dapat untuk multiple models)

```
- Scholarship    (students review scholarship)
- Course         (students review course)
- Article        (users review article)
- Organization   (users review organization)
```

### Transactions (dapat untuk multiple models)

```
- Course         (payment untuk course enrollment)
- Subscription   (payment untuk premium)
- MentoringSession  (payment untuk mentoring session)
```

---

## 📋 Request Format

```bash
# GET Request
curl -X GET http://localhost:8000/api/courses \
  -H "Authorization: Bearer {token}"

# POST Request
curl -X POST http://localhost:8000/api/enrollments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "course_id": 1,
    "status": "active"
  }'

# PUT Request
curl -X PUT http://localhost:8000/api/enrollments/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "progress": 50
  }'

# DELETE Request
curl -X DELETE http://localhost:8000/api/enrollments/1 \
  -H "Authorization: Bearer {token}"

# File Upload
curl -X POST http://localhost:8000/api/auth/profile/photo \
  -H "Authorization: Bearer {token}" \
  -F "photo=@/path/to/photo.jpg"
```

---

## 📊 Response Format

### Success Response (200)

```json
{
  "message": "Success message",
  "data": {
    "id": 1,
    "name": "John Doe",
    ...
  }
}
```

### Validation Error (422)

```json
{
    "message": "Validation error",
    "errors": {
        "email": ["Email sudah terdaftar"],
        "password": ["Password minimal 8 karakter"]
    }
}
```

### Unauthorized (401)

```json
{
    "message": "Unauthenticated"
}
```

### Forbidden (403)

```json
{
    "message": "Forbidden - You don't have permission"
}
```

### Not Found (404)

```json
{
    "message": "Resource not found"
}
```

---

## 🛡️ Status Codes

| Code | Meaning       | When                   |
| ---- | ------------- | ---------------------- |
| 200  | OK            | Success                |
| 201  | Created       | Successfully created   |
| 204  | No Content    | Successfully deleted   |
| 400  | Bad Request   | Invalid format         |
| 401  | Unauthorized  | No/invalid token       |
| 403  | Forbidden     | No permission          |
| 404  | Not Found     | Resource doesn't exist |
| 422  | Unprocessable | Validation error       |
| 500  | Server Error  | Server error           |

---

## 🔗 Request/Response Examples

### Example 1: Register

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "student"
  }'
```

### Example 2: Login & Get Token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# Response:
# {
#   "message": "Login successful",
#   "data": {
#     "user": {
#       "id": 1,
#       "name": "John Doe",
#       "email": "john@example.com",
#       "role": "student"
#     },
#     "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
#     "token_type": "Bearer",
#     "expires_in": 3600
#   }
# }
```

### Example 3: Use Token

```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."

# Response:
# {
#   "user": {
#     "id": 1,
#     "name": "John Doe",
#     "email": "john@example.com",
#     "role": "student",
#     "phone": "08123456789",
#     ...
#   }
# }
```

---

## 🧪 Testing Tools

| Tool               | Usage                           |
| ------------------ | ------------------------------- |
| **Postman**        | Import `API_TESTING.json`       |
| **cURL**           | Use `CURL_COMMANDS_TESTING.txt` |
| **Thunder Client** | VS Code extension               |
| **REST Client**    | VS Code extension               |
| **Insomnia**       | API client                      |

---

## ⚡ Quick Start

```bash
# 1. Start server
php artisan serve

# 2. Register user
POST http://localhost:8000/api/register

# 3. Login
POST http://localhost:8000/api/login

# 4. Copy token
# Use in: Authorization: Bearer {token}

# 5. Test endpoint
GET http://localhost:8000/api/auth/profile
```

---

## 🎯 Key Features

✅ **73 Endpoints** - Complete API coverage  
✅ **JWT Auth** - Stateless authentication  
✅ **6 Features** - All core features implemented  
✅ **17 Tables** - Normalized database design  
✅ **Role-Based** - Student, mentor, admin, corporate  
✅ **Validation** - FormRequest validation  
✅ **Response Formatting** - Resource classes  
✅ **File Upload** - Support upload photo, PDF, image  
✅ **Polymorphic** - Review, Transaction support multiple models  
✅ **Production Ready** - Professional code structure

---

## 📞 Common Issues

### Token Expired

```
Response: 401 Unauthorized
Solution: Login again to get new token
```

### Permission Denied

```
Response: 403 Forbidden
Solution: Check if your role has permission for this endpoint
```

### Validation Error

```
Response: 422 Unprocessable
Solution: Check error message and fix request data
```

### Not Found

```
Response: 404 Not Found
Solution: Check if resource ID exists
```

---

## 📝 Notes

-   Token expires in 3600 seconds (1 hour)
-   All protected endpoints require token
-   Use `Authorization: Bearer {token}` header
-   Role checking is automatic via middleware
-   All dates in format: YYYY-MM-DD HH:MM:SS
-   ID in response is always the database ID
