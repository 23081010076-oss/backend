REM 🧪 API Testing dengan CURL (Windows PowerShell)
REM Ganti YOUR_TOKEN dengan JWT token dari login

REM Setup Variables
SET BASE_URL=http://localhost:8000/api
SET TOKEN=YOUR_JWT_TOKEN
SET HEADER_AUTH=Authorization: Bearer %TOKEN%
SET HEADER_JSON=Content-Type: application/json

ECHO.
ECHO ==========================================
ECHO 🔐 STEP 1: Login ^& Get Token
ECHO ==========================================

curl -X POST "%BASE_URL%/auth/login" ^
  -H "%HEADER_JSON%" ^
  -d "{\"email\":\"user@example.com\",\"password\":\"password\"}"

ECHO.
ECHO.
ECHO ==========================================
ECHO 📦 STEP 2: Subscriptions
ECHO ==========================================

ECHO.
ECHO >>> Create Single Course Subscription
curl -X POST "%BASE_URL%/subscriptions" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"package_type\":\"single_course\",\"duration\":1,\"duration_unit\":\"months\",\"courses_ids\":[1]}"

ECHO.
ECHO >>> Create All-in-One Subscription
curl -X POST "%BASE_URL%/subscriptions" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"package_type\":\"all_in_one\",\"duration\":3,\"duration_unit\":\"months\",\"courses_ids\":[1,2,3,4,5]}"

ECHO.
ECHO >>> List All Subscriptions
curl -X GET "%BASE_URL%/subscriptions" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Get Subscription Detail
curl -X GET "%BASE_URL%/subscriptions/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Update Subscription
curl -X PUT "%BASE_URL%/subscriptions/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"duration\":3,\"auto_renew\":true}"

ECHO.
ECHO >>> Delete Subscription
curl -X DELETE "%BASE_URL%/subscriptions/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO.
ECHO ==========================================
ECHO 💳 STEP 3: Transactions
ECHO ==========================================

ECHO.
ECHO >>> Create Transaction
curl -X POST "%BASE_URL%/transactions" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"subscription_id\":2,\"payment_method\":\"qris\"}"

ECHO.
ECHO >>> List All Transactions
curl -X GET "%BASE_URL%/transactions" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Get Transaction Detail
curl -X GET "%BASE_URL%/transactions/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Confirm Payment
curl -X POST "%BASE_URL%/transactions/1/confirm" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{}"

ECHO.
ECHO >>> Process Refund
curl -X POST "%BASE_URL%/transactions/1/refund" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"refund_reason\":\"User request - change of plans\"}"

ECHO.
ECHO.
ECHO ==========================================
ECHO 📝 STEP 4: Need Assessment
ECHO ==========================================

ECHO.
ECHO >>> Submit Need Assessment Form
curl -X POST "%BASE_URL%/mentoring-sessions/1/need-assessments" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"form_data\":{\"learning_goals\":\"Menjadi full-stack developer\",\"previous_experience\":\"HTML/CSS/JavaScript dasar\",\"challenges\":\"Kesulitan async/await\",\"expectations\":\"Junior developer\"}}"

ECHO.
ECHO >>> Get Need Assessment Form
curl -X GET "%BASE_URL%/mentoring-sessions/1/need-assessments" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Mark Assessment as Completed
curl -X PUT "%BASE_URL%/mentoring-sessions/1/need-assessments/mark-completed" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{}"

ECHO.
ECHO >>> Delete Need Assessment
curl -X DELETE "%BASE_URL%/mentoring-sessions/1/need-assessments" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO.
ECHO ==========================================
ECHO 📁 STEP 5: Coaching Files
ECHO ==========================================

ECHO.
ECHO >>> Upload Coaching File (PDF)
curl -X POST "%BASE_URL%/mentoring-sessions/1/coaching-files" ^
  -H "%HEADER_AUTH%" ^
  -F "file_name=Slide-Week-1" ^
  -F "file=@slide-week-1.pdf" ^
  -F "file_type=pdf" ^
  -F "uploaded_by=1"

ECHO.
ECHO >>> List All Coaching Files
curl -X GET "%BASE_URL%/mentoring-sessions/1/coaching-files" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Get Coaching File Detail
curl -X GET "%BASE_URL%/mentoring-sessions/1/coaching-files/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Delete Single File
curl -X DELETE "%BASE_URL%/mentoring-sessions/1/coaching-files/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO.
ECHO ==========================================
ECHO 📊 STEP 6: Progress Reports
ECHO ==========================================

ECHO.
ECHO >>> Create Progress Report
curl -X POST "%BASE_URL%/progress-reports" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"enrollment_id\":1,\"report_date\":\"2025-11-17\",\"progress_percentage\":45,\"notes\":\"Sudah paham async/await\",\"attachment_url\":\"https://example.com/docs/progress.pdf\",\"frequency\":14}"

ECHO.
ECHO >>> List All Progress Reports
curl -X GET "%BASE_URL%/progress-reports" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Get Progress Report Detail
curl -X GET "%BASE_URL%/progress-reports/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Update Progress Report
curl -X PUT "%BASE_URL%/progress-reports/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"progress_percentage\":50,\"notes\":\"Updated: Sudah mengerti patterns\"}"

ECHO.
ECHO >>> Get Reports Per Enrollment
curl -X GET "%BASE_URL%/enrollments/1/progress-reports" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Set Report Frequency
curl -X POST "%BASE_URL%/progress-reports/frequency" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%" ^
  -d "{\"enrollment_id\":1,\"frequency\":7}"

ECHO.
ECHO >>> Get Due Reports
curl -X GET "%BASE_URL%/progress-reports/due" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO >>> Delete Progress Report
curl -X DELETE "%BASE_URL%/progress-reports/1" ^
  -H "%HEADER_AUTH%" ^
  -H "%HEADER_JSON%"

ECHO.
ECHO.
ECHO ==========================================
ECHO ✅ Testing Selesai
ECHO ==========================================
PAUSE
