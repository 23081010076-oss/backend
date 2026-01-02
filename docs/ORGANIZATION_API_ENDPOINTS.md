# Organization API Endpoints

## Masalah yang Diperbaiki

Data seeder organizations sudah ada di database (5 organizations), tetapi tidak muncul di API karena endpoint `/api/organizations` hanya menampilkan organizations milik user yang sedang login (`user_id = Auth::id()`).

## Solusi

Menambahkan endpoint publik baru untuk melihat semua organizations (katalog publik).

## Endpoints

### 1. List All Organizations (Public - NEW!)

**Endpoint:** `GET /api/organizations/list/all`  
**Authentication:** Required  
**Purpose:** Melihat semua organizations yang ada di database (katalog publik)

**Query Parameters:**

- `type` (optional): Filter berdasarkan tipe (`company`, `government`, `university`)
- `search` (optional): Cari berdasarkan nama organization

**Response:**

```json
{
  "success": true,
  "message": "All organizations retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Google Indonesia",
        "type": "company",
        "description": "Google mission is to organize the world information...",
        "location": "Jakarta, Indonesia",
        "website": "https://about.google/",
        "contact_email": "contact@google.com",
        "phone": "+62 21 2358 8000",
        "founded_year": 1998,
        "logo_url": "https://upload.wikimedia.org/..."
      },
      {
        "id": 2,
        "name": "LPDP (Lembaga Pengelola Dana Pendidikan)",
        "type": "government",
        ...
      }
    ],
    "total": 5
  }
}
```

**Contoh Request:**

```bash
# Semua organizations
curl -X GET "http://localhost:8000/api/organizations/list/all" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by type
curl -X GET "http://localhost:8000/api/organizations/list/all?type=company" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Search by name
curl -X GET "http://localhost:8000/api/organizations/list/all?search=Google" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. List My Organizations (User's Own)

**Endpoint:** `GET /api/organizations`  
**Authentication:** Required  
**Purpose:** Melihat organizations milik user yang sedang login

**Response:**

```json
{
  "success": true,
  "message": "Organizations retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      // Hanya organizations milik user yang login
    ]
  }
}
```

### 3. Create Organization

**Endpoint:** `POST /api/organizations`  
**Authentication:** Required

### 4. Show Organization Details

**Endpoint:** `GET /api/organizations/{id}`  
**Authentication:** Required

### 5. Update Organization

**Endpoint:** `PUT /api/organizations/{id}`  
**Authentication:** Required (owner only)

### 6. Delete Organization

**Endpoint:** `DELETE /api/organizations/{id}`  
**Authentication:** Required (owner only)

### 7. Upload Organization Logo

**Endpoint:** `POST /api/organizations/{id}/logo`  
**Authentication:** Required (owner only)

### 8. Delete Organization Logo

**Endpoint:** `DELETE /api/organizations/{id}/logo`  
**Authentication:** Required (owner only)

## Data Seeder Organizations

Seeder telah membuat 5 organizations:

1. Google Indonesia (company)
2. LPDP - Lembaga Pengelola Dana Pendidikan (government)
3. Universitas Indonesia (university)
4. Ruangguru (company)
5. Bank Rakyat Indonesia - BRI (company)

## Testing

```bash
# Verify organizations in database
php artisan tinker --execute="echo App\Models\Organization::count();"

# Should return: 5

# Test endpoint (setelah login)
curl -X GET "http://localhost:8000/api/organizations/list/all" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Perbedaan Kedua Endpoint

| Feature  | `/api/organizations`     | `/api/organizations/list/all` |
| -------- | ------------------------ | ----------------------------- |
| Purpose  | User's own organizations | All organizations (catalog)   |
| Filter   | `user_id = Auth::id()`   | No user filter                |
| Use Case | Portfolio management     | Browse/Search organizations   |
| Access   | Owner only               | All authenticated users       |

## Catatan

- Endpoint lama (`/api/organizations`) tetap ada untuk manage organizations milik user
- Endpoint baru (`/api/organizations/list/all`) untuk katalog publik
- Keduanya memerlukan authentication token
