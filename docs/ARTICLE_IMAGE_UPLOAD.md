# Article Image Upload Feature

## Overview
Fitur upload gambar untuk artikel telah ditambahkan. Admin/Corporate user dapat mengunggah gambar saat membuat atau mengupdate artikel.

## Changes Made

### 1. Request Validation
- **StoreArticleRequest.php**: Menambahkan validasi untuk field `image`
- **UpdateArticleRequest.php**: Menambahkan validasi untuk field `image`
- Format: jpeg, jpg, png, gif, webp
- Ukuran maksimal: 5MB

### 2. Service Layer
- **ArticleService.php**:
  - `createArticle()`: Handle upload gambar saat create
  - `updateArticle()`: Handle upload gambar baru dan delete gambar lama
  - `deleteArticle()`: Delete gambar saat delete artikel
  - `uploadImage()`: Private method untuk upload gambar
  - `deleteImage()`: Private method untuk delete gambar

### 3. Model
- **Article.php**:
  - Menambahkan accessor `image_url` untuk return URL lengkap
  - Auto-append `image_url` ke JSON response

### 4. Swagger Documentation
- Updated endpoint documentation untuk POST /api/articles
- Updated endpoint documentation untuk PUT /api/articles
- Menambahkan contoh response dengan field `image` dan `image_url`

## API Usage

### Create Article with Image

**Endpoint**: `POST /api/articles`

**Content-Type**: `multipart/form-data`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body** (form-data):
```
title: "Panduan Karir di Tech Industry"
content: "Artikel lengkap tentang karir di industri teknologi..."
category: "career"
author: "John Doe" (optional)
image: [FILE] (jpeg/jpg/png/gif/webp, max 5MB)
```

**Response Success (201)**:
```json
{
  "sukses": true,
  "pesan": "Artikel berhasil ditambahkan",
  "data": {
    "id": 1,
    "title": "Panduan Karir di Tech Industry",
    "content": "Artikel lengkap...",
    "category": "career",
    "author": "John Doe",
    "image": "articles/article_1735123456_abc123def.jpg",
    "image_url": "http://localhost:8000/storage/articles/article_1735123456_abc123def.jpg",
    "created_at": "2026-01-02T10:00:00.000000Z"
  }
}
```

### Update Article with Image

**Endpoint**: `PUT /api/articles/{id}` atau `POST /api/articles/{id}` dengan `_method=PUT`

**Content-Type**: `multipart/form-data`

**Body** (form-data):
```
_method: PUT (required for multipart PUT request)
title: "Updated Title" (optional)
content: "Updated content..." (optional)
category: "education" (optional)
image: [NEW_FILE] (optional - akan replace gambar lama)
```

**Note**: 
- Jika upload gambar baru, gambar lama akan otomatis dihapus
- Semua field adalah optional untuk update
- Gunakan `_method=PUT` jika menggunakan POST dengan multipart

### Delete Article

**Endpoint**: `DELETE /api/articles/{id}`

- Gambar akan otomatis dihapus dari storage saat artikel dihapus

## File Storage

- Gambar disimpan di: `storage/app/public/articles/`
- Format nama file: `article_{timestamp}_{random}.{extension}`
- Accessible via: `http://localhost:8000/storage/articles/{filename}`

## Validation Rules

```php
'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120'
```

- **nullable**: Image tidak wajib
- **image**: Harus file gambar valid
- **mimes**: Format yang diperbolehkan: jpeg, jpg, png, gif, webp
- **max**: Ukuran maksimal 5MB (5120 KB)

## Validation Error Messages

```
- "File harus berupa gambar"
- "Format gambar harus jpeg, jpg, png, gif, atau webp"
- "Ukuran gambar maksimal 5MB"
```

## Testing

### Test Create with Image (Postman/Insomnia)

1. Set method: **POST**
2. URL: `http://localhost:8000/api/articles`
3. Headers:
   - `Authorization: Bearer {your_token}`
4. Body → form-data:
   - `title`: "Test Article"
   - `content`: "Test content"
   - `category`: "education"
   - `image`: [Select file]
5. Send request

### Test Update with Image

1. Set method: **POST** (because multipart doesn't support PUT directly)
2. URL: `http://localhost:8000/api/articles/1`
3. Headers:
   - `Authorization: Bearer {your_token}`
4. Body → form-data:
   - `_method`: "PUT"
   - `title`: "Updated Title"
   - `image`: [Select new file]
5. Send request

### Verify Image Upload

1. Check response for `image` and `image_url` fields
2. Open `image_url` in browser to verify image is accessible
3. Check `storage/app/public/articles/` directory

## Error Handling

### Common Errors

1. **"File harus berupa gambar"**
   - Cause: File bukan gambar atau format tidak valid
   - Solution: Upload file dengan format jpeg, jpg, png, gif, atau webp

2. **"Ukuran gambar maksimal 5MB"**
   - Cause: File terlalu besar
   - Solution: Compress atau resize gambar sebelum upload

3. **Storage link not configured**
   - Cause: Symbolic link belum dibuat
   - Solution: Run `php artisan storage:link`

## Security Considerations

1. ✅ File type validation (hanya image)
2. ✅ File size limit (5MB)
3. ✅ Random filename generation (prevent predictable paths)
4. ✅ Old image cleanup (prevent storage bloat)
5. ✅ Authorization check (only admin/corporate can create/update)

## Model Accessor

Model Article secara otomatis menambahkan `image_url` ke setiap response:

```php
// Accessor di Model
public function getImageUrlAttribute(): ?string
{
    if (!$this->image) {
        return null;
    }
    
    if (filter_var($this->image, FILTER_VALIDATE_URL)) {
        return $this->image;
    }
    
    return asset('storage/' . $this->image);
}
```

Response akan selalu include:
- `image`: Path relatif (e.g., "articles/article_123.jpg")
- `image_url`: Full URL (e.g., "http://localhost:8000/storage/articles/article_123.jpg")
