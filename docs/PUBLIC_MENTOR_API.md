# Public Mentor API - Untuk Landing Page

## 📋 Deskripsi
Endpoint publik untuk menampilkan daftar mentor di landing page. **Tidak memerlukan autentikasi**.

---

## 🔓 Endpoint Publik

### 1. Daftar Mentor
**GET** `/api/mentors`

Menampilkan daftar semua mentor aktif.

#### Query Parameters (Opsional)
- `page` (integer) - Nomor halaman (default: 1)
- `per_page` (integer) - Jumlah item per halaman (default: 15)
- `search` (string) - Cari berdasarkan nama mentor

#### Contoh Request
```bash
# Tanpa parameter
curl http://127.0.0.1:8000/api/mentors

# Dengan pencarian
curl "http://127.0.0.1:8000/api/mentors?search=Budi"

# Dengan pagination
curl "http://127.0.0.1:8000/api/mentors?page=1&per_page=10"
```

#### Contoh Response Success (200)
```json
{
    "sukses": true,
    "pesan": "Daftar mentor berhasil diambil",
    "data": {
        "data": [
            {
                "id": 7,
                "name": "Dr. Budi Santoso",
                "email": "mentor@example.com",
                "role": "mentor",
                "phone": "081234567890",
                "bio": "Expert in Machine Learning with 10 years of experience",
                "profile_photo": "https://example.com/photos/mentor.jpg",
                "status": "active",
                "academic_sessions_count": 15,
                "life_plan_sessions_count": 8
            },
            {
                "id": 12,
                "name": "Prof. Siti Nurhaliza",
                "email": "siti.mentor@example.com",
                "role": "mentor",
                "phone": "081234567891",
                "bio": "Data Science and AI Specialist",
                "profile_photo": null,
                "status": "active",
                "academic_sessions_count": 20,
                "life_plan_sessions_count": 12
            }
        ],
        "current_page": 1,
        "last_page": 2,
        "per_page": 15,
        "total": 25
    }
}
```

---

### 2. Detail Mentor
**GET** `/api/mentors/{id}`

Menampilkan detail profil mentor termasuk achievements, experiences, dan organizations.

#### Path Parameters
- `id` (integer, required) - ID mentor

#### Contoh Request
```bash
curl http://127.0.0.1:8000/api/mentors/7
```

#### Contoh Response Success (200)
```json
{
    "sukses": true,
    "pesan": "Detail mentor berhasil diambil",
    "data": {
        "id": 7,
        "name": "Dr. Budi Santoso",
        "email": "mentor@example.com",
        "role": "mentor",
        "phone": "081234567890",
        "bio": "Expert in Machine Learning with 10 years of experience. Has worked with various tech companies and startups.",
        "profile_photo": "https://example.com/photos/mentor.jpg",
        "status": "active",
        "achievements": [
            {
                "id": 1,
                "title": "Best Mentor Award 2024",
                "description": "Received for outstanding mentoring services",
                "category": "professional"
            }
        ],
        "experiences": [
            {
                "id": 5,
                "type": "work",
                "title": "Senior Data Scientist",
                "organization": "Tech Corp",
                "start_date": "2020-01-01",
                "end_date": null,
                "description": "Leading ML projects"
            }
        ],
        "organizations": [
            {
                "id": 2,
                "name": "Indonesian AI Society",
                "role": "Member",
                "start_date": "2019-06-01"
            }
        ]
    }
}
```

#### Response Error (404)
```json
{
    "sukses": false,
    "pesan": "No query results for model [App\\Models\\User] 999"
}
```

---

## 💻 Implementasi di Frontend

### Vanilla JavaScript
```javascript
// Fetch daftar mentor
async function getMentors(page = 1) {
    try {
        const response = await fetch(`http://127.0.0.1:8000/api/mentors?page=${page}&per_page=6`);
        const data = await response.json();
        
        if (data.sukses) {
            displayMentors(data.data.data);
        }
    } catch (error) {
        console.error('Error fetching mentors:', error);
    }
}

// Fetch detail mentor
async function getMentorDetail(mentorId) {
    try {
        const response = await fetch(`http://127.0.0.1:8000/api/mentors/${mentorId}`);
        const data = await response.json();
        
        if (data.sukses) {
            displayMentorDetail(data.data);
        }
    } catch (error) {
        console.error('Error fetching mentor detail:', error);
    }
}

// Display mentors
function displayMentors(mentors) {
    const container = document.getElementById('mentors-container');
    container.innerHTML = mentors.map(mentor => `
        <div class="mentor-card">
            <img src="${mentor.profile_photo || '/default-avatar.png'}" alt="${mentor.name}">
            <h3>${mentor.name}</h3>
            <p>${mentor.bio || 'Mentor profesional'}</p>
            <div class="stats">
                <span>Academic: ${mentor.academic_sessions_count}</span>
                <span>Life Plan: ${mentor.life_plan_sessions_count}</span>
            </div>
            <button onclick="getMentorDetail(${mentor.id})">Lihat Detail</button>
        </div>
    `).join('');
}
```

### React/Next.js
```jsx
import { useState, useEffect } from 'react';

function MentorsList() {
    const [mentors, setMentors] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchMentors();
    }, []);

    const fetchMentors = async () => {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/mentors');
            const data = await response.json();
            
            if (data.sukses) {
                setMentors(data.data.data);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div className="mentors-grid">
            {mentors.map(mentor => (
                <div key={mentor.id} className="mentor-card">
                    <img 
                        src={mentor.profile_photo || '/default-avatar.png'} 
                        alt={mentor.name} 
                    />
                    <h3>{mentor.name}</h3>
                    <p>{mentor.bio}</p>
                    <div className="stats">
                        <span>🎓 {mentor.academic_sessions_count}</span>
                        <span>💡 {mentor.life_plan_sessions_count}</span>
                    </div>
                </div>
            ))}
        </div>
    );
}
```

### Vue.js
```vue
<template>
  <div class="mentors-section">
    <h2>Meet Our Mentors</h2>
    <div v-if="loading">Loading...</div>
    <div v-else class="mentors-grid">
      <div v-for="mentor in mentors" :key="mentor.id" class="mentor-card">
        <img :src="mentor.profile_photo || '/default-avatar.png'" :alt="mentor.name">
        <h3>{{ mentor.name }}</h3>
        <p>{{ mentor.bio }}</p>
        <div class="stats">
          <span>Academic: {{ mentor.academic_sessions_count }}</span>
          <span>Life Plan: {{ mentor.life_plan_sessions_count }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      mentors: [],
      loading: true
    }
  },
  mounted() {
    this.fetchMentors();
  },
  methods: {
    async fetchMentors() {
      try {
        const response = await fetch('http://127.0.0.1:8000/api/mentors');
        const data = await response.json();
        
        if (data.sukses) {
          this.mentors = data.data.data;
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
```

---

## 🎨 Contoh CSS untuk Mentor Card
```css
.mentors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    padding: 24px;
}

.mentor-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    text-align: center;
}

.mentor-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.mentor-card img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 16px;
}

.mentor-card h3 {
    margin: 12px 0;
    font-size: 20px;
    color: #333;
}

.mentor-card p {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 16px;
}

.mentor-card .stats {
    display: flex;
    justify-content: space-around;
    padding-top: 16px;
    border-top: 1px solid #eee;
}

.mentor-card .stats span {
    font-size: 13px;
    color: #888;
}
```

---

## 📝 Catatan Penting

1. ✅ **Tidak Perlu Token** - Endpoint ini bersifat publik, tidak memerlukan Bearer token
2. ✅ **CORS Enabled** - Pastikan CORS sudah dikonfigurasi di Laravel untuk domain frontend
3. ✅ **Hanya Mentor Aktif** - Endpoint ini hanya menampilkan mentor dengan status "active"
4. ✅ **Caching** - Disarankan menggunakan caching di frontend untuk performa lebih baik
5. ✅ **Rate Limiting** - Endpoint ini mungkin memiliki rate limiting, gunakan dengan bijak

---

## 🔧 Testing dengan Postman

1. Buka Postman
2. Buat request baru dengan method **GET**
3. URL: `http://127.0.0.1:8000/api/mentors`
4. **Tidak perlu** menambahkan Authorization header
5. Klik **Send**

---

## ⚠️ Troubleshooting

### Error: CORS Policy
Jika mendapat error CORS di frontend, pastikan file `config/cors.php` sudah dikonfigurasi:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'], // atau domain spesifik
'allowed_methods' => ['*'],
```

### Error: 404 Not Found
- Pastikan server Laravel sudah running: `php artisan serve`
- Cek route list: `php artisan route:list | grep mentors`

### Error: 500 Internal Server Error
- Cek log Laravel: `storage/logs/laravel.log`
- Pastikan database connection sudah benar

---

## 🚀 Next Steps

Setelah mendapatkan daftar mentor, user bisa:
1. Melihat detail mentor dengan endpoint `/api/mentors/{id}`
2. **Login/Register** untuk booking mentoring session
3. Akses endpoint protected: `/api/mentoring-sessions` (requires auth)

---

**Created**: January 2026  
**Status**: ✅ Active & Public
