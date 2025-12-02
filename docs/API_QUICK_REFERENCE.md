# API Quick Reference

> **Base URL**: `http://127.0.0.1:8000/api`

## 🔑 Authentication

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/register` | POST | No | Register new user |
| `/login` | POST | No | Login & get token |
| `/auth/google/redirect` | GET | No | Google OAuth redirect |
| `/auth/google/callback` | GET | No | Google OAuth callback |
| `/auth/logout` | POST | Yes | Logout |
| `/auth/refresh` | POST | Yes | Refresh token |
| `/auth/me` | GET | Yes | Get current user |
| `/auth/profile` | GET | Yes | Get full profile |
| `/auth/profile` | PUT | Yes | Update profile |
| `/auth/change-password` | PUT | Yes | Change password |
| `/auth/profile/photo` | POST | Yes | Upload profile photo |
| `/auth/profile/cv` | POST | Yes | Upload CV |
| `/auth/portfolio` | GET | Yes | Get portfolio |
| `/auth/recommendations` | GET | Yes | Get recommendations |
| `/auth/activity-history` | GET | Yes | Get activity history |

## 📚 Courses

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/courses` | GET | No | Get all courses (public) |
| `/courses?type=bootcamp&level=intermediate` | GET | No | Get filtered courses |
| `/courses/{id}` | GET | No | Get single course |
| `/courses/{id}/enroll` | POST | Yes | Enroll to course |
| `/my-courses` | GET | Yes | Get my courses |
| `/enrollments` | GET | Yes | Get all enrollments |
| `/enrollments/{id}` | GET | Yes | Get single enrollment |
| `/enrollments/{id}/progress` | PUT | Yes | Update progress |

## 💳 Subscriptions

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/subscriptions` | GET | Yes | Get all subscriptions |
| `/subscriptions` | POST | Yes | Create subscription |
| `/subscriptions/{id}` | GET | Yes | Get single subscription |
| `/subscriptions/{id}` | PUT | Yes | Update subscription |
| `/subscriptions/{id}/upgrade` | POST | Yes | Upgrade subscription |
| `/subscriptions/{id}` | DELETE | Yes | Delete subscription |

## 🎓 Scholarships

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/scholarships` | GET | No | Get all scholarships |
| `/scholarships/{id}` | GET | No | Get single scholarship |
| `/scholarships/{id}/apply` | POST | Yes | Apply for scholarship |
| `/my-applications` | GET | Yes | Get my applications |

## 🧑‍🏫 Mentoring

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/mentoring-sessions` | GET | Yes | Get all sessions |
| `/mentoring-sessions` | POST | Yes | Create session |
| `/mentoring-sessions/{id}` | GET | Yes | Get single session |
| `/mentoring-sessions/{id}/schedule` | POST | Yes | Schedule session |
| `/mentoring-sessions/{id}/status` | PUT | Yes | Update status |
| `/my-mentoring-sessions` | GET | Yes | Get my sessions |
| `/mentoring-sessions/{id}` | DELETE | Yes | Delete session |

### Need Assessment

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/mentoring-sessions/{id}/need-assessments` | GET | Yes | Get assessment |
| `/mentoring-sessions/{id}/need-assessments` | POST | Yes | Submit assessment |
| `/mentoring-sessions/{id}/need-assessments` | PUT | Yes | Update assessment |
| `/mentoring-sessions/{id}/need-assessments/mark-completed` | PUT | Yes | Mark completed |
| `/mentoring-sessions/{id}/need-assessments` | DELETE | Yes | Delete assessment |

### Coaching Files

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/mentoring-sessions/{id}/coaching-files` | GET | Yes | Get files |
| `/mentoring-sessions/{id}/coaching-files` | POST | Yes | Upload file |
| `/mentoring-sessions/{id}/coaching-files/{fileId}/download` | GET | Yes | Download file |
| `/mentoring-sessions/{id}/coaching-files/{fileId}` | DELETE | Yes | Delete file |

## 🏆 Portfolio

### Achievements

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/achievements` | GET | Yes | Get all achievements |
| `/achievements` | POST | Yes | Create achievement |
| `/achievements/{id}` | GET | Yes | Get single achievement |
| `/achievements/{id}` | PUT | Yes | Update achievement |
| `/achievements/{id}` | DELETE | Yes | Delete achievement |

### Experiences

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/experiences` | GET | Yes | Get all experiences |
| `/experiences` | POST | Yes | Create experience |
| `/experiences/{id}` | GET | Yes | Get single experience |
| `/experiences/{id}` | PUT | Yes | Update experience |
| `/experiences/{id}` | DELETE | Yes | Delete experience |

### Organizations

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/organizations` | GET | Yes | Get all organizations |
| `/organizations` | POST | Yes | Create organization |
| `/organizations/{id}` | GET | Yes | Get single organization |
| `/organizations/{id}` | PUT | Yes | Update organization |
| `/organizations/{id}` | DELETE | Yes | Delete organization |

## 💰 Transactions

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/transactions` | GET | Yes | Get all transactions |
| `/transactions/{id}` | GET | Yes | Get single transaction |
| `/transactions/courses/{courseId}` | POST | Yes | Create course transaction |
| `/transactions/subscriptions` | POST | Yes | Create subscription transaction |
| `/transactions/mentoring-sessions/{sessionId}` | POST | Yes | Create mentoring transaction |
| `/transactions/{id}/payment-proof` | POST | Yes | Upload payment proof |
| `/transactions/{id}/refund` | POST | Yes | Request refund |

## 📰 Articles & Reviews

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/articles` | GET | No | Get all articles |
| `/articles/{id}` | GET | No | Get single article |
| `/reviews` | GET | No | Get all reviews |
| `/reviews` | POST | Yes | Create review |
| `/reviews/{id}` | GET | Yes | Get single review |
| `/reviews/{id}` | PUT | Yes | Update review |
| `/reviews/{id}` | DELETE | Yes | Delete review |

## 👨‍💼 Admin Only

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/admin/users` | GET | Admin | Get all users |
| `/admin/users` | POST | Admin | Create user |
| `/admin/users/{id}` | GET | Admin | Get single user |
| `/admin/users/{id}` | PUT | Admin | Update user |
| `/admin/users/{id}` | DELETE | Admin | Delete user |
| `/courses` | POST | Admin | Create course |
| `/courses/{id}` | PUT | Admin | Update course |
| `/courses/{id}` | DELETE | Admin | Delete course |
| `/transactions/{id}/confirm` | POST | Admin | Confirm payment |
| `/transactions/statistics` | GET | Admin | Get statistics |
| `/corporate-contacts` | GET | Admin | Get all contacts |
| `/corporate-contacts/{id}` | GET | Admin | Get single contact |
| `/corporate-contacts/{id}/status` | PUT | Admin | Update contact status |
| `/corporate-contacts/{id}` | DELETE | Admin | Delete contact |

## 🚀 Quick Start

### 1. Login
```javascript
const response = await fetch('http://127.0.0.1:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'ahmad@example.com',
    password: 'password123'
  })
});

const { data } = await response.json();
localStorage.setItem('token', data.token);
```

### 2. Make Authenticated Request
```javascript
const response = await fetch('http://127.0.0.1:8000/api/auth/me', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`,
    'Accept': 'application/json'
  }
});

const { data } = await response.json();
console.log(data); // User data
```

### 3. Upload File
```javascript
const formData = new FormData();
formData.append('profile_photo', fileInput.files[0]);

const response = await fetch('http://127.0.0.1:8000/api/auth/profile/photo', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`,
    'Accept': 'application/json'
  },
  body: formData
});
```

## 📦 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Success message",
  "data": { /* response data */ }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

## 🔧 HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

## 📝 Common Request Bodies

### Register
```json
{
  "name": "Ahmad Rizki",
  "email": "ahmad@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "student"
}
```

### Create Achievement
```json
{
  "title": "Best Graduate Award",
  "description": "Awarded as the best graduate",
  "date": "2024-08-15",
  "issuer": "Universitas Indonesia"
}
```

### Create Mentoring Session
```json
{
  "mentor_id": 7,
  "member_id": 4,
  "type": "academic",
  "payment_method": "qris",
  "schedule": "2025-12-20 10:00:00"
}
```

### Apply Scholarship
Form Data:
- `cv_path`: File
- `motivation_letter`: File
- `transcript_path`: File (optional)
- `recommendation_path`: File (optional)

---

**For detailed documentation, see**: [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
