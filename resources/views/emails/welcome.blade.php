<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            background: #374151;
            color: #9ca3af;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .features {
            margin: 20px 0;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Learning Platform</h1>
        <p>Selamat Datang!</p>
    </div>
    
    <div class="content">
        <h2>Halo, {{ $userName }}! 👋</h2>
        
        <p>Terima kasih telah bergabung dengan <strong>Learning Platform</strong>. Kami sangat senang Anda menjadi bagian dari komunitas pembelajaran kami.</p>
        
        @if($userRole === 'mentor')
        <p>Sebagai <strong>Mentor</strong>, Anda dapat:</p>
        <ul class="features">
            <li>📚 Membuat dan mengelola kursus</li>
            <li>📝 Menulis artikel edukatif</li>
            <li>💬 Menawarkan sesi mentoring</li>
            <li>📊 Melihat statistik performa</li>
        </ul>
        @else
        <p>Sebagai <strong>User</strong>, Anda dapat:</p>
        <ul class="features">
            <li>📚 Mengakses berbagai kursus berkualitas</li>
            <li>📝 Membaca artikel dari para ahli</li>
            <li>💬 Booking sesi mentoring</li>
            <li>🏆 Melamar beasiswa</li>
        </ul>
        @endif
        
        <p>Mulai jelajahi platform kami dan tingkatkan skill Anda!</p>
        
        <center>
            <a href="{{ config('app.frontend_url', config('app.url')) }}" class="btn">
                Mulai Belajar →
            </a>
        </center>
        
        <p>Jika ada pertanyaan, jangan ragu untuk menghubungi tim support kami.</p>
        
        <p>Salam hangat,<br>
        <strong>Tim Learning Platform</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Learning Platform. All rights reserved.</p>
        <p>Email ini dikirim otomatis, mohon tidak membalas.</p>
    </div>
</body>
</html>
