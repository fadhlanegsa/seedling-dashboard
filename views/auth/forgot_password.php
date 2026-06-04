<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Sandi - Dashboard Stok Bibit Indonesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1B5E20 0%, #4CAF50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
            pointer-events: none;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(217, 119, 6, 0.3);
        }

        .login-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 2.5rem 0.875rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(217, 119, 6, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-login {
            text-align: center;
            margin-top: 1.5rem;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .back-login a {
            color: #1B5E20;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .back-login a:hover {
            color: #4CAF50;
            text-decoration: underline;
        }

        .back-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            z-index: 10;
        }

        .back-home a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            background: rgba(255,255,255,0.2);
            border-radius: 50px;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }

        .back-home a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(-5px);
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            animation: shake 0.5s;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .back-home {
                top: 1rem;
                left: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="back-home">
        <a href="<?= url('public/landing') ?>">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Beranda
        </a>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Lupa Sandi?</h1>
                <p>Masukkan email terdaftar Anda untuk menerima link pemulihan kata sandi.</p>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('auth/process-forgot-password') ?>" method="POST">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label class="form-label">Email Terdaftar</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" required autofocus placeholder="nama@email.com">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Link Reset
                </button>
            </form>

            <div class="back-login">
                Ingat password Anda? <a href="<?= url('auth/login') ?>">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
