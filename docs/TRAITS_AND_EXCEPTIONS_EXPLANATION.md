# 🔧 Penjelasan Folder Traits & Exceptions

## 📋 Overview

Folder `Traits` dan `Exceptions` adalah bagian dari **code reusability** dan **error handling** di Laravel. Keduanya membantu mengurangi duplikasi code dan membuat error handling lebih konsisten.

---

## 📁 Folder `app/Traits`

### Apa itu Trait?

**Trait** adalah cara untuk **reuse code** (pakai ulang code) di beberapa class tanpa inheritance (pewarisan).

**Masalah tanpa Trait**:
```php
// Di CourseController
public function index() {
    return response()->json([
        'success' => true,
        'message' => 'Berhasil',
        'data' => $courses
    ], 200);
}

// Di UserController (DUPLIKASI!)
public function index() {
    return response()->json([
        'success' => true,
        'message' => 'Berhasil',
        'data' => $users
    ], 200);
}
```

**Solusi dengan Trait**:
```php
// Di semua Controller
use ApiResponse;

public function index() {
    return $this->successResponse($courses, 'Berhasil');
}
```

---

### 1. **ApiResponse.php**

**Lokasi**: `app/Traits/ApiResponse.php`

**Fungsi**: Menyediakan **format response JSON yang seragam** untuk semua API endpoint.

#### Cara Pakai

**Di Controller**:
```php
use App\Traits\ApiResponse;

class CourseController extends Controller
{
    use ApiResponse;  // Tambahkan ini
    
    public function index()
    {
        $courses = Course::all();
        
        // Pakai method dari trait
        return $this->successResponse($courses, 'Daftar kursus berhasil diambil');
    }
}
```

#### Method yang Tersedia

##### 1. **successResponse()** - Response Sukses
```php
// Syntax
$this->successResponse($data, $message, $statusCode = 200)

// Contoh
return $this->successResponse($user, 'Login berhasil');

// Output
{
  "sukses": true,
  "pesan": "Login berhasil",
  "data": {
    "id": 1,
    "name": "Ahmad",
    "email": "ahmad@example.com"
  }
}
```

##### 2. **errorResponse()** - Response Error
```php
// Syntax
$this->errorResponse($message, $statusCode = 400, $errors = null)

// Contoh
return $this->errorResponse('Email sudah terdaftar', 400);

// Output
{
  "sukses": false,
  "pesan": "Email sudah terdaftar",
  "data": null
}
```

##### 3. **createdResponse()** - Data Baru Dibuat (201)
```php
// Contoh
return $this->createdResponse($course, 'Kursus berhasil dibuat');

// Output (Status: 201 Created)
{
  "sukses": true,
  "pesan": "Kursus berhasil dibuat",
  "data": { /* course data */ }
}
```

##### 4. **notFoundResponse()** - Data Tidak Ditemukan (404)
```php
// Contoh
return $this->notFoundResponse('Kursus tidak ditemukan');

// Output (Status: 404 Not Found)
{
  "sukses": false,
  "pesan": "Kursus tidak ditemukan",
  "data": null
}
```

##### 5. **unauthorizedResponse()** - Belum Login (401)
```php
// Contoh
return $this->unauthorizedResponse('Silakan login terlebih dahulu');

// Output (Status: 401 Unauthorized)
{
  "sukses": false,
  "pesan": "Silakan login terlebih dahulu",
  "data": null
}
```

##### 6. **forbiddenResponse()** - Tidak Diizinkan (403)
```php
// Contoh
return $this->forbiddenResponse('Anda tidak memiliki izin');

// Output (Status: 403 Forbidden)
{
  "sukses": false,
  "pesan": "Anda tidak memiliki izin",
  "data": null
}
```

##### 7. **validationErrorResponse()** - Validasi Gagal (422)
```php
// Contoh
return $this->validationErrorResponse([
    'email' => ['Email sudah terdaftar'],
    'password' => ['Password minimal 8 karakter']
], 'Data tidak valid');

// Output (Status: 422 Unprocessable Entity)
{
  "sukses": false,
  "pesan": "Data tidak valid",
  "data": null,
  "errors": {
    "email": ["Email sudah terdaftar"],
    "password": ["Password minimal 8 karakter"]
  }
}
```

##### 8. **serverErrorResponse()** - Server Error (500)
```php
// Contoh
return $this->serverErrorResponse('Terjadi kesalahan pada server');

// Output (Status: 500 Internal Server Error)
{
  "sukses": false,
  "pesan": "Terjadi kesalahan pada server",
  "data": null
}
```

##### 9. **paginatedResponse()** - Response dengan Pagination
```php
// Contoh
$courses = Course::paginate(10);
return $this->paginatedResponse($courses, 'Daftar kursus');

// Output
{
  "sukses": true,
  "pesan": "Daftar kursus",
  "data": [ /* array of courses */ ],
  "meta": {
    "total": 50,
    "per_halaman": 10,
    "halaman_sekarang": 1,
    "halaman_terakhir": 5,
    "dari": 1,
    "sampai": 10
  }
}
```

#### Contoh Penggunaan di Controller

```php
use App\Traits\ApiResponse;

class CourseController extends Controller
{
    use ApiResponse;
    
    // GET /api/courses
    public function index()
    {
        $courses = Course::all();
        return $this->successResponse($courses, 'Daftar kursus');
    }
    
    // GET /api/courses/{id}
    public function show($id)
    {
        $course = Course::find($id);
        
        if (!$course) {
            return $this->notFoundResponse('Kursus tidak ditemukan');
        }
        
        return $this->successResponse($course);
    }
    
    // POST /api/courses
    public function store(Request $request)
    {
        $course = Course::create($request->all());
        return $this->createdResponse($course, 'Kursus berhasil dibuat');
    }
    
    // DELETE /api/courses/{id}
    public function destroy($id)
    {
        $course = Course::find($id);
        
        if (!$course) {
            return $this->notFoundResponse('Kursus tidak ditemukan');
        }
        
        // Check ownership
        if ($course->user_id !== auth()->id()) {
            return $this->forbiddenResponse('Anda tidak bisa menghapus kursus ini');
        }
        
        $course->delete();
        return $this->successResponse(null, 'Kursus berhasil dihapus');
    }
}
```

---

### 2. **HasOwnership.php**

**Lokasi**: `app/Traits/HasOwnership.php`

**Fungsi**: Menyediakan method untuk **cek kepemilikan resource** (apakah user adalah pemilik data).

#### Cara Pakai

**Di Controller**:
```php
use App\Traits\HasOwnership;

class AchievementController extends Controller
{
    use HasOwnership;  // Tambahkan ini
    
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);
        
        // Cek apakah user adalah pemilik
        if (!$this->isOwner($achievement)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $achievement->update($request->all());
        return response()->json($achievement);
    }
}
```

#### Method yang Tersedia

##### 1. **isOwner()** - Cek Kepemilikan
```php
// Syntax
$this->isOwner($resource, $ownerColumn = 'user_id')

// Contoh
$achievement = Achievement::find(1);

if ($this->isOwner($achievement)) {
    // User adalah pemilik
} else {
    // User bukan pemilik
}

// Dengan custom column
if ($this->isOwner($course, 'instructor_id')) {
    // User adalah instructor
}
```

##### 2. **isOwnerOrAdmin()** - Cek Pemilik atau Admin
```php
// Syntax
$this->isOwnerOrAdmin($resource, $ownerColumn = 'user_id')

// Contoh
if ($this->isOwnerOrAdmin($achievement)) {
    // User adalah pemilik ATAU admin
    $achievement->delete();
}
```

##### 3. **isAdmin()** - Cek Admin
```php
// Syntax
$this->isAdmin($user = null)

// Contoh
if ($this->isAdmin()) {
    // User adalah admin
}

// Cek user lain
if ($this->isAdmin($someUser)) {
    // $someUser adalah admin
}
```

##### 4. **hasRole()** - Cek Role
```php
// Syntax
$this->hasRole(['role1', 'role2'], $user = null)

// Contoh
if ($this->hasRole(['admin', 'mentor'])) {
    // User adalah admin ATAU mentor
}
```

##### 5. **authorizeOwnership()** - Auto Abort jika Bukan Pemilik
```php
// Syntax
$this->authorizeOwnership($resource, $ownerColumn = 'user_id', $message = 'Unauthorized')

// Contoh
public function update(Request $request, $id)
{
    $achievement = Achievement::findOrFail($id);
    
    // Otomatis throw 403 jika bukan pemilik/admin
    $this->authorizeOwnership($achievement);
    
    // Code di bawah hanya jalan jika authorized
    $achievement->update($request->all());
    return response()->json($achievement);
}
```

##### 6. **scopeOwnedBy()** - Filter Query by Owner
```php
// Syntax
$query->scopeOwnedBy($user = null, $ownerColumn = 'user_id')

// Contoh
// Admin: lihat semua achievements
// User: lihat achievements miliknya saja
$achievements = Achievement::query()
    ->scopeOwnedBy()
    ->get();
```

#### Contoh Penggunaan Lengkap

```php
use App\Traits\ApiResponse;
use App\Traits\HasOwnership;

class AchievementController extends Controller
{
    use ApiResponse, HasOwnership;
    
    // GET /api/achievements
    public function index()
    {
        // Admin: lihat semua
        // User: lihat miliknya saja
        $achievements = Achievement::query()
            ->scopeOwnedBy()
            ->get();
            
        return $this->successResponse($achievements);
    }
    
    // PUT /api/achievements/{id}
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);
        
        // Cek ownership (auto abort 403 jika bukan pemilik)
        $this->authorizeOwnership($achievement);
        
        $achievement->update($request->validated());
        
        return $this->successResponse($achievement, 'Achievement updated');
    }
    
    // DELETE /api/achievements/{id}
    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);
        
        // Hanya pemilik atau admin yang bisa hapus
        if (!$this->isOwnerOrAdmin($achievement)) {
            return $this->forbiddenResponse('You cannot delete this achievement');
        }
        
        $achievement->delete();
        
        return $this->successResponse(null, 'Achievement deleted');
    }
}
```

---

## 📁 Folder `app/Exceptions`

### Apa itu Exception?

**Exception** adalah cara untuk **handle error** dengan lebih terstruktur. Daripada return error response di mana-mana, kita bisa **throw exception** dan Laravel akan handle-nya.

### **ApiException.php**

**Lokasi**: `app/Exceptions/ApiException.php`

**Fungsi**: Custom exception untuk API dengan **format error yang konsisten**.

#### Cara Pakai

##### Method 1: Throw Exception
```php
use App\Exceptions\ApiException;

class CourseController extends Controller
{
    public function show($id)
    {
        $course = Course::find($id);
        
        if (!$course) {
            // Throw exception (akan auto-convert ke JSON response)
            throw ApiException::notFound('Course not found');
        }
        
        return response()->json($course);
    }
}
```

##### Method 2: Return Exception
```php
public function show($id)
{
    $course = Course::find($id);
    
    if (!$course) {
        // Return exception response
        return ApiException::notFound('Course not found')->render();
    }
    
    return response()->json($course);
}
```

#### Static Methods

##### 1. **notFound()** - 404 Not Found
```php
throw ApiException::notFound('Course not found');

// Output (Status: 404)
{
  "success": false,
  "message": "Course not found"
}
```

##### 2. **unauthorized()** - 401 Unauthorized
```php
throw ApiException::unauthorized('Please login first');

// Output (Status: 401)
{
  "success": false,
  "message": "Please login first"
}
```

##### 3. **forbidden()** - 403 Forbidden
```php
throw ApiException::forbidden('You do not have permission');

// Output (Status: 403)
{
  "success": false,
  "message": "You do not have permission"
}
```

##### 4. **validationError()** - 422 Validation Error
```php
throw ApiException::validationError([
    'email' => ['Email is required'],
    'password' => ['Password must be at least 8 characters']
], 'Validation failed');

// Output (Status: 422)
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email is required"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

##### 5. **conflict()** - 409 Conflict
```php
throw ApiException::conflict('Email already exists');

// Output (Status: 409)
{
  "success": false,
  "message": "Email already exists"
}
```

##### 6. **serverError()** - 500 Server Error
```php
throw ApiException::serverError('Database connection failed');

// Output (Status: 500)
{
  "success": false,
  "message": "Database connection failed"
}
```

#### Contoh Penggunaan

```php
use App\Exceptions\ApiException;

class CourseController extends Controller
{
    public function show($id)
    {
        $course = Course::find($id);
        
        if (!$course) {
            throw ApiException::notFound('Course not found');
        }
        
        return response()->json($course);
    }
    
    public function enroll(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        // Check if already enrolled
        $existing = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $id)
            ->first();
            
        if ($existing) {
            throw ApiException::conflict('You are already enrolled in this course');
        }
        
        // Check if user has permission
        if (!auth()->user()->canEnroll()) {
            throw ApiException::forbidden('You need an active subscription to enroll');
        }
        
        $enrollment = Enrollment::create([
            'user_id' => auth()->id(),
            'course_id' => $id,
        ]);
        
        return response()->json($enrollment, 201);
    }
}
```

---

## 🎯 Kombinasi Traits + Exception

Contoh penggunaan **ApiResponse**, **HasOwnership**, dan **ApiException** bersamaan:

```php
use App\Traits\ApiResponse;
use App\Traits\HasOwnership;
use App\Exceptions\ApiException;

class AchievementController extends Controller
{
    use ApiResponse, HasOwnership;
    
    public function update(Request $request, $id)
    {
        try {
            $achievement = Achievement::find($id);
            
            // Throw exception jika tidak ditemukan
            if (!$achievement) {
                throw ApiException::notFound('Achievement not found');
            }
            
            // Throw exception jika bukan pemilik
            if (!$this->isOwnerOrAdmin($achievement)) {
                throw ApiException::forbidden('You cannot edit this achievement');
            }
            
            $achievement->update($request->validated());
            
            // Return success response
            return $this->successResponse($achievement, 'Achievement updated successfully');
            
        } catch (ApiException $e) {
            // ApiException akan auto-render ke JSON
            return $e->render();
        } catch (\Exception $e) {
            // Handle unexpected errors
            return $this->serverErrorResponse('An error occurred: ' . $e->getMessage());
        }
    }
}
```

---

## 📊 Perbandingan

### Tanpa Traits & Exceptions
```php
public function update(Request $request, $id)
{
    $achievement = Achievement::find($id);
    
    if (!$achievement) {
        return response()->json([
            'success' => false,
            'message' => 'Achievement not found'
        ], 404);
    }
    
    if ($achievement->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Forbidden'
        ], 403);
    }
    
    $achievement->update($request->all());
    
    return response()->json([
        'success' => true,
        'message' => 'Updated',
        'data' => $achievement
    ], 200);
}
```

### Dengan Traits & Exceptions
```php
use ApiResponse, HasOwnership;

public function update(Request $request, $id)
{
    $achievement = Achievement::findOrFail($id);
    
    $this->authorizeOwnership($achievement);
    
    $achievement->update($request->validated());
    
    return $this->successResponse($achievement, 'Updated');
}
```

**Lebih bersih, lebih mudah dibaca!** ✨

---

## 📝 Kesimpulan

| Folder | File | Fungsi | Keuntungan |
|--------|------|--------|------------|
| **Traits** | ApiResponse | Format response konsisten | ✅ No duplikasi<br>✅ Mudah maintain |
| **Traits** | HasOwnership | Cek kepemilikan resource | ✅ Reusable<br>✅ Security |
| **Exceptions** | ApiException | Handle error terstruktur | ✅ Konsisten<br>✅ Clean code |

**Best Practice**: Gunakan ketiganya di semua controller untuk code yang **clean**, **consistent**, dan **maintainable**! 🚀
