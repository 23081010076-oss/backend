# Course API - Request Body Examples

## 1. Create Course (POST /api/courses)

### Bootcamp - Premium

```json
{
  "title": "Full Stack Web Development Bootcamp",
  "description": "Comprehensive bootcamp covering frontend and backend development with React, Node.js, and databases",
  "category": "Programming",
  "type": "bootcamp",
  "level": "intermediate",
  "access_type": "premium",
  "duration": "3 months",
  "price": 5000000,
  "instructor": "John Doe",
  "video_url": "https://example.com/intro-video.mp4",
  "video_duration": "10 minutes",
  "total_videos": 80
}
```

### Course - Free

```json
{
  "title": "Introduction to Python Programming",
  "description": "Learn Python basics from scratch",
  "category": "Programming",
  "type": "course",
  "level": "beginner",
  "access_type": "free",
  "duration": "4 weeks",
  "price": 0,
  "instructor": "Jane Smith",
  "video_url": "https://youtube.com/watch?v=intro",
  "video_duration": "5 minutes",
  "total_videos": 20
}
```

### Course - Regular

```json
{
  "title": "Data Science with Python",
  "description": "Master data analysis, visualization, and machine learning with Python",
  "category": "Data Science",
  "type": "course",
  "level": "advanced",
  "access_type": "regular",
  "duration": "8 weeks",
  "price": 1500000,
  "instructor": "Dr. Sarah Johnson",
  "video_url": "https://example.com/preview.mp4",
  "video_duration": "15 minutes",
  "total_videos": 45
}
```

### Bootcamp - Free (Trial)

```json
{
  "title": "UI/UX Design Fundamentals",
  "description": "Free introductory bootcamp for aspiring UI/UX designers",
  "category": "Design",
  "type": "bootcamp",
  "level": "beginner",
  "access_type": "free",
  "duration": "2 weeks",
  "price": 0,
  "instructor": "Michael Chen",
  "total_videos": 15
}
```

### Minimal Required Fields

```json
{
  "title": "Quick Start Course",
  "type": "course",
  "level": "beginner",
  "access_type": "free"
}
```

---

## 2. Update Course (PUT /api/courses/{id})

### Update Price and Duration

```json
{
  "price": 3500000,
  "duration": "4 months"
}
```

### Update Level and Content

```json
{
  "level": "advanced",
  "description": "Now includes advanced topics and real-world projects",
  "total_videos": 100
}
```

### Update to Premium

```json
{
  "access_type": "premium",
  "price": 7500000,
  "duration": "6 months"
}
```

### Complete Update

```json
{
  "title": "Advanced Full Stack Development Bootcamp 2025",
  "description": "Updated with latest technologies: Next.js 14, TypeScript, Prisma, Docker",
  "category": "Programming",
  "type": "bootcamp",
  "level": "advanced",
  "access_type": "premium",
  "duration": "5 months",
  "price": 8000000,
  "instructor": "John Doe & Team",
  "video_url": "https://example.com/new-intro.mp4",
  "video_duration": "12 minutes",
  "total_videos": 120
}
```

---

## Field Specifications

### Required Fields (untuk Create)

- `title` - string, max 255 characters
- `type` - enum: `bootcamp` atau `course`
- `level` - enum: `beginner`, `intermediate`, `advanced`
- `access_type` - enum: `free`, `regular`, `premium`

### Optional Fields

- `description` - string (text panjang)
- `category` - string, max 100 characters
- `duration` - string (contoh: "3 months", "8 weeks")
- `price` - number, min: 0 (wajib 0 jika `access_type` = `free`)
- `certificate_url` - string (URL)
- `instructor` - string (nama instruktur)
- `video_url` - string (URL intro video)
- `video_duration` - string (contoh: "10 minutes")
- `total_videos` - integer, min: 0

### Field Rules

1. Jika `access_type` = `free`, maka `price` harus 0
2. Jika `access_type` = `premium`, `price` biasanya > 2000000
3. `bootcamp` biasanya lebih mahal dan lebih lama dari `course`
4. `beginner` untuk pemula, `intermediate` untuk menengah, `advanced` untuk mahir

---

## Query Parameters (untuk GET /api/courses)

### Filter by Type

```
GET /api/courses?type=bootcamp
GET /api/courses?type=course
```

### Filter by Level

```
GET /api/courses?level=beginner
GET /api/courses?level=intermediate
GET /api/courses?level=advanced
```

### Filter by Access Type

```
GET /api/courses?access_type=free
GET /api/courses?access_type=regular
GET /api/courses?access_type=premium
```

### Multiple Filters

```
GET /api/courses?type=course&level=beginner&access_type=free
GET /api/courses?type=bootcamp&level=advanced&access_type=premium
```

### With Pagination

```
GET /api/courses?page=1&per_page=10
GET /api/courses?type=course&page=2&per_page=20
```

---

## Response Examples

### Success Create (201)

```json
{
  "sukses": true,
  "pesan": "Kursus berhasil ditambahkan",
  "data": {
    "id": 15,
    "title": "Full Stack Web Development Bootcamp",
    "description": "Comprehensive bootcamp...",
    "category": "Programming",
    "type": "bootcamp",
    "level": "intermediate",
    "access_type": "premium",
    "duration": "3 months",
    "price": 5000000,
    "instructor": "John Doe",
    "created_at": "2025-12-24T10:30:00.000000Z",
    "updated_at": "2025-12-24T10:30:00.000000Z"
  }
}
```

### Error Validation (422)

```json
{
  "sukses": false,
  "pesan": "Validasi gagal",
  "errors": {
    "title": ["Judul kursus wajib diisi"],
    "type": ["Jenis kursus harus bootcamp atau course"],
    "price": ["Harga tidak boleh negatif"]
  }
}
```

### Error Unauthorized (403)

```json
{
  "sukses": false,
  "pesan": "This action is unauthorized."
}
```
