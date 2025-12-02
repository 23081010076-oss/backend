# 📚 API Documentation - Learning Platform Backend

> **Base URL**: `http://127.0.0.1:8000/api`  
> **Authentication**: JWT Bearer Token  
> **Content-Type**: `application/json`

---

## 📑 Table of Contents

1. [Authentication](#authentication)
2. [User Profile](#user-profile)
3. [Courses & Enrollment](#courses--enrollment)
4. [Subscriptions](#subscriptions)
5. [Scholarships](#scholarships)
6. [Mentoring Sessions](#mentoring-sessions)
7. [Portfolio (Achievements, Experiences, Organizations)](#portfolio)
8. [Transactions](#transactions)
9. [Articles](#articles)
10. [Reviews](#reviews)
11. [Admin Endpoints](#admin-endpoints)
12. [Error Handling](#error-handling)

---

## 🔐 Authentication

### Register

**Endpoint**: `POST /register`  
**Auth**: None (Public)

**Request Body**:
```json
{
  "name": "Ahmad Rizki",
  "email": "ahmad@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "student",
  "phone": "081234567890",
  "gender": "male",
  "birth_date": "2000-01-15"
}
```

**Response** (201):
```json
{
  "success": true,
  "message": "Pendaftaran berhasil. Silakan login untuk melanjutkan.",
  "data": {
    "id": 1,
    "name": "Ahmad Rizki",
    "email": "ahmad@example.com",
    "role": "student",
    "profile_photo_url": null
  }
}
```

---

### Login

**Endpoint**: `POST /login`  
**Auth**: None (Public)

**Request Body**:
```json
{
  "email": "ahmad@example.com",
  "password": "password123"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmad Rizki",
      "email": "ahmad@example.com",
      "role": "student"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

**Frontend Implementation**:
```javascript
// Login function
async function login(email, password) {
  const response = await fetch('http://127.0.0.1:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Save token to localStorage
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
    return data.data;
  }
  
  throw new Error(data.message);
}
```

---

### Google OAuth Login

**Step 1: Redirect to Google**  
**Endpoint**: `GET /auth/google/redirect`

**Frontend Implementation**:
```javascript
// Redirect user to Google login
window.location.href = 'http://127.0.0.1:8000/api/auth/google/redirect';
```

**Step 2: Handle Callback**  
**Endpoint**: `GET /auth/google/callback`

Google akan redirect ke callback URL dengan token di query parameter.

---

### Logout

**Endpoint**: `POST /auth/logout`  
**Auth**: Required

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Logout berhasil",
  "data": null
}
```

---

### Refresh Token

**Endpoint**: `POST /auth/refresh`  
**Auth**: Required

**Response** (200):
```json
{
  "success": true,
  "message": "Token berhasil diperbarui",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

---

## 👤 User Profile

### Get Current User

**Endpoint**: `GET /auth/me`  
**Auth**: Required

**Response** (200):
```json
{
  "success": true,
  "message": "Data pengguna berhasil diambil",
  "data": {
    "id": 1,
    "name": "Ahmad Rizki",
    "email": "ahmad@example.com",
    "role": "student",
    "phone": "081234567890",
    "institution": "Universitas Indonesia",
    "major": "Computer Science",
    "profile_photo_url": "http://127.0.0.1:8000/storage/profile-photos/xyz.jpg"
  }
}
```

---

### Get Full Profile

**Endpoint**: `GET /auth/profile`  
**Auth**: Required

**Response** (200):
```json
{
  "success": true,
  "data": {
    "user": { /* user data */ },
    "achievements": [ /* array of achievements */ ],
    "experiences": [ /* array of experiences */ ],
    "subscriptions": [ /* array of subscriptions */ ]
  }
}
```

---

### Update Profile

**Endpoint**: `PUT /auth/profile`  
**Auth**: Required

**Request Body**:
```json
{
  "name": "Ahmad Rizki Updated",
  "phone": "081234567890",
  "address": "Jakarta Selatan",
  "institution": "Universitas Indonesia",
  "major": "Computer Science",
  "education_level": "bachelor",
  "bio": "Passionate about technology"
}
```

---

### Change Password

**Endpoint**: `PUT /auth/change-password`  
**Auth**: Required

**Request Body**:
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

### Upload Profile Photo

**Endpoint**: `POST /auth/profile/photo`  
**Auth**: Required  
**Content-Type**: `multipart/form-data`

**Request Body** (Form Data):
- `profile_photo`: File (jpeg, png, jpg, gif, max 2MB)

**Frontend Implementation**:
```javascript
async function uploadProfilePhoto(file) {
  const formData = new FormData();
  formData.append('profile_photo', file);
  
  const response = await fetch('http://127.0.0.1:8000/api/auth/profile/photo', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${localStorage.getItem('token')}`,
      'Accept': 'application/json'
    },
    body: formData
  });
  
  return await response.json();
}
```

---

### Upload CV

**Endpoint**: `POST /auth/profile/cv`  
**Auth**: Required  
**Content-Type**: `multipart/form-data`

**Request Body** (Form Data):
- `cv`: File (pdf, doc, docx, max 2MB)

---

### Get Portfolio

**Endpoint**: `GET /auth/portfolio`  
**Auth**: Required

**Response** (200):
```json
{
  "success": true,
  "data": {
    "profile": { /* user data */ },
    "prestasi": [ /* achievements */ ],
    "pengalaman": [ /* experiences */ ],
    "organisasi": [ /* organizations */ ],
    "kursus": [ /* enrollments */ ],
    "lamaran_beasiswa": [ /* scholarship applications */ ],
    "sesi_mentoring": {
      "sebagai_murid": [ /* sessions as student */ ],
      "sebagai_mentor": [ /* sessions as mentor */ ]
    },
    "langganan": [ /* subscriptions */ ]
  }
}
```

---

### Get Recommendations

**Endpoint**: `GET /auth/recommendations`  
**Auth**: Required

Returns recommended courses based on user's major.

---

### Get Activity History

**Endpoint**: `GET /auth/activity-history`  
**Auth**: Required

**Response** (200):
```json
{
  "success": true,
  "data": {
    "ringkasan": {
      "kursus_selesai": 5,
      "kursus_sedang_diambil": 2,
      "mentoring_selesai": 3,
      "lamaran_beasiswa": 2,
      "jumlah_prestasi": 4,
      "jumlah_pengalaman": 3,
      "jumlah_organisasi": 2
    },
    "terbaru": {
      "kursus_terbaru": [ /* latest 5 enrollments */ ],
      "lamaran_terbaru": [ /* latest 5 applications */ ],
      "mentoring_terbaru": [ /* latest 5 sessions */ ]
    }
  }
}
```

---

## 📚 Courses & Enrollment

### Get All Courses (Public)

**Endpoint**: `GET /courses`  
**Auth**: None (Public)

**Query Parameters**:
- `type`: Filter by type (e-learning, bootcamp)
- `level`: Filter by level (beginner, intermediate, advanced)
- `search`: Search in title/description

**Example**: `GET /courses?type=bootcamp&level=intermediate`

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Full Stack Web Development",
      "description": "Learn full stack development",
      "type": "bootcamp",
      "level": "intermediate",
      "price": 2500000,
      "duration": "12 weeks",
      "instructor": "John Doe",
      "video_url": "https://example.com/video",
      "total_videos": 50
    }
  ]
}
```

---

### Get Single Course

**Endpoint**: `GET /courses/{id}`  
**Auth**: None (Public)

---

### Enroll to Course

**Endpoint**: `POST /courses/{id}/enroll`  
**Auth**: Required

**Response** (201):
```json
{
  "success": true,
  "message": "Berhasil mendaftar ke kursus",
  "data": {
    "id": 1,
    "user_id": 4,
    "course_id": 2,
    "progress": 0,
    "completed": false,
    "enrolled_at": "2025-12-02T10:00:00.000000Z"
  }
}
```

---

### Get My Courses

**Endpoint**: `GET /my-courses`  
**Auth**: Required

Returns all courses the authenticated user is enrolled in.

---

### Get All Enrollments

**Endpoint**: `GET /enrollments`  
**Auth**: Required

---

### Get Single Enrollment

**Endpoint**: `GET /enrollments/{id}`  
**Auth**: Required

---

### Update Enrollment Progress

**Endpoint**: `PUT /enrollments/{id}/progress`  
**Auth**: Required

**Request Body**:
```json
{
  "progress": 75,
  "completed": false
}
```

---

## 💳 Subscriptions

### Get All Subscriptions

**Endpoint**: `GET /subscriptions`  
**Auth**: Required

---

### Create Subscription

**Endpoint**: `POST /subscriptions`  
**Auth**: Required

**Request Body**:
```json
{
  "plan": "premium",
  "package_type": "single_course",
  "duration": 3,
  "duration_unit": "months",
  "courses_ids": [2],
  "price": 500000,
  "auto_renew": false,
  "start_date": "2025-12-02",
  "status": "active"
}
```

---

### Get Single Subscription

**Endpoint**: `GET /subscriptions/{id}`  
**Auth**: Required

---

### Update Subscription

**Endpoint**: `PUT /subscriptions/{id}`  
**Auth**: Required

**Request Body**:
```json
{
  "auto_renew": false
}
```

---

### Upgrade Subscription

**Endpoint**: `POST /subscriptions/{id}/upgrade`  
**Auth**: Required

**Request Body**:
```json
{
  "new_plan": "premium",
  "payment_method": "qris"
}
```

---

### Delete Subscription

**Endpoint**: `DELETE /subscriptions/{id}`  
**Auth**: Required

---

## 🎓 Scholarships

### Get All Scholarships (Public)

**Endpoint**: `GET /scholarships`  
**Auth**: None (Public)

**Query Parameters**:
- `status`: Filter by status (open, closed)
- `location`: Filter by location

---

### Get Single Scholarship

**Endpoint**: `GET /scholarships/{id}`  
**Auth**: None (Public)

---

### Apply for Scholarship

**Endpoint**: `POST /scholarships/{id}/apply`  
**Auth**: Required  
**Content-Type**: `multipart/form-data`

**Request Body** (Form Data):
- `cv_path`: File (required)
- `motivation_letter`: File (required)
- `transcript_path`: File (optional)
- `recommendation_path`: File (optional)

---

### Get My Applications

**Endpoint**: `GET /my-applications`  
**Auth**: Required

Returns all scholarship applications for the authenticated user.

---

## 🧑‍🏫 Mentoring Sessions

### Get All Mentoring Sessions

**Endpoint**: `GET /mentoring-sessions`  
**Auth**: Required

---

### Create Mentoring Session

**Endpoint**: `POST /mentoring-sessions`  
**Auth**: Required

**Request Body**:
```json
{
  "mentor_id": 7,
  "member_id": 4,
  "type": "academic",
  "payment_method": "qris",
  "schedule": "2025-12-20 10:00:00",
  "status": "pending"
}
```

**Types**: `academic`, `life_plan`

---

### Get Single Mentoring Session

**Endpoint**: `GET /mentoring-sessions/{id}`  
**Auth**: Required

---

### Schedule Mentoring Session

**Endpoint**: `POST /mentoring-sessions/{id}/schedule`  
**Auth**: Required (Mentor)

**Request Body**:
```json
{
  "schedule": "2025-12-15 14:00:00",
  "meeting_link": "https://zoom.us/j/123456789"
}
```

---

### Update Session Status

**Endpoint**: `PUT /mentoring-sessions/{id}/status`  
**Auth**: Required (Mentor)

**Request Body**:
```json
{
  "status": "completed"
}
```

**Status Options**: `pending`, `scheduled`, `ongoing`, `completed`, `cancelled`

---

### Get My Mentoring Sessions

**Endpoint**: `GET /my-mentoring-sessions`  
**Auth**: Required

---

### Delete Mentoring Session

**Endpoint**: `DELETE /mentoring-sessions/{id}`  
**Auth**: Required

---

### Need Assessment

#### Get Need Assessment

**Endpoint**: `GET /mentoring-sessions/{sessionId}/need-assessments`  
**Auth**: Required

---

#### Submit Need Assessment

**Endpoint**: `POST /mentoring-sessions/{sessionId}/need-assessments`  
**Auth**: Required (Student)

**Request Body**:
```json
{
  "form_data": {
    "current_situation": "3rd year CS student struggling with career direction",
    "goals": "Become a software engineer at top tech company",
    "challenges": ["Lack of experience", "Interview preparation"],
    "expectations": "Guidance on portfolio and interviews"
  }
}
```

---

#### Update Need Assessment

**Endpoint**: `PUT /mentoring-sessions/{sessionId}/need-assessments`  
**Auth**: Required (Student)

---

#### Mark Assessment Completed

**Endpoint**: `PUT /mentoring-sessions/{sessionId}/need-assessments/mark-completed`  
**Auth**: Required (Mentor)

---

#### Delete Need Assessment

**Endpoint**: `DELETE /mentoring-sessions/{sessionId}/need-assessments`  
**Auth**: Required

---

### Coaching Files

#### Get Coaching Files

**Endpoint**: `GET /mentoring-sessions/{sessionId}/coaching-files`  
**Auth**: Required

---

#### Upload Coaching File

**Endpoint**: `POST /mentoring-sessions/{sessionId}/coaching-files`  
**Auth**: Required (Mentor)  
**Content-Type**: `multipart/form-data`

**Request Body** (Form Data):
- `file`: File (required)
- `file_name`: String (required)

---

#### Download Coaching File

**Endpoint**: `GET /mentoring-sessions/{sessionId}/coaching-files/{fileId}/download`  
**Auth**: Required

---

#### Delete Coaching File

**Endpoint**: `DELETE /mentoring-sessions/{sessionId}/coaching-files/{fileId}`  
**Auth**: Required (Mentor)

---

## 🏆 Portfolio

### Achievements

#### Get All Achievements

**Endpoint**: `GET /achievements`  
**Auth**: Required

---

#### Create Achievement

**Endpoint**: `POST /achievements`  
**Auth**: Required

**Request Body**:
```json
{
  "title": "Best Graduate Award",
  "description": "Awarded as the best graduate in CS department",
  "date": "2024-08-15",
  "issuer": "Universitas Indonesia",
  "certificate_url": "https://example.com/cert.pdf"
}
```

---

#### Get Single Achievement

**Endpoint**: `GET /achievements/{id}`  
**Auth**: Required

---

#### Update Achievement

**Endpoint**: `PUT /achievements/{id}`  
**Auth**: Required

---

#### Delete Achievement

**Endpoint**: `DELETE /achievements/{id}`  
**Auth**: Required

---

### Experiences

#### Get All Experiences

**Endpoint**: `GET /experiences`  
**Auth**: Required

---

#### Create Experience

**Endpoint**: `POST /experiences`  
**Auth**: Required

**Request Body**:
```json
{
  "type": "internship",
  "title": "Software Engineer Intern",
  "company": "Google Indonesia",
  "level": "Junior",
  "start_date": "2024-06-01",
  "end_date": "2024-08-31",
  "description": "Developed backend services using Java"
}
```

**Types**: `internship`, `full_time`, `part_time`, `volunteer`, `freelance`

---

#### Get Single Experience

**Endpoint**: `GET /experiences/{id}`  
**Auth**: Required

---

#### Update Experience

**Endpoint**: `PUT /experiences/{id}`  
**Auth**: Required

---

#### Delete Experience

**Endpoint**: `DELETE /experiences/{id}`  
**Auth**: Required

---

### Organizations

#### Get All Organizations

**Endpoint**: `GET /organizations`  
**Auth**: Required

---

#### Get Single Organization

**Endpoint**: `GET /organizations/{id}`  
**Auth**: Required

---

#### Create Organization

**Endpoint**: `POST /organizations`  
**Auth**: Required

**Request Body**:
```json
{
  "name": "Tech Student Association",
  "type": "student_organization",
  "description": "A community for tech students",
  "location": "Jakarta, Indonesia",
  "website": "https://example.com",
  "contact_email": "contact@example.com",
  "phone": "021-1234567",
  "founded_year": 2020
}
```

---

#### Update Organization

**Endpoint**: `PUT /organizations/{id}`  
**Auth**: Required

---

#### Delete Organization

**Endpoint**: `DELETE /organizations/{id}`  
**Auth**: Required

---

## 💰 Transactions

### Get All Transactions

**Endpoint**: `GET /transactions`  
**Auth**: Required

Returns all transactions for the authenticated user.

---

### Get Single Transaction

**Endpoint**: `GET /transactions/{id}`  
**Auth**: Required

---

### Create Transaction for Course

**Endpoint**: `POST /transactions/courses/{courseId}`  
**Auth**: Required

**Request Body**:
```json
{
  "payment_method": "qris"
}
```

**Payment Methods**: `qris`, `bank_transfer`, `credit_card`, `e_wallet`

**Response** (201):
```json
{
  "success": true,
  "data": {
    "transaction": { /* transaction data */ },
    "snap_token": "abc123...",
    "redirect_url": "https://app.midtrans.com/snap/v2/vtweb/abc123"
  }
}
```

---

### Create Transaction for Subscription

**Endpoint**: `POST /transactions/subscriptions`  
**Auth**: Required

**Request Body**:
```json
{
  "plan": "premium",
  "package_type": "all_in_one",
  "duration": 12,
  "duration_unit": "months",
  "payment_method": "bank_transfer"
}
```

---

### Create Transaction for Mentoring

**Endpoint**: `POST /transactions/mentoring-sessions/{sessionId}`  
**Auth**: Required

**Request Body**:
```json
{
  "payment_method": "qris"
}
```

---

### Upload Payment Proof

**Endpoint**: `POST /transactions/{id}/payment-proof`  
**Auth**: Required  
**Content-Type**: `multipart/form-data`

**Request Body** (Form Data):
- `payment_proof`: File (required)

---

### Request Refund

**Endpoint**: `POST /transactions/{id}/refund`  
**Auth**: Required

**Request Body**:
```json
{
  "reason": "Course tidak sesuai dengan ekspektasi"
}
```

---

## 📰 Articles

### Get All Articles (Public)

**Endpoint**: `GET /articles`  
**Auth**: None (Public)

**Query Parameters**:
- `category`: Filter by category

---

### Get Single Article

**Endpoint**: `GET /articles/{id}`  
**Auth**: None (Public)

---

## ⭐ Reviews

### Get All Reviews (Public)

**Endpoint**: `GET /reviews`  
**Auth**: None (Public)

---

### Create Review

**Endpoint**: `POST /reviews`  
**Auth**: Required

**Request Body**:
```json
{
  "reviewable_type": "App\\Models\\Scholarship",
  "reviewable_id": 1,
  "rating": 5,
  "comment": "Excellent scholarship program!"
}
```

**Reviewable Types**:
- `App\\Models\\Scholarship`
- `App\\Models\\Organization`
- `App\\Models\\Course`

---

### Get Single Review

**Endpoint**: `GET /reviews/{id}`  
**Auth**: Required

---

### Update Review

**Endpoint**: `PUT /reviews/{id}`  
**Auth**: Required

---

### Delete Review

**Endpoint**: `DELETE /reviews/{id}`  
**Auth**: Required

---

## 👨‍💼 Admin Endpoints

### User Management

#### Get All Users

**Endpoint**: `GET /admin/users`  
**Auth**: Required (Admin)

---

#### Get Single User

**Endpoint**: `GET /admin/users/{id}`  
**Auth**: Required (Admin)

---

#### Create User

**Endpoint**: `POST /admin/users`  
**Auth**: Required (Admin)

---

#### Update User

**Endpoint**: `PUT /admin/users/{id}`  
**Auth**: Required (Admin)

---

#### Delete User

**Endpoint**: `DELETE /admin/users/{id}`  
**Auth**: Required (Admin)

---

### Course Management

#### Create Course

**Endpoint**: `POST /courses`  
**Auth**: Required (Admin)

**Request Body**:
```json
{
  "title": "Full Stack Web Development Bootcamp",
  "description": "Comprehensive bootcamp covering frontend and backend",
  "type": "bootcamp",
  "level": "intermediate",
  "duration": "12 weeks",
  "price": 2500000,
  "access_type": "premium",
  "instructor": "John Doe",
  "video_url": "https://example.com/video/fullstack",
  "video_duration": "120:00:00",
  "total_videos": 50
}
```

---

#### Update Course

**Endpoint**: `PUT /courses/{id}`  
**Auth**: Required (Admin)

---

#### Delete Course

**Endpoint**: `DELETE /courses/{id}`  
**Auth**: Required (Admin)

---

### Transaction Management

#### Confirm Payment

**Endpoint**: `POST /transactions/{id}/confirm`  
**Auth**: Required (Admin)

**Request Body**:
```json
{
  "status": "paid"
}
```

---

#### Get Transaction Statistics

**Endpoint**: `GET /transactions/statistics`  
**Auth**: Required (Admin)

**Response** (200):
```json
{
  "success": true,
  "data": {
    "total_revenue": 50000000,
    "total_transactions": 150,
    "pending_transactions": 10,
    "completed_transactions": 130,
    "failed_transactions": 10,
    "revenue_by_type": {
      "course": 20000000,
      "subscription": 25000000,
      "mentoring": 5000000
    }
  }
}
```

---

### Corporate Contact Management

#### Get All Contacts

**Endpoint**: `GET /corporate-contacts`  
**Auth**: Required (Admin)

---

#### Get Single Contact

**Endpoint**: `GET /corporate-contacts/{id}`  
**Auth**: Required (Admin)

---

#### Update Contact Status

**Endpoint**: `PUT /corporate-contacts/{id}/status`  
**Auth**: Required (Admin)

**Request Body**:
```json
{
  "status": "contacted"
}
```

**Status Options**: `pending`, `contacted`, `completed`

---

#### Delete Contact

**Endpoint**: `DELETE /corporate-contacts/{id}`  
**Auth**: Required (Admin)

---

## 🚨 Error Handling

### Standard Error Response

All errors follow this format:

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": ["Error detail 1", "Error detail 2"]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized (Invalid/Missing Token) |
| 403 | Forbidden (No Permission) |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

### Common Errors

#### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

**Solution**: Include valid Bearer token in Authorization header.

---

#### 422 Validation Error
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## 🔧 Frontend Integration Guide

### Setup Axios Interceptor

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request interceptor - add token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor - handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Token expired, try refresh
      try {
        const { data } = await axios.post(
          'http://127.0.0.1:8000/api/auth/refresh',
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          }
        );
        
        localStorage.setItem('token', data.data.token);
        
        // Retry original request
        error.config.headers.Authorization = `Bearer ${data.data.token}`;
        return axios(error.config);
      } catch (refreshError) {
        // Refresh failed, logout user
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      }
    }
    
    return Promise.reject(error);
  }
);

export default api;
```

---

### Usage Examples

#### Get Courses
```javascript
import api from './api';

async function getCourses(filters = {}) {
  try {
    const { data } = await api.get('/courses', { params: filters });
    return data.data;
  } catch (error) {
    console.error('Error fetching courses:', error);
    throw error;
  }
}

// Usage
const courses = await getCourses({ type: 'bootcamp', level: 'intermediate' });
```

---

#### Create Achievement
```javascript
async function createAchievement(achievementData) {
  try {
    const { data } = await api.post('/achievements', achievementData);
    return data.data;
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation errors
      console.error('Validation errors:', error.response.data.errors);
    }
    throw error;
  }
}

// Usage
const achievement = await createAchievement({
  title: 'Best Graduate Award',
  description: 'Awarded as the best graduate',
  date: '2024-08-15',
  issuer: 'Universitas Indonesia'
});
```

---

#### Upload File
```javascript
async function uploadProfilePhoto(file) {
  const formData = new FormData();
  formData.append('profile_photo', file);
  
  try {
    const { data } = await api.post('/auth/profile/photo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    return data.data;
  } catch (error) {
    console.error('Error uploading photo:', error);
    throw error;
  }
}

// Usage in React
function ProfilePhotoUpload() {
  const handleFileChange = async (e) => {
    const file = e.target.files[0];
    if (file) {
      const result = await uploadProfilePhoto(file);
      console.log('Photo uploaded:', result.profile_photo_url);
    }
  };
  
  return <input type="file" onChange={handleFileChange} accept="image/*" />;
}
```

---

## 📝 Notes

1. **All timestamps** are in ISO 8601 format: `YYYY-MM-DDTHH:mm:ss.000000Z`
2. **File uploads** require `Content-Type: multipart/form-data`
3. **Token expiration**: Default is 60 minutes, use refresh endpoint before expiry
4. **Rate limiting**: Some endpoints have rate limiting (login, register, uploads)
5. **Pagination**: Some list endpoints support pagination with `page` and `per_page` query params

---

## 🔗 Additional Resources

- **Postman Collection**: Import `Student_App_Flow.postman_collection.json` for testing
- **Base URL**: `http://127.0.0.1:8000/api`
- **Environment Variables**: Set `baseUrl` in your frontend config

---

**Last Updated**: December 2, 2025  
**API Version**: 1.0
