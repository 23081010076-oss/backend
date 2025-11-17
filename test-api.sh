#!/bin/bash
# 🧪 API Testing dengan CURL
# Ganti YOUR_TOKEN dengan JWT token dari login

BASE_URL="http://localhost:8000/api"
TOKEN="YOUR_JWT_TOKEN"
HEADER_AUTH="Authorization: Bearer $TOKEN"
HEADER_JSON="Content-Type: application/json"

echo "=========================================="
echo "🔐 STEP 1: Login & Get Token"
echo "=========================================="
curl -X POST "$BASE_URL/auth/login" \
  -H "$HEADER_JSON" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }' | jq .

echo -e "\n\n=========================================="
echo "📦 STEP 2: Subscriptions"
echo "=========================================="

echo ">>> Create Single Course Subscription"
curl -X POST "$BASE_URL/subscriptions" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "package_type": "single_course",
    "duration": 1,
    "duration_unit": "months",
    "courses_ids": [1]
  }' | jq .

echo -e "\n>>> Create All-in-One Subscription"
curl -X POST "$BASE_URL/subscriptions" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "package_type": "all_in_one",
    "duration": 3,
    "duration_unit": "months",
    "courses_ids": [1, 2, 3, 4, 5]
  }' | jq .

echo -e "\n>>> List All Subscriptions"
curl -X GET "$BASE_URL/subscriptions" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Get Subscription Detail"
curl -X GET "$BASE_URL/subscriptions/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Update Subscription"
curl -X PUT "$BASE_URL/subscriptions/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "duration": 3,
    "auto_renew": true
  }' | jq .

echo -e "\n>>> Delete Subscription"
curl -X DELETE "$BASE_URL/subscriptions/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n\n=========================================="
echo "💳 STEP 3: Transactions"
echo "=========================================="

echo ">>> Create Transaction"
curl -X POST "$BASE_URL/transactions" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "subscription_id": 2,
    "payment_method": "qris"
  }' | jq .

echo -e "\n>>> List All Transactions"
curl -X GET "$BASE_URL/transactions" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Get Transaction Detail"
curl -X GET "$BASE_URL/transactions/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Confirm Payment"
curl -X POST "$BASE_URL/transactions/1/confirm" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{}' | jq .

echo -e "\n>>> Process Refund"
curl -X POST "$BASE_URL/transactions/1/refund" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "refund_reason": "User request - change of plans"
  }' | jq .

echo -e "\n\n=========================================="
echo "📝 STEP 4: Need Assessment"
echo "=========================================="

echo ">>> Submit Need Assessment Form"
curl -X POST "$BASE_URL/mentoring-sessions/1/need-assessments" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "form_data": {
      "learning_goals": "Menjadi full-stack developer yang kompeten",
      "previous_experience": "Sudah belajar HTML/CSS/JavaScript dasar",
      "challenges": "Kesulitan memahami async/await",
      "expectations": "Ingin dapat job sebagai junior developer"
    }
  }' | jq .

echo -e "\n>>> Get Need Assessment Form"
curl -X GET "$BASE_URL/mentoring-sessions/1/need-assessments" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Mark Assessment as Completed"
curl -X PUT "$BASE_URL/mentoring-sessions/1/need-assessments/mark-completed" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{}' | jq .

echo -e "\n>>> Delete Need Assessment"
curl -X DELETE "$BASE_URL/mentoring-sessions/1/need-assessments" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n\n=========================================="
echo "📁 STEP 5: Coaching Files"
echo "=========================================="

echo ">>> Upload Coaching File (PDF)"
curl -X POST "$BASE_URL/mentoring-sessions/1/coaching-files" \
  -H "$HEADER_AUTH" \
  -F "file_name=Slide-Week-1-Introduction" \
  -F "file=@./slide-week-1.pdf" \
  -F "file_type=pdf" \
  -F "uploaded_by=1" | jq .

echo -e "\n>>> Upload Video File"
curl -X POST "$BASE_URL/mentoring-sessions/1/coaching-files" \
  -H "$HEADER_AUTH" \
  -F "file_name=Video-Tutorial-AsyncAwait" \
  -F "file=@./tutorial-async-await.mp4" \
  -F "file_type=video" \
  -F "uploaded_by=1" | jq .

echo -e "\n>>> List All Coaching Files"
curl -X GET "$BASE_URL/mentoring-sessions/1/coaching-files" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Get Coaching File Detail"
curl -X GET "$BASE_URL/mentoring-sessions/1/coaching-files/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Download File"
curl -X GET "$BASE_URL/mentoring-sessions/1/coaching-files/1/download" \
  -H "$HEADER_AUTH" \
  -o downloaded-file.pdf

echo -e "\n>>> Delete Single File"
curl -X DELETE "$BASE_URL/mentoring-sessions/1/coaching-files/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Delete All Files"
curl -X DELETE "$BASE_URL/mentoring-sessions/1/coaching-files" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n\n=========================================="
echo "📊 STEP 6: Progress Reports"
echo "=========================================="

echo ">>> Create Progress Report"
curl -X POST "$BASE_URL/progress-reports" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "enrollment_id": 1,
    "report_date": "2025-11-17",
    "progress_percentage": 45,
    "notes": "Sudah paham konsep async/await dan promises",
    "attachment_url": "https://example.com/docs/progress.pdf",
    "frequency": 14
  }' | jq .

echo -e "\n>>> List All Progress Reports"
curl -X GET "$BASE_URL/progress-reports" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Get Progress Report Detail"
curl -X GET "$BASE_URL/progress-reports/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Update Progress Report"
curl -X PUT "$BASE_URL/progress-reports/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "progress_percentage": 50,
    "notes": "Updated: Sudah mengerti advanced patterns juga"
  }' | jq .

echo -e "\n>>> Get Reports Per Enrollment"
curl -X GET "$BASE_URL/enrollments/1/progress-reports" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Set Report Frequency"
curl -X POST "$BASE_URL/progress-reports/frequency" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" \
  -d '{
    "enrollment_id": 1,
    "frequency": 7
  }' | jq .

echo -e "\n>>> Get Due/Overdue Reports"
curl -X GET "$BASE_URL/progress-reports/due" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n>>> Delete Progress Report"
curl -X DELETE "$BASE_URL/progress-reports/1" \
  -H "$HEADER_AUTH" \
  -H "$HEADER_JSON" | jq .

echo -e "\n\n=========================================="
echo "✅ Testing Selesai"
echo "=========================================="
