# Mentor Coaching Types API

## Masalah yang Diperbaiki
Endpoint `GET /api/mentors` tidak menampilkan informasi tipe coaching (academic vs life coaching) yang dimiliki mentor.

## Solusi
Menggunakan data yang sudah ada di `mentoring_sessions` table (field `type`) untuk menampilkan:
1. **coaching_types**: Array tipe coaching yang tersedia/pernah dilakukan mentor
2. **academic_sessions_count**: Jumlah sesi academic coaching
3. **life_plan_sessions_count**: Jumlah sesi life plan coaching

## Endpoints

### 1. List All Mentors (Updated!)
**Endpoint:** `GET /api/mentors`  
**Authentication:** Required

**Query Parameters:**
- `search` (optional): Cari mentor berdasarkan nama
- `coaching_type` (optional): Filter mentor berdasarkan tipe coaching
  - Nilai: `academic` atau `life_plan`

**Response:**
```json
{
  "success": true,
  "message": "Daftar mentor berhasil diambil",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 5,
        "name": "Dr. Sarah Johnson",
        "email": "sarah@mentor.com",
        "role": "mentor",
        "status": "active",
        "specialization": ["Career Counseling", "Leadership Development"],
        "coaching_types": ["academic", "life_plan"],
        "academic_sessions_count": 15,
        "life_plan_sessions_count": 8,
        "bio": "Career coach with 10 years experience...",
        "profile_photo": "https://...",
        "created_at": "2024-12-01 10:00:00"
      },
      {
        "id": 6,
        "name": "Michael Chen",
        "email": "michael@mentor.com",
        "role": "mentor",
        "status": "active",
        "specialization": ["Academic Writing", "Research Methods"],
        "coaching_types": ["academic"],
        "academic_sessions_count": 25,
        "life_plan_sessions_count": 0,
        "bio": "Academic coach specializing in research...",
        "profile_photo": "https://...",
        "created_at": "2024-12-01 10:00:00"
      }
    ],
    "total": 3
  }
}
```

### 2. Show Mentor Details
**Endpoint:** `GET /api/mentors/{id}`  
**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Detail mentor berhasil diambil",
  "data": {
    "id": 5,
    "name": "Dr. Sarah Johnson",
    "email": "sarah@mentor.com",
    "role": "mentor",
    "status": "active",
    "coaching_types": ["academic", "life_plan"],
    "academic_sessions_count": 15,
    "life_plan_sessions_count": 8,
    "specialization": ["Career Counseling", "Leadership Development"],
    "bio": "Career coach with 10 years experience...",
    "profile_photo": "https://...",
    "phone": "+62 812 3456 7890",
    "institution": "Universitas Indonesia",
    "major": "Psychology",
    "education_level": "S3"
  }
}
```

## Contoh Penggunaan

### 1. Lihat Semua Mentors
```bash
curl -X GET "http://localhost:8000/api/mentors" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Filter Mentor Academic Coaching
```bash
curl -X GET "http://localhost:8000/api/mentors?coaching_type=academic" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Filter Mentor Life Plan Coaching
```bash
curl -X GET "http://localhost:8000/api/mentors?coaching_type=life_plan" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Search Mentor by Name
```bash
curl -X GET "http://localhost:8000/api/mentors?search=Sarah" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. Kombinasi Filter
```bash
curl -X GET "http://localhost:8000/api/mentors?coaching_type=academic&search=Sarah" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Field Penjelasan

| Field | Type | Description |
|-------|------|-------------|
| `coaching_types` | array | Tipe coaching yang tersedia: `['academic', 'life_plan']` atau `['academic']` atau `['life_plan']`. Jika mentor belum punya sesi, default semua tipe tersedia |
| `academic_sessions_count` | integer | Total sesi academic coaching yang pernah dilakukan |
| `life_plan_sessions_count` | integer | Total sesi life plan coaching yang pernah dilakukan |

## Coaching Types Explanation

### Academic Coaching
- Fokus pada pengembangan akademik
- Contoh: Bantuan skripsi, riset, academic writing, study skills
- Session type: `academic`

### Life Plan Coaching  
- Fokus pada perencanaan hidup dan karir
- Contoh: Career guidance, goal setting, work-life balance, personal development
- Session type: `life_plan`

## Logic Implementasi

1. **Data Source**: Field `type` di tabel `mentoring_sessions`
   - Nilai: `academic` atau `life_plan`

2. **Counting**: Menggunakan `withCount()` di query
   ```php
   ->withCount([
       'mentoringSessionsAsMentor as academic_sessions_count' => function($q) {
           $q->where('type', 'academic');
       },
       'mentoringSessionsAsMentor as life_plan_sessions_count' => function($q) {
           $q->where('type', 'life_plan');
       }
   ])
   ```

3. **Coaching Types**: Accessor di Model User
   ```php
   public function getCoachingTypesAttribute()
   {
       // Ambil distinct type dari mentoring sessions
       // Jika belum ada sessions, return semua type
   }
   ```

4. **Filter**: Query berdasarkan sessions yang pernah dilakukan
   ```php
   if (!empty($filters['coaching_type'])) {
       $query->whereHas('mentoringSessionsAsMentor', function($q) use ($coachingType) {
           $q->where('type', $coachingType);
       });
   }
   ```

## Testing

```bash
# Login terlebih dahulu
curl -X POST "http://localhost:8000/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "student@example.com", "password": "password"}'

# Simpan token dari response, lalu:
curl -X GET "http://localhost:8000/api/mentors" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Test filter academic coaching
curl -X GET "http://localhost:8000/api/mentors?coaching_type=academic" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Notes
- **Tidak ada field baru di database**: Menggunakan data existing dari `mentoring_sessions.type`
- **Dynamic**: Coaching types otomatis update berdasarkan sessions yang dilakukan
- **Backward Compatible**: Endpoint lama tetap berfungsi, hanya ditambahkan field baru
- **Filter Optional**: Semua query parameter bersifat opsional
