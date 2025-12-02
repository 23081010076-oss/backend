<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', serif;
            background: #fff;
        }
        .certificate {
            width: 800px;
            min-height: 600px;
            margin: 0 auto;
            padding: 40px;
            border: 3px solid #1a365d;
            position: relative;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid #667eea;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 36px;
            color: #1a365d;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 14px;
            color: #667eea;
            margin-top: 5px;
            letter-spacing: 2px;
        }
        .content {
            text-align: center;
            padding: 30px 0;
        }
        .content .label {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .content .name {
            font-size: 42px;
            color: #1a365d;
            font-style: italic;
            margin: 20px 0;
            border-bottom: 2px solid #667eea;
            display: inline-block;
            padding: 0 30px 10px;
        }
        .content .description {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.8;
            margin: 20px 50px;
        }
        .content .course-name {
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
            margin: 15px 0;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 50px;
        }
        .footer .column {
            text-align: center;
        }
        .footer .line {
            width: 150px;
            border-bottom: 1px solid #4a5568;
            margin-bottom: 5px;
        }
        .footer .label {
            font-size: 12px;
            color: #718096;
        }
        .footer .value {
            font-size: 14px;
            color: #1a365d;
            margin-top: 5px;
        }
        .certificate-id {
            position: absolute;
            bottom: 20px;
            right: 30px;
            font-size: 10px;
            color: #a0aec0;
        }
        .logo {
            position: absolute;
            top: 30px;
            left: 30px;
            font-size: 18px;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="logo">🎓 Learning Platform</div>
        
        <div class="header">
            <h1>Certificate</h1>
            <div class="subtitle">of Completion</div>
        </div>
        
        <div class="content">
            <div class="label">This is to certify that</div>
            <div class="name">{{ $user_name }}</div>
            
            <div class="description">
                has successfully completed the course
            </div>
            
            <div class="course-name">"{{ $course_name }}"</div>
            
            <div class="description">
                Demonstrating dedication to professional development and commitment to learning excellence.
            </div>
        </div>
        
        <div class="footer">
            <div class="column">
                <div class="line"></div>
                <div class="label">Completion Date</div>
                <div class="value">{{ \Carbon\Carbon::parse($completion_date)->format('d F Y') }}</div>
            </div>
            
            <div class="column">
                <div class="line"></div>
                <div class="label">Authorized Signature</div>
                <div class="value">Learning Platform</div>
            </div>
        </div>
        
        <div class="certificate-id">Certificate ID: {{ $certificate_id }}</div>
    </div>
</body>
</html>
