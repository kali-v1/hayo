<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - الصفحة غير موجودة</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-page {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }
        
        .error-page .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        
        .error-page .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">404</div>
        <div class="error-message">عذراً، الصفحة التي تبحث عنها غير موجودة</div>
        <p class="mb-4">قد تكون الصفحة قد تم نقلها أو حذفها أو أن الرابط الذي اتبعته غير صحيح.</p>
        <div>
            <a href="/admin" class="btn btn-primary">
                <i class="fas fa-home me-2"></i> العودة إلى الرئيسية
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>