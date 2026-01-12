# Scholarship API - Request Body Examples

## 1. Create Scholarship (POST /api/scholarships)

### Open Status - Full Information

```json
{
  "name": "Beasiswa Unggulan Kemendikbud 2025",
  "description": "Program beasiswa penuh untuk mahasiswa berprestasi tinggi yang ingin melanjutkan studi S2/S3 di dalam dan luar negeri",
  "status": "open",
  "benefit": "Biaya kuliah penuh, tunjangan hidup bulanan, biaya penelitian, tiket pesawat PP",
  "location": "Dalam Negeri dan Luar Negeri",
  "deadline": "2025-03-31",
  "study_field": "Teknik, Sains, Sosial Humaniora",
  "funding_amount": 500000000,
  "requirements": "IPK min 3.5, Surat rekomendasi dari dosen, Proposal penelitian, Sertifikat prestasi",
  "organization_id": 1,
  "provider_id": "DIKTI-2025-001"
}
```

### Coming Soon Status

```json
{
  "name": "Beasiswa LPDP 2026",
  "description": "Beasiswa dari Lembaga Pengelola Dana Pendidikan untuk studi S2 dan S3",
  "status": "coming_soon",
  "benefit": "Dana pendidikan penuh, biaya hidup, asuransi kesehatan",
  "location": "Luar Negeri (Top 200 QS Ranking)",
  "study_field": "All fields",
  "funding_amount": 750000000,
  "requirements": "WNI, Berkomitmen kembali ke Indonesia, LoA unconditional",
  "organization_id": 2
}
```

### Closed Status - Corporate Scholarship

```json
{
  "name": "Beasiswa Bank Indonesia Scholarship Program",
  "description": "Program pengembangan SDM perbankan dan ekonomi",
  "status": "closed",
  "benefit": "Biaya kuliah, tunjangan bulanan, magang di BI",
  "location": "Dalam Negeri",
  "deadline": "2024-12-31",
  "study_field": "Ekonomi, Manajemen, Akuntansi",
  "funding_amount": 300000000,
  "requirements": "Mahasiswa aktif tahun ke-3 atau lebih, IPK min 3.25"
}
```

### Minimal Required Fields

```json
{
  "name": "Beasiswa Prestasi Universitas",
  "status": "open"
}
```

---

## 2. Update Scholarship (PUT /api/scholarships/{id})

### Update Status and Deadline

```json
{
  "status": "closed",
  "deadline": "2025-02-28"
}
```

### Update Funding Amount

```json
{
  "funding_amount": 600000000,
  "benefit": "Biaya kuliah penuh, tunjangan hidup Rp 5 juta/bulan, biaya penelitian Rp 10 juta/tahun"
}
```

### Extend Deadline (Keep Open)

```json
{
  "deadline": "2025-06-30",
  "status": "open"
}
```

### Complete Update

```json
{
  "name": "Beasiswa Unggulan Kemendikbud 2025 (Extended)",
  "description": "Program beasiswa penuh untuk mahasiswa berprestasi - perpanjangan periode pendaftaran",
  "status": "open",
  "benefit": "Biaya kuliah penuh, tunjangan hidup bulanan Rp 7 juta, biaya penelitian, tiket pesawat PP",
  "location": "Dalam Negeri dan Luar Negeri (50+ negara)",
  "deadline": "2025-06-30",
  "study_field": "Semua bidang studi",
  "funding_amount": 550000000,
  "requirements": "IPK min 3.5, Surat rekomendasi 2 dosen, Proposal penelitian, TOEFL min 550/IELTS 6.5"
}
```

---

## 3. Apply for Scholarship (POST /api/scholarships/{id}/apply)

**Note**: Semua field bersifat opsional, request menggunakan `multipart/form-data`

### With All Documents

```
Content-Type: multipart/form-data

motivation_letter: [FILE] - PDF/DOC/DOCX, max 2MB
cv_path: [FILE] - PDF, max 2MB
transcript_path: [FILE] - PDF, max 2MB
recommendation_path: [FILE] - PDF/DOC/DOCX, max 2MB
```

### Minimal Application (No Documents)

```
Content-Type: multipart/form-data

(Empty body - valid, semua field opsional)
```

### Example using cURL

```bash
curl -X POST "http://localhost:8000/api/scholarships/1/apply" \
  -H "Authorization: Bearer {studentToken}" \
  -F "motivation_letter=@/path/to/motivation.pdf" \
  -F "cv_path=@/path/to/cv.pdf" \
  -F "transcript_path=@/path/to/transcript.pdf" \
  -F "recommendation_path=@/path/to/recommendation.pdf"
```

---

## 4. Update Application Status (PUT /api/scholarship-applications/{id}/status)

**Note**: Admin/Corporate only

### Accept Application

```json
{
  "status": "accepted"
}
```

### Reject Application

```json
{
  "status": "rejected"
}
```

### Move to Review

```json
{
  "status": "review"
}
```

### Back to Submitted

```json
{
  "status": "submitted"
}
```

---

## Field Specifications

### Create Scholarship

#### Required Fields

- `name` - string, max 255 characters (Nama beasiswa)
- `status` - enum: `open`, `coming_soon`, `closed`

#### Optional Fields

- `description` - string (Deskripsi lengkap beasiswa)
- `benefit` - string (Keuntungan/fasilitas yang didapat)
- `location` - string (Lokasi studi)
- `deadline` - date (Format: YYYY-MM-DD)
- `study_field` - string, max 255 characters (Bidang studi)
- `funding_amount` - number, min 0 (Jumlah dana dalam Rupiah)
- `requirements` - string (Persyaratan pendaftaran)
- `organization_id` - integer (ID organisasi penyedia)
- `provider_id` - string, max 255 (ID eksternal dari provider)

### Update Scholarship

Semua field bersifat opsional (gunakan field yang ingin diubah saja)

### Apply for Scholarship

#### Optional Files (All Optional)

- `motivation_letter` - file (PDF, DOC, DOCX), max 2MB
- `cv_path` - file (PDF only), max 2MB
- `transcript_path` - file (PDF only), max 2MB
- `recommendation_path` - file (PDF, DOC, DOCX), max 2MB

### Update Application Status

#### Required Field

- `status` - enum: `submitted`, `review`, `accepted`, `rejected`

---

## Query Parameters

### Filter Scholarships (GET /api/scholarships)

```
GET /api/scholarships?status=open
GET /api/scholarships?status=coming_soon
GET /api/scholarships?status=closed
```

### With Pagination

```
GET /api/scholarships?page=1&per_page=10
GET /api/scholarships?status=open&page=1
```

---

## Response Examples

### Success Create (201)

```json
{
  "sukses": true,
  "pesan": "Beasiswa berhasil ditambahkan",
  "data": {
    "id": 15,
    "name": "Beasiswa Unggulan Kemendikbud 2025",
    "description": "Program beasiswa penuh...",
    "status": "open",
    "benefit": "Biaya kuliah penuh...",
    "location": "Dalam Negeri dan Luar Negeri",
    "deadline": "2025-03-31",
    "study_field": "Teknik, Sains, Sosial Humaniora",
    "funding_amount": 500000000,
    "organization_id": 1,
    "created_at": "2025-12-24T11:00:00.000000Z",
    "updated_at": "2025-12-24T11:00:00.000000Z"
  }
}
```

### Success Apply (201)

```json
{
  "sukses": true,
  "pesan": "Lamaran beasiswa berhasil dikirim",
  "data": {
    "id": 42,
    "user_id": 6,
    "scholarship_id": 15,
    "motivation_letter": "storage/scholarships/motivation_letters/abc123.pdf",
    "cv_path": "storage/scholarships/cvs/def456.pdf",
    "transcript_path": "storage/scholarships/transcripts/ghi789.pdf",
    "recommendation_path": "storage/scholarships/recommendations/jkl012.pdf",
    "status": "submitted",
    "created_at": "2025-12-24T11:30:00.000000Z"
  }
}
```

### Error Validation (422)

```json
{
  "sukses": false,
  "pesan": "Validasi gagal",
  "errors": {
    "name": ["Nama beasiswa wajib diisi"],
    "status": ["Status harus open, coming_soon, atau closed"],
    "deadline": ["Format deadline tidak valid"],
    "cv_path": ["Format CV harus pdf"]
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

### Error Already Applied (422)

```json
{
  "sukses": false,
  "pesan": "Anda sudah mendaftar beasiswa ini sebelumnya"
}
```

---

## Usage Notes

### Status Flow

1. **coming_soon** → Beasiswa diumumkan tapi belum bisa didaftar
2. **open** → Beasiswa dibuka untuk pendaftaran
3. **closed** → Beasiswa ditutup, tidak menerima pendaftaran baru

### Application Status Flow

1. **submitted** → Lamaran baru disubmit oleh student
2. **review** → Sedang direview oleh admin/corporate
3. **accepted** → Lamaran diterima
4. **rejected** → Lamaran ditolak

### File Upload Rules

- Ukuran maksimal per file: 2MB
- Format CV & Transkrip: PDF only
- Format Motivation Letter & Recommendation: PDF, DOC, DOCX
- Semua file disimpan di `storage/app/public/scholarships/`

### Authorization

- **Create/Update/Delete Scholarship**: Admin & Corporate only
- **Apply for Scholarship**: Student only (user yang login)
- **Update Application Status**: Admin & Corporate only
- **View Scholarships**: Public (semua user)
- **View Applications**: Admin, Corporate, atau pemilik aplikasi
