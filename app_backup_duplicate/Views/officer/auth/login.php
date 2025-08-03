<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>अधिकारी लॉगिन - महाराष्ट्र पोलीस ड्यूटी व्यवस्थापन</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            color: white;
            text-align: center;
            padding: 2rem 1rem;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 82, 152, 0.4);
        }
        .police-badge {
            font-size: 2.5rem;
            color: #FFD700;
            margin-bottom: 1rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
        }
        .mobile-info {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #2a5298;
        }
        @media (max-width: 576px) {
            .login-body {
                padding: 1.5rem;
            }
            .login-header {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-shield police-badge"></i>
                <h2>अधिकारी लॉगिन</h2>
                <p class="mb-0 small">महाराष्ट्र पोलीस ड्यूटी ट्रॅकिंग</p>
            </div>
            <div class="login-body">
                <div class="mobile-info">
                    <small>
                        <i class="fas fa-mobile-alt me-2"></i>
                        <strong>मोबाइल ट्रॅकिंग:</strong> लॉगिन केल्यानंतर आपले स्थान ट्रॅक करण्यासाठी परवानगी द्या
                    </small>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('officer/login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="badge_no" class="form-label">
                            <i class="fas fa-id-badge me-2"></i>बॅज नंबर
                        </label>
                        <input type="text" class="form-control" id="badge_no" name="badge_no" 
                               value="<?= old('badge_no') ?>" placeholder="उदा: MH001" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>पासवर्ड
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>लॉगिन करा
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        केवळ अधिकृत पोलीस अधिकाऱ्यांसाठी
                    </small>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= base_url('admin/login') ?>" class="text-decoration-none small">
                        <i class="fas fa-user-cog me-1"></i>प्रशासक लॉगिन
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
