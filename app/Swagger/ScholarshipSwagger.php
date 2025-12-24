<?php

namespace App\Swagger;

/**
 * @OA\Get(
 * path="/api/scholarships",
 * summary="Get all scholarships",
 * description="Get list of all available scholarships with filtering and sorting options",
 * operationId="getScholarships",
 * tags={"Scholarships"},
 * @OA\Parameter(
 * name="status",
 * in="query",
 * description="Filter by scholarship status",
 * required=false,
 * @OA\Schema(type="string", enum={"open", "coming_soon", "closed"})
 * ),
 * @OA\Parameter(
 * name="location",
 * in="query",
 * description="Filter by location",
 * required=false,
 * @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 * name="study_field",
 * in="query",
 * description="Filter by study field",
 * required=false,
 * @OA\Schema(type="string", example="Computer Science")
 * ),
 * @OA\Parameter(
 * name="search",
 * in="query",
 * description="Search by name or description",
 * required=false,
 * @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 * name="sort",
 * in="query",
 * description="Sort by: popular (by applications count), deadline, or latest",
 * required=false,
 * @OA\Schema(type="string", enum={"popular", "deadline", "latest"}, default="latest")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarships retrieved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Daftar beasiswa berhasil diambil"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="data", type="array",
 * @OA\Items(
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="organization_id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Beasiswa LPDP untuk Magister dan Doktor"),
 * @OA\Property(property="description", type="string", example="Program beasiswa penuh dari pemerintah Indonesia untuk studi S2 dan S3 dalam dan luar negeri"),
 * @OA\Property(property="benefit", type="string", example="Biaya pendidikan penuh, Biaya hidup bulanan, Asuransi kesehatan, Biaya keberangkatan"),
 * @OA\Property(property="location", type="string", example="Indonesia dan Luar Negeri"),
 * @OA\Property(property="study_field", type="string", example="Semua bidang studi"),
 * @OA\Property(property="status", type="string", example="open"),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-06-30"),
 * @OA\Property(property="applications_count", type="integer", example=5, description="Number of applications received"),
 * @OA\Property(property="organization", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Lembaga Pengelola Dana Pendidikan"),
 * @OA\Property(property="email", type="string", example="info@lpdp.kemenkeu.go.id")
 * )
 * )
 * ),
 * @OA\Property(property="per_page", type="integer", example=15),
 * @OA\Property(property="total", type="integer", example=50)
 * )
 * )
 * )
 * )
 *
 * @OA\Get(
 * path="/api/scholarships/{id}",
 * summary="Get scholarship detail",
 * description="Get detailed information about a specific scholarship",
 * operationId="getScholarshipById",
 * tags={"Scholarships"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship detail retrieved"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 *
 * @OA\Post(
 * path="/api/scholarships",
 * summary="Create scholarship",
 * description="Create a new scholarship (Admin only)",
 * operationId="createScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"name","description"},
 * @OA\Property(property="name", type="string", example="New Scholarship Program"),
 * @OA\Property(property="description", type="string", example="Description of the scholarship"),
 * @OA\Property(property="benefit", type="string", example="Full tuition, living expenses"),
 * @OA\Property(property="location", type="string", example="United States"),
 * @OA\Property(property="status", type="string", enum={"open", "coming_soon", "closed"}),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-06-30")
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Scholarship created successfully"
 * ),
 * @OA\Response(
 * response=401,
 * description="Unauthenticated"
 * ),
 * @OA\Response(
 * response=403,
 * description="Unauthorized"
 * )
 * )
 * * @OA\Put(
 * path="/api/scholarships/{id}",
 * summary="Update scholarship",
 * description="Update an existing scholarship (Admin only)",
 * operationId="updateScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * @OA\Property(property="name", type="string", example="Updated Program Name"),
 * @OA\Property(property="description", type="string", example="Updated description"),
 * @OA\Property(property="benefit", type="string", example="Updated benefits"),
 * @OA\Property(property="location", type="string", example="UK"),
 * @OA\Property(property="status", type="string", enum={"open", "coming_soon", "closed"}),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-12-31")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship updated successfully"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 * * @OA\Delete(
 * path="/api/scholarships/{id}",
 * summary="Delete scholarship",
 * description="Remove a scholarship from the system (Admin only)",
 * operationId="deleteScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship deleted successfully"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 *
 * @OA\Post(
 * path="/api/scholarships/{id}/apply",
 * summary="Apply for scholarship (Direct Submit)",
 * description="Submit scholarship application directly with documents. Use /draft for step-by-step flow.",
 * operationId="applyScholarship",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="ID of the scholarship",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\MediaType(
 * mediaType="multipart/form-data",
 * @OA\Schema(
 * @OA\Property(property="motivation_letter", type="string", format="binary", description="Motivation letter file (PDF/DOC)"),
 * @OA\Property(property="cv_path", type="string", format="binary", description="CV file (PDF)"),
 * @OA\Property(property="transcript_path", type="string", format="binary", description="Transcript file (PDF)"),
 * @OA\Property(property="recommendation_path", type="string", format="binary", description="Recommendation letter (PDF)")
 * )
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Application submitted successfully"
 * ),
 * @OA\Response(
 * response=422,
 * description="Validation error"
 * )
 * )
 *
 * @OA\Post(
 * path="/api/scholarships/{id}/draft",
 * summary="Step 1: Save Draft Application",
 * description="Create a draft scholarship application with uploaded documents. This is the first step in the application flow.",
 * operationId="saveDraftApplication",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\MediaType(
 * mediaType="multipart/form-data",
 * @OA\Schema(
 * @OA\Property(property="cv_path", type="string", format="binary", description="CV file (PDF) - Required for submit"),
 * @OA\Property(property="transcript_path", type="string", format="binary", description="Grade transcript (PDF)"),
 * @OA\Property(property="recommendation_path", type="string", format="binary", description="Recommendation letter (PDF/DOC)"),
 * @OA\Property(property="motivation_letter", type="string", format="binary", description="Motivation letter file (PDF/DOC) - optional if using text"),
 * @OA\Property(property="motivation_letter_text", type="string", description="Motivation letter as text - optional if uploading file")
 * )
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Draft saved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Draft lamaran berhasil disimpan"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="user_id", type="integer", example=1),
 * @OA\Property(property="scholarship_id", type="integer", example=1),
 * @OA\Property(property="status", type="string", example="draft"),
 * @OA\Property(property="cv_path", type="string", example="scholarship-docs/cv.pdf"),
 * @OA\Property(property="motivation_letter_text", type="string", example="Saya sangat tertarik...")
 * )
 * )
 * ),
 * @OA\Response(response=422, description="Validation error or already applied")
 * )
 *
 * @OA\Put(
 * path="/api/scholarship-applications/{id}/assessment",
 * summary="Step 2: Update Pre-Assessment Data",
 * description="Fill in pre-assessment form (GPA, other scholarship, parent income, university)",
 * operationId="updateAssessment",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Application ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * @OA\Property(property="gpa", type="number", format="float", example=3.75, description="GPA (0-4)"),
 * @OA\Property(property="has_other_scholarship", type="boolean", example=false, description="Apakah sedang menerima beasiswa lain?"),
 * @OA\Property(property="parent_income", type="integer", example=5000000, description="Penghasilan orang tua (Rupiah)"),
 * @OA\Property(property="university", type="string", example="Universitas Indonesia", description="Nama universitas")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Assessment saved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Data pre-assessment berhasil disimpan"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="gpa", type="number", example=3.75),
 * @OA\Property(property="has_other_scholarship", type="boolean", example=false),
 * @OA\Property(property="parent_income", type="integer", example=5000000),
 * @OA\Property(property="university", type="string", example="Universitas Indonesia")
 * )
 * )
 * ),
 * @OA\Response(response=403, description="Not your application"),
 * @OA\Response(response=422, description="Can only update draft")
 * )
 *
 * @OA\Get(
 * path="/api/scholarship-applications/{id}",
 * summary="Step 3: Get Application Detail (Review)",
 * description="Get detailed application data for review before submitting",
 * operationId="getApplicationDetail",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Application ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Application detail retrieved",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Detail lamaran berhasil diambil"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="status", type="string", example="draft"),
 * @OA\Property(property="cv_path", type="string", example="scholarship-docs/cv.pdf"),
 * @OA\Property(property="transcript_path", type="string", example="scholarship-docs/transcript.pdf"),
 * @OA\Property(property="recommendation_path", type="string", example="scholarship-docs/recommendation.pdf"),
 * @OA\Property(property="motivation_letter", type="string", nullable=true),
 * @OA\Property(property="motivation_letter_text", type="string", example="Saya sangat tertarik..."),
 * @OA\Property(property="gpa", type="number", example=3.75),
 * @OA\Property(property="has_other_scholarship", type="boolean", example=false),
 * @OA\Property(property="parent_income", type="integer", example=5000000),
 * @OA\Property(property="university", type="string", example="Universitas Indonesia"),
 * @OA\Property(property="scholarship", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Beasiswa LPDP")
 * )
 * )
 * )
 * ),
 * @OA\Response(response=404, description="Application not found")
 * )
 *
 * @OA\Put(
 * path="/api/scholarship-applications/{id}/draft",
 * summary="Step 3b: Update Draft Documents",
 * description="Update documents in draft application before submitting",
 * operationId="updateDraftApplication",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Application ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\MediaType(
 * mediaType="multipart/form-data",
 * @OA\Schema(
 * @OA\Property(property="cv_path", type="string", format="binary", description="CV file (PDF)"),
 * @OA\Property(property="transcript_path", type="string", format="binary", description="Grade transcript (PDF)"),
 * @OA\Property(property="recommendation_path", type="string", format="binary", description="Recommendation letter (PDF/DOC)"),
 * @OA\Property(property="motivation_letter", type="string", format="binary", description="Motivation letter file"),
 * @OA\Property(property="motivation_letter_text", type="string", description="Motivation letter as text")
 * )
 * )
 * ),
 * @OA\Response(response=200, description="Draft updated successfully"),
 * @OA\Response(response=403, description="Not your application"),
 * @OA\Response(response=422, description="Can only update draft")
 * )
 *
 * @OA\Post(
 * path="/api/scholarship-applications/{id}/submit",
 * summary="Step 4: Submit Application",
 * description="Submit the draft application. Changes status from 'draft' to 'submitted'. CV is required.",
 * operationId="submitApplication",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Application ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Application submitted successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Lamaran berhasil dikirim"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="status", type="string", example="submitted"),
 * @OA\Property(property="submitted_at", type="string", format="datetime", example="2024-12-24T12:30:00Z")
 * )
 * )
 * ),
 * @OA\Response(response=403, description="Not your application"),
 * @OA\Response(response=422, description="Can only submit draft or CV required")
 * )
 *
 * @OA\Get(
 * path="/api/my-applications",
 * summary="Get my applications",
 * description="Get list of current user's scholarship applications",
 * operationId="getMyApplications",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Response(
 * response=200,
 * description="Applications retrieved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Daftar lamaran berhasil diambil"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="data", type="array",
 * @OA\Items(
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="status", type="string", enum={"draft","submitted","review","accepted","rejected"}),
 * @OA\Property(property="scholarship", type="object",
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="name", type="string")
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 *
 * @OA\Put(
 * path="/api/scholarship-applications/{id}/status",
 * summary="Update application status (Admin)",
 * description="Update the status of a scholarship application (Admin/Corporate only)",
 * operationId="updateApplicationStatus",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Application ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"status"},
 * @OA\Property(
 * property="status",
 * type="string",
 * example="accepted",
 * enum={"draft", "submitted", "review", "accepted", "rejected"},
 * description="The new status"
 * )
 * )
 * ),
 * @OA\Response(response=200, description="Status updated successfully"),
 * @OA\Response(response=403, description="Unauthorized"),
 * @OA\Response(response=422, description="Invalid status")
 * )
 */
class ScholarshipSwagger {}

