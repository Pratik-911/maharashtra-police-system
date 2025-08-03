<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>महाराष्ट्र पोलीस ड्यूटी व्यवस्थापन प्रणाली</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .hero-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
        }
        
        .logo {
            font-size: 5rem;
            color: #FFD700;
            margin-bottom: 2rem;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .login-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-login {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        
        .btn-admin {
            background: linear-gradient(45deg, #FF6B35, #F7931E);
            color: white;
            border: none;
        }
        
        .btn-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.4);
            color: white;
        }
        
        .btn-officer {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            color: white;
            border: none;
        }
        
        .btn-officer:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.4);
            color: white;
        }
        
        .features {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feature-item {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .feature-item i {
            margin-right: 10px;
            color: #FFD700;
        }
        
        @media (max-width: 768px) {
            .hero-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .logo {
                font-size: 4rem;
            }
            
            .login-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="hero-card">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            
            <h1 class="hero-title">महाराष्ट्र पोलीस</h1>
            <h2 class="hero-subtitle">ड्यूटी व्यवस्थापन प्रणाली</h2>
            
            <p class="mb-4">
                स्थान ट्रॅकिंग आणि अनुपालन मोजमापासह आधुनिक ड्यूटी व्यवस्थापन
            </p>
            
            <div class="login-buttons">
                <a href="/admin/login" class="btn btn-login btn-admin">
                    <i class="fas fa-user-cog me-2"></i>
                    प्रशासक लॉगिन
                </a>
                <a href="/officer/login" class="btn btn-login btn-officer">
                    <i class="fas fa-user-shield me-2"></i>
                    अधिकारी लॉगिन
                </a>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>रिअल-टाइम स्थान ट्रॅकिंग</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>अनुपालन मोजमाप आणि रिपोर्टिंग</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>मोबाइल-फ्रेंडली इंटरफेस</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>सुरक्षित आणि एन्क्रिप्टेड</span>
                </div>
            </div>
            
            <div class="mt-4">
                <small class="text-light">
                    <i class="fas fa-info-circle me-1"></i>
                    केवळ अधिकृत कर्मचाऱ्यांसाठी प्रवेश
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
