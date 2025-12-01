# 🚀 PANDUAN INTEGRASI API

Dokumentasi lengkap untuk integrasi frontend (React, Vue, Flutter, React Native, dll) dengan backend Laravel ini.

---

## 📋 Daftar Isi

1. [Informasi Dasar](#-informasi-dasar)
2. [Authentication](#-authentication)
3. [Cara Memanggil API](#-cara-memanggil-api)
4. [Contoh Integrasi](#-contoh-integrasi)
5. [Daftar Endpoint](#-daftar-endpoint-lengkap)
6. [Error Handling](#-error-handling)
7. [Tips & Best Practice](#-tips--best-practice)

---

## 📌 Informasi Dasar

### Base URL
```
Development: http://localhost:8000/api
Production:  https://your-domain.com/api
```

### Headers Wajib
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // Untuk endpoint yang butuh login
```

### Format Response Standar

```json
// ✅ Response Sukses
{
  "sukses": true,
  "pesan": "Data berhasil diambil",
  "data": { ... }
}

// ❌ Response Error
{
  "sukses": false,
  "pesan": "Terjadi kesalahan",
  "data": null,
  "errors": { ... }  // Optional: detail error
}

// 📄 Response dengan Pagination
{
  "sukses": true,
  "pesan": "Daftar data",
  "data": [ ... ],
  "meta": {
    "total": 100,
    "per_halaman": 10,
    "halaman_sekarang": 1,
    "halaman_terakhir": 10,
    "dari": 1,
    "sampai": 10
  }
}
```

---

## 🔐 Authentication

### 1. Register (Daftar Akun)

```http
POST /api/register
```

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "student",
  "phone": "081234567890",
  "gender": "male",
  "birth_date": "2000-01-15"
}
```

**Response (201):**
```json
{
  "sukses": true,
  "pesan": "Pendaftaran berhasil. Silakan login untuk melanjutkan.",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "student"
  }
}
```

---

### 2. Login

```http
POST /api/login
```

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "sukses": true,
  "pesan": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

**⚠️ PENTING:** Simpan token untuk digunakan di request selanjutnya!

---

### 3. Get Current User (Me)

```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "sukses": true,
  "pesan": "Data pengguna berhasil diambil",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "student",
    "phone": "081234567890",
    "profile_photo": "profile-photos/abc123.jpg"
  }
}
```

---

### 4. Logout

```http
POST /api/auth/logout
Authorization: Bearer {token}
```

---

### 5. Refresh Token

```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

**Response:** Token baru dengan masa berlaku baru.

---

## 💻 Cara Memanggil API

### JavaScript (Fetch)

```javascript
// Tanpa auth (public)
const response = await fetch('http://localhost:8000/api/courses');
const data = await response.json();

// Dengan auth
const response = await fetch('http://localhost:8000/api/auth/me', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});
```

### Axios (Recommended)

```javascript
import axios from 'axios';

// Setup instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token interceptor
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Usage
const { data } = await api.get('/courses');
const { data } = await api.post('/login', { email, password });
```

---

## 📱 Contoh Integrasi

### React dengan Context + Axios

```jsx
// src/context/AuthContext.jsx
import { createContext, useState, useContext, useEffect } from 'react';
import api from '../services/api';

const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      loadUser();
    } else {
      setLoading(false);
    }
  }, []);

  const loadUser = async () => {
    try {
      const { data } = await api.get('/auth/me');
      setUser(data.data);
    } catch (error) {
      localStorage.removeItem('token');
    } finally {
      setLoading(false);
    }
  };

  const login = async (email, password) => {
    const { data } = await api.post('/login', { email, password });
    localStorage.setItem('token', data.data.token);
    setUser(data.data.user);
    return data;
  };

  const logout = async () => {
    await api.post('/auth/logout');
    localStorage.removeItem('token');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);
```

```jsx
// src/services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

---

### Vue 3 dengan Pinia

```javascript
// stores/auth.js
import { defineStore } from 'pinia';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  
  actions: {
    async login(email, password) {
      const { data } = await api.post('/login', { email, password });
      this.token = data.data.token;
      this.user = data.data.user;
      localStorage.setItem('token', this.token);
    },
    
    async logout() {
      await api.post('/auth/logout');
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
    },
    
    async fetchUser() {
      const { data } = await api.get('/auth/me');
      this.user = data.data;
    },
  },
});
```

---

### Flutter (Dart)

```dart
// lib/services/api_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android emulator
  
  static Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }
  
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );
    
    final data = jsonDecode(response.body);
    
    if (data['sukses']) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', data['data']['token']);
    }
    
    return data;
  }
  
  static Future<Map<String, dynamic>> getUser() async {
    final response = await http.get(
      Uri.parse('$baseUrl/auth/me'),
      headers: await _getHeaders(),
    );
    return jsonDecode(response.body);
  }
}
```

---

### React Native (Expo)

```javascript
// services/api.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'http://192.168.1.x:8000/api', // Ganti dengan IP komputer
});

api.interceptors.request.use(async config => {
  const token = await AsyncStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const login = async (email, password) => {
  const { data } = await api.post('/login', { email, password });
  if (data.sukses) {
    await AsyncStorage.setItem('token', data.data.token);
  }
  return data;
};

export const logout = async () => {
  await api.post('/auth/logout');
  await AsyncStorage.removeItem('token');
};

export default api;
```

---

## 📚 Daftar Endpoint Lengkap

### 🔓 Public Endpoints (Tanpa Login)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/register` | Daftar akun baru |
| POST | `/login` | Login |
| GET | `/courses` | Daftar kursus |
| GET | `/courses/{id}` | Detail kursus |
| GET | `/scholarships` | Daftar beasiswa |
| GET | `/scholarships/{id}` | Detail beasiswa |
| GET | `/articles` | Daftar artikel |
| GET | `/articles/{id}` | Detail artikel |
| GET | `/reviews` | Daftar review |
| POST | `/corporate-contact` | Kirim inquiry corporate |

---

### 🔐 Protected Endpoints (Perlu Login)

#### Authentication & Profile
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/logout` | Logout |
| GET | `/auth/me` | Data user login |
| POST | `/auth/refresh` | Refresh token |
| PUT | `/auth/change-password` | Ganti password |
| GET | `/auth/profile` | Profil lengkap |
| PUT | `/auth/profile` | Update profil |
| POST | `/auth/profile/photo` | Upload foto |
| POST | `/auth/profile/cv` | Upload CV |
| GET | `/auth/portfolio` | Portofolio lengkap |
| GET | `/auth/activity-history` | Riwayat aktivitas |
| GET | `/auth/recommendations` | Rekomendasi kursus |

#### Achievements
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/achievements` | Daftar prestasi |
| POST | `/achievements` | Tambah prestasi |
| GET | `/achievements/{id}` | Detail prestasi |
| PUT | `/achievements/{id}` | Update prestasi |
| DELETE | `/achievements/{id}` | Hapus prestasi |

#### Experiences
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/experiences` | Daftar pengalaman |
| POST | `/experiences` | Tambah pengalaman |
| GET | `/experiences/{id}` | Detail pengalaman |
| PUT | `/experiences/{id}` | Update pengalaman |
| DELETE | `/experiences/{id}` | Hapus pengalaman |

#### Organizations
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/organizations` | Daftar organisasi |
| POST | `/organizations` | Tambah organisasi |
| GET | `/organizations/{id}` | Detail organisasi |
| PUT | `/organizations/{id}` | Update organisasi |
| DELETE | `/organizations/{id}` | Hapus organisasi |

#### Enrollments (Pendaftaran Kursus)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/enrollments` | Daftar enrollment |
| POST | `/courses/{id}/enroll` | Daftar ke kursus |
| GET | `/my-courses` | Kursus yang diikuti |
| PUT | `/enrollments/{id}/progress` | Update progress |

#### Scholarships (Beasiswa)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/scholarships/{id}/apply` | Lamar beasiswa |
| GET | `/my-applications` | Lamaran saya |

#### Mentoring Sessions
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/mentoring-sessions` | Daftar sesi |
| POST | `/mentoring-sessions` | Buat sesi baru |
| GET | `/my-mentoring-sessions` | Sesi mentoring saya |
| PUT | `/mentoring-sessions/{id}/status` | Update status |

#### Subscriptions (Langganan)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/subscriptions` | Daftar langganan |
| POST | `/subscriptions` | Buat langganan |
| POST | `/subscriptions/{id}/upgrade` | Upgrade paket |

#### Transactions (Pembayaran)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/transactions` | Daftar transaksi |
| GET | `/transactions/{id}` | Detail transaksi |
| POST | `/transactions/courses/{id}` | Bayar kursus |
| POST | `/transactions/subscriptions` | Bayar langganan |
| POST | `/transactions/{id}/payment-proof` | Upload bukti bayar |

#### Reviews
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/reviews` | Tulis review |
| PUT | `/reviews/{id}` | Update review |
| DELETE | `/reviews/{id}` | Hapus review |

---

### 👑 Admin Only Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/admin/users` | Daftar semua user |
| POST | `/admin/users` | Buat user baru |
| PUT | `/admin/users/{id}` | Update user |
| DELETE | `/admin/users/{id}` | Hapus user |
| POST | `/courses` | Buat kursus |
| PUT | `/courses/{id}` | Update kursus |
| DELETE | `/courses/{id}` | Hapus kursus |
| POST | `/transactions/{id}/confirm` | Konfirmasi pembayaran |

---

## ⚠️ Error Handling

### HTTP Status Codes

| Code | Arti | Kapan Terjadi |
|------|------|---------------|
| **200** | OK | Request berhasil |
| **201** | Created | Data berhasil dibuat |
| **400** | Bad Request | Request tidak valid |
| **401** | Unauthorized | Belum login / token expired |
| **403** | Forbidden | Tidak punya akses |
| **404** | Not Found | Data tidak ditemukan |
| **422** | Validation Error | Validasi gagal |
| **500** | Server Error | Error di server |

### Contoh Handle Error (React)

```javascript
try {
  const { data } = await api.post('/login', { email, password });
  
  if (data.sukses) {
    // Login berhasil
    localStorage.setItem('token', data.data.token);
  }
} catch (error) {
  if (error.response) {
    const { status, data } = error.response;
    
    switch (status) {
      case 401:
        alert('Email atau password salah');
        break;
      case 422:
        // Validation error
        console.log(data.errors);
        // { email: ['Email sudah terdaftar'], password: ['Min 8 karakter'] }
        break;
      case 500:
        alert('Terjadi kesalahan server');
        break;
      default:
        alert(data.pesan || 'Terjadi kesalahan');
    }
  } else {
    alert('Tidak dapat terhubung ke server');
  }
}
```

---

## 💡 Tips & Best Practice

### 1. Environment Variables

```bash
# .env (React/Vite)
VITE_API_URL=http://localhost:8000/api

# .env (React Native)
API_URL=http://192.168.1.100:8000/api
```

### 2. Token Storage

| Platform | Recommended Storage |
|----------|---------------------|
| Web | `localStorage` atau `httpOnly cookies` |
| React Native | `AsyncStorage` atau `SecureStore` |
| Flutter | `SharedPreferences` atau `FlutterSecureStorage` |

### 3. Auto Refresh Token

```javascript
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401 && !error.config._retry) {
      error.config._retry = true;
      
      try {
        const { data } = await api.post('/auth/refresh');
        localStorage.setItem('token', data.data.token);
        error.config.headers.Authorization = `Bearer ${data.data.token}`;
        return api(error.config);
      } catch (refreshError) {
        localStorage.removeItem('token');
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
```

### 4. File Upload

```javascript
// Upload foto profil
const formData = new FormData();
formData.append('photo', file); // file dari input type="file"

const { data } = await api.post('/auth/profile/photo', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
});
```

### 5. Pagination

```javascript
// Ambil halaman tertentu
const { data } = await api.get('/courses', {
  params: {
    page: 2,
    per_page: 10,
    search: 'laravel',
    level: 'beginner'
  }
});

// Response
// data.data = array of courses
// data.meta.total = total semua data
// data.meta.halaman_terakhir = last page number
```

---

## 🔧 Testing dengan Postman/Insomnia

### Collection Variables
```
base_url: http://localhost:8000/api
token: (kosongkan, isi setelah login)
```

### Pre-request Script (Auto set token)
```javascript
// Setelah login, set token otomatis
if (pm.response.json().sukses && pm.response.json().data.token) {
    pm.collectionVariables.set('token', pm.response.json().data.token);
}
```

---

## 📞 Kontak & Support

Jika ada pertanyaan tentang integrasi API:
1. Baca dokumentasi ini dengan teliti
2. Cek error message dari response
3. Pastikan token valid dan tidak expired

**Happy Coding! 🎉**
