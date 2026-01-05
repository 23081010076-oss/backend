# Sistem Rekomendasi Course

## Overview
Sistem rekomendasi course yang menggunakan data profile user (specialization dan major) untuk memberikan rekomendasi course yang relevan.

## Cara Kerja

### 1. Data Profile yang Digunakan
- **Specialization** (Minat): Array berisi minat/keahlian user (contoh: ["Web Development", "Machine Learning", "UI/UX Design"])
- **Major** (Jurusan): String berisi jurusan pendidikan user (contoh: "Teknik Informatika")

### 2. Algoritma Scoring

Sistem menggunakan **relevance scoring** dengan prioritas berikut:

#### Prioritas Tertinggi: Specialization (150-80 poin)
- Match di **title course**: 150 poin (specialization pertama) - 140 poin (kedua) - dst
- Match di **category**: 100 poin - 90 poin - dst
- Match di **description**: 80 poin - 70 poin - dst

#### Prioritas Menengah: Major (60-30 poin)
- Match di **title course**: 60 poin
- Match di **description**: 40 poin
- Match di **category**: 30 poin

#### Prioritas Rendah: Rating & Popularity
- **Average rating** dari reviews
- **Enrollment count** (jumlah user yang enroll)

### 3. Filter Subscription
Hanya menampilkan course yang bisa diakses sesuai subscription plan user:
- **Free**: Hanya course dengan `access_type = 'free'`
- **Regular**: Course dengan `access_type = 'free'` atau `'regular'`
- **Premium**: Semua course

### 4. Exclude Enrolled Courses
Course yang sudah di-enroll oleh user tidak akan muncul di rekomendasi.

## Endpoint API

### GET /api/auth/recommendations

**Headers:**
```
Authorization: Bearer {jwt_token}
```

**Query Parameters:**
```
limit: integer (default: 5) - Jumlah maksimal rekomendasi
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Rekomendasi kursus berhasil diambil",
  "data": {
    "recommendations": [
      {
        "id": 1,
        "title": "Web Development Bootcamp",
        "category": "Programming",
        "description": "...",
        "price": 500000,
        "access_type": "regular",
        "relevance_score": 150,
        "reviews_avg_rating": 4.8,
        "enrollments_count": 245
      }
    ],
    "criteria": {
      "subscription_plan": "regular",
      "specializations": ["Web Development", "UI/UX Design"],
      "major": "Teknik Informatika",
      "excluded_enrolled": 3,
      "algorithm": "specialization_score + major_score + rating + popularity"
    }
  }
}
```

## Cara Update Profile (Specialization & Major)

### PUT /api/auth/profile

**Headers:**
```
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "major": "Teknik Informatika",
  "specialization": [
    "Web Development",
    "Machine Learning",
    "UI/UX Design"
  ]
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Profil berhasil diupdate",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "major": "Teknik Informatika",
    "specialization": ["Web Development", "Machine Learning", "UI/UX Design"]
  }
}
```

## Contoh Use Case

### Scenario 1: User dengan Specialization
```
User Profile:
- major: "Teknik Informatika"
- specialization: ["Web Development", "React"]

Hasil Rekomendasi (urutan):
1. "React Advanced Development" (150 poin - exact match di title)
2. "Web Development Bootcamp" (150 poin - exact match di title)
3. "Full Stack Web Developer" (100 poin - match di category)
4. "Data Structures for CS" (60 poin - match major di title)
```

### Scenario 2: User tanpa Specialization
```
User Profile:
- major: "Sistem Informasi"
- specialization: []

Hasil Rekomendasi (urutan):
1. Course dengan rating tertinggi (match major di title: 60 poin)
2. Course populer (enrollment count tinggi)
3. Course dengan rating bagus
```

### Scenario 3: User Free Plan
```
User Profile:
- subscription: "free"
- specialization: ["Python", "Data Science"]

Hasil Rekomendasi:
- Hanya menampilkan course dengan access_type = "free"
- Diurutkan berdasarkan relevance dengan "Python" dan "Data Science"
```

## Validasi

### Specialization Rules
```php
'specialization'   => 'nullable|array',
'specialization.*' => 'string|max:50',
```

- Tipe data: **Array of strings**
- Setiap item maksimal **50 karakter**
- Boleh kosong (nullable)

## Database Schema

### users table
```sql
specialization JSON NULL    -- Array minat user
major          VARCHAR(255) -- Jurusan pendidikan
```

Contoh data:
```json
{
  "major": "Teknik Informatika",
  "specialization": ["Web Development", "Machine Learning"]
}
```

## Tips Implementasi

1. **Update Profile Setelah Register**
   - Arahkan user untuk melengkapi specialization setelah register
   - Ini akan meningkatkan akurasi rekomendasi

2. **Multiple Specializations**
   - User bisa menambahkan beberapa specialization
   - Specialization pertama mendapat bobot tertinggi

3. **Fallback**
   - Jika semua course sudah di-enroll, sistem akan menampilkan top-rated courses
   - Jika tidak ada specialization/major, urutkan berdasarkan rating & popularity

4. **Performance**
   - Query menggunakan raw SQL dengan CASE WHEN untuk scoring
   - Efficient dengan index pada columns yang di-search
