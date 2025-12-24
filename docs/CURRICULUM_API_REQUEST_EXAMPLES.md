# Course Curriculum API - Request Body Examples

## 1. Create Curriculum (POST /api/courses/{courseId}/curriculums)

### Basic Curriculum

```json
{
  "title": "Introduction to Variables and Data Types",
  "description": "Learn about different data types in Python: integers, strings, floats, booleans, and lists",
  "duration": "15 minutes",
  "order": 1
}
```

### With Section Grouping

```json
{
  "title": "Functions and Parameters",
  "section": "Module 2: Functions",
  "section_order": 2,
  "description": "Understanding how to create and use functions with parameters and return values",
  "duration": "20 minutes",
  "order": 5
}
```

### Advanced Topic

```json
{
  "title": "Object-Oriented Programming: Classes and Objects",
  "section": "Module 5: OOP Concepts",
  "section_order": 5,
  "description": "Deep dive into OOP principles: encapsulation, inheritance, polymorphism, and abstraction with practical examples",
  "duration": "45 minutes",
  "order": 15
}
```

### Video Tutorial

```json
{
  "title": "Building Your First REST API",
  "section": "Final Project",
  "section_order": 10,
  "description": "Step-by-step guide to creating a production-ready REST API using Flask and SQLAlchemy",
  "duration": "1 hour 30 minutes",
  "order": 30
}
```

### Minimal Required Fields

```json
{
  "title": "Installing Python and Setting Up Environment"
}
```

---

## 2. Bulk Create Curriculums (POST /api/courses/{courseId}/curriculums/bulk)

### Complete Course Structure

```json
{
  "curriculums": [
    {
      "title": "Course Introduction",
      "section": "Getting Started",
      "section_order": 1,
      "description": "Welcome to the course! Overview of what you'll learn",
      "duration": "5 minutes",
      "order": 1
    },
    {
      "title": "Setting Up Your Environment",
      "section": "Getting Started",
      "section_order": 1,
      "description": "Install Python, VS Code, and necessary extensions",
      "duration": "15 minutes",
      "order": 2
    },
    {
      "title": "Variables and Data Types",
      "section": "Python Basics",
      "section_order": 2,
      "description": "Learn about int, float, string, boolean, and list",
      "duration": "20 minutes",
      "order": 3
    },
    {
      "title": "Control Flow: If-Else Statements",
      "section": "Python Basics",
      "section_order": 2,
      "description": "Making decisions in your code with conditionals",
      "duration": "18 minutes",
      "order": 4
    },
    {
      "title": "Loops: For and While",
      "section": "Python Basics",
      "section_order": 2,
      "description": "Repeating actions efficiently with loops",
      "duration": "25 minutes",
      "order": 5
    }
  ]
}
```

### Minimal Bulk Create (Auto-ordering)

```json
{
  "curriculums": [
    {
      "title": "Introduction to HTML"
    },
    {
      "title": "HTML Tags and Elements"
    },
    {
      "title": "CSS Basics"
    },
    {
      "title": "CSS Flexbox"
    },
    {
      "title": "Responsive Design"
    }
  ]
}
```

---

## 3. Update Curriculum (PUT /api/courses/{courseId}/curriculums/{id})

### Update Title and Description

```json
{
  "title": "Advanced Object-Oriented Programming",
  "description": "Comprehensive guide to OOP: classes, objects, inheritance, polymorphism, encapsulation, and design patterns"
}
```

### Update Duration Only

```json
{
  "duration": "35 minutes"
}
```

### Change Order

```json
{
  "order": 8
}
```

### Move to Different Section

```json
{
  "section": "Advanced Topics",
  "section_order": 6,
  "order": 20
}
```

### Complete Update

```json
{
  "title": "Building RESTful APIs with FastAPI",
  "section": "Backend Development",
  "section_order": 4,
  "description": "Modern Python web development with FastAPI: routing, validation, database integration, authentication, and deployment",
  "duration": "2 hours",
  "order": 18
}
```

---

## 4. Reorder Curriculums (POST /api/courses/{courseId}/curriculums/reorder)

### Reorder Multiple Items

```json
{
  "ordered_ids": [5, 3, 1, 2, 4, 6, 7]
}
```

**Explanation**:

- Curriculum ID 5 akan menjadi order 1
- Curriculum ID 3 akan menjadi order 2
- Curriculum ID 1 akan menjadi order 3
- dst...

---

## Field Specifications

### Create Curriculum

#### Required Fields

- `title` - string, max 255 characters (Judul materi/topik)

#### Optional Fields

- `description` - string (Penjelasan detail materi)
- `duration` - string, max 100 characters (Durasi: "15 minutes", "1 hour", dll)
- `order` - integer, min 0 (Urutan tampil dalam course)
- `section` - string, max 255 characters (Nama section/modul)
- `section_order` - integer, min 0 (Urutan section)

### Update Curriculum

Semua field bersifat opsional (gunakan field yang ingin diubah saja)

### Bulk Create

- `curriculums` - array (required), min 1 item
- Setiap item dalam array mengikuti aturan Create Curriculum
- Jika `order` tidak diisi, akan di-generate otomatis (1, 2, 3, ...)

### Reorder

- `ordered_ids` - array of integers (required)
- Setiap ID harus valid (exists di database)
- Urutan array menentukan urutan baru

---

## Organizational Concepts

### Section Grouping

Section digunakan untuk mengelompokkan curriculum menjadi modul/bab:

```
Section: "Getting Started" (section_order: 1)
  ├─ Curriculum 1: Course Introduction (order: 1)
  ├─ Curriculum 2: Environment Setup (order: 2)
  └─ Curriculum 3: First Python Program (order: 3)

Section: "Python Basics" (section_order: 2)
  ├─ Curriculum 4: Variables (order: 4)
  ├─ Curriculum 5: Data Types (order: 5)
  └─ Curriculum 6: Operators (order: 6)
```

### Order vs Section Order

- **order**: Urutan curriculum dalam keseluruhan course (1, 2, 3, ...)
- **section_order**: Urutan section/modul dalam course (1, 2, 3, ...)

---

## Response Examples

### Success Create (201)

```json
{
  "sukses": true,
  "pesan": "Kurikulum berhasil ditambahkan",
  "data": {
    "id": 42,
    "course_id": 5,
    "title": "Introduction to Variables and Data Types",
    "description": "Learn about different data types in Python...",
    "duration": "15 minutes",
    "order": 1,
    "section": null,
    "section_order": null,
    "created_at": "2025-12-24T12:00:00.000000Z",
    "updated_at": "2025-12-24T12:00:00.000000Z"
  }
}
```

### Success Bulk Create (201)

```json
{
  "sukses": true,
  "pesan": "5 kurikulum berhasil ditambahkan",
  "data": [
    {
      "id": 43,
      "course_id": 5,
      "title": "Course Introduction",
      "order": 1,
      "created_at": "2025-12-24T12:05:00.000000Z"
    },
    {
      "id": 44,
      "course_id": 5,
      "title": "Setting Up Your Environment",
      "order": 2,
      "created_at": "2025-12-24T12:05:00.000000Z"
    }
    // ... 3 more items
  ]
}
```

### Success Reorder (200)

```json
{
  "sukses": true,
  "pesan": "Urutan kurikulum berhasil diupdate",
  "data": null
}
```

### Error Validation (422)

```json
{
  "sukses": false,
  "pesan": "Validasi gagal",
  "errors": {
    "title": ["Judul kurikulum wajib diisi"],
    "order": ["Urutan harus berupa angka"],
    "curriculums": ["Curriculums harus berupa array dengan minimal 1 item"]
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

### Error Course Not Found (404)

```json
{
  "sukses": false,
  "pesan": "No query results for model [App\\Models\\Course] 999"
}
```

---

## API Endpoints Summary

| Method | Endpoint                                      | Description                 | Auth            |
| ------ | --------------------------------------------- | --------------------------- | --------------- |
| GET    | `/api/courses/{courseId}/curriculums`         | List all curriculums        | Public          |
| POST   | `/api/courses/{courseId}/curriculums`         | Create single curriculum    | Admin/Corporate |
| POST   | `/api/courses/{courseId}/curriculums/bulk`    | Create multiple curriculums | Admin/Corporate |
| GET    | `/api/courses/{courseId}/curriculums/{id}`    | Get curriculum detail       | Public          |
| PUT    | `/api/courses/{courseId}/curriculums/{id}`    | Update curriculum           | Admin/Corporate |
| DELETE | `/api/courses/{courseId}/curriculums/{id}`    | Delete curriculum           | Admin/Corporate |
| POST   | `/api/courses/{courseId}/curriculums/reorder` | Reorder curriculums         | Admin/Corporate |

---

## Usage Tips

### Best Practices

1. **Use section grouping** untuk course yang kompleks dengan banyak topik
2. **Set order explicitly** saat create untuk kontrol penuh atas urutan
3. **Use bulk create** saat inisialisasi course baru (lebih efisien)
4. **Use reorder endpoint** untuk mengubah urutan banyak item sekaligus

### Duration Format Suggestions

- Short videos: "5 minutes", "10 minutes"
- Medium content: "20 minutes", "30 minutes"
- Long sessions: "1 hour", "1 hour 30 minutes"
- Workshop: "2 hours", "3 hours"

### Section Naming Examples

- "Getting Started"
- "Module 1: Basics"
- "Week 1: Introduction"
- "Chapter 3: Advanced Concepts"
- "Project: Building Your App"
- "Final Assessment"

### Authorization

- **View Curriculums**: Public (semua user bisa lihat)
- **Create/Update/Delete**: Admin & Corporate only (yang bisa edit course)
- Authorization mengikuti course parent-nya
