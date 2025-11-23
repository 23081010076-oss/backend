<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            text-align: center;
            padding: 50px;
            border: 10px solid #787878;
        }
        .container {
            padding: 20px;
        }
        .header {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .sub-header {
            font-size: 20px;
            margin-bottom: 40px;
        }
        .recipient-name {
            font-size: 35px;
            font-weight: bold;
            margin: 20px 0;
            border-bottom: 2px solid #333;
            display: inline-block;
            padding-bottom: 10px;
            min-width: 400px;
        }
        .course-name {
            font-size: 25px;
            font-weight: bold;
            margin: 20px 0;
            color: #2980b9;
        }
        .content {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 50px;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 10px;
        }
        .date {
            font-size: 16px;
            margin-top: 20px;
        }
        .logo {
            margin-bottom: 30px;
            /* Add your logo styling here */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <!-- You can add an <img> tag here for the logo -->
            <h1>LMS PLATFORM</h1>
        </div>
        
        <div class="header">CERTIFICATE OF COMPLETION</div>
        
        <div class="sub-header">This is to certify that</div>
        
        <div class="recipient-name">{{ $user->name }}</div>
        
        <div class="content">
            has successfully completed the course
            <div class="course-name">{{ $course->title }}</div>
            demonstrating dedication and proficiency in the subject matter.
        </div>
        
        <div class="date">
            Date: {{ $date }}
        </div>
        
        <div class="signature">
            Authorized Signature
        </div>
    </div>
</body>
</html>
