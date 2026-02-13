<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Login · Warung Seblang POS</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(145deg, #f0f5fe 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        /* Main card */
        .login-card {
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 40px 24px 40px 24px;
            box-shadow: 0 30px 60px rgba(18, 52, 102, 0.12), 0 8px 20px rgba(0, 40, 80, 0.08);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* LEFT - FORM SECTION */
        .form-section {
            flex: 1 1 45%;
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .brand-mobile {
            display: none;
            font-size: 26px;
            font-weight: 700;
            color: #0a4b7a;
            margin-bottom: 20px;
        }

        .brand-mobile i {
            margin-right: 8px;
            color: #2a7faa;
        }

        .form-section h2 {
            font-size: 34px;
            font-weight: 700;
            color: #0a2e4a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .welcome-tag {
            font-size: 15px;
            color: #4a657b;
            margin-bottom: 36px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #e1ecfe;
            padding-bottom: 18px;
        }

        .welcome-tag i {
            color: #2a7faa;
        }

        /* Input group */
        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e4a6b;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background: #f5faff;
            border-radius: 16px;
            padding: 4px 18px;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .input-wrapper:focus-within {
            background: white;
            border-color: #2a7faa;
            box-shadow: 0 8px 18px rgba(42, 127, 170, 0.08);
        }

        .input-wrapper i {
            color: #5e7e9c;
            font-size: 16px;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .input-wrapper input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 14px 0;
            font-size: 15px;
            color: #0a2e4a;
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: #99b4cc;
            font-weight: 400;
            font-size: 14px;
        }

        /* Password toggle wrapper */
        .password-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .toggle-password {
            background: none;
            border: none;
            color: #5e7e9c;
            font-size: 16px;
            padding: 0 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #0a4b7a;
        }

        .toggle-password i {
            margin-right: 0;
            font-size: 18px;
        }

        /* Form options */
        .form-extra {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 8px 0 32px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2c5775;
        }

        .remember input {
            width: 18px;
            height: 18px;
            accent-color: #2a7faa;
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-link {
            color: #2a7faa;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .forgot-link:hover {
            color: #0a4b7a;
            text-decoration: underline;
        }

        /* Login button */
        .btn-login {
            background: linear-gradient(105deg, #1a6392, #0a4b7a);
            border: none;
            border-radius: 40px;
            padding: 16px 24px;
            color: white;
            font-weight: 700;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 12px 25px -8px rgba(10, 75, 122, 0.4);
            width: 100%;
            letter-spacing: 1px;
        }

        .btn-login i {
            font-size: 16px;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            background: linear-gradient(105deg, #0a4b7a, #083b5e);
            box-shadow: 0 18px 30px -8px rgba(8, 59, 94, 0.5);
            transform: translateY(-2px);
        }

        .btn-login:hover i {
            transform: translateX(5px);
        }

        /* Error message */
        .error-message {
            background: #f0f7ff;
            border-left: 6px solid #2a7faa;
            padding: 14px 18px;
            border-radius: 12px;
            color: #0a4b7a;
            font-size: 14px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .error-message i {
            font-size: 20px;
            color: #2a7faa;
        }

        /* RIGHT - HERO SECTION */
        .hero-section {
            flex: 1 1 45%;
            background: linear-gradient(145deg, #1a6392, #0a3e5e);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 32px;
            position: relative;
            color: white;
            border-top-left-radius: 120px;
            border-bottom-left-radius: 120px;
        }

        .brand-icon {
            font-size: 90px;
            color: white;
            text-shadow: 0 15px 25px rgba(0,0,0,0.15);
            margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            backdrop-filter: blur(4px);
            border: 2px solid rgba(255,255,255,0.2);
        }

        .hero-section h1 {
            font-size: 38px;
            font-weight: 800;
            color: white;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .hero-section h1 span {
            background: rgba(255,255,255,0.15);
            padding: 4px 14px;
            border-radius: 100px;
            font-size: 30px;
            backdrop-filter: blur(4px);
        }

        .hero-desc {
            font-size: 18px;
            color: rgba(255,255,255,0.95);
            line-height: 1.5;
            text-align: center;
            max-width: 280px;
            font-weight: 500;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(4px);
            padding: 18px 26px;
            border-radius: 40px 16px 40px 16px;
            margin-top: 15px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .hero-desc i {
            color: #b3e4ff;
            margin: 0 4px;
        }

        .feature-badge {
            display: flex;
            gap: 20px;
            margin-top: 35px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .badge {
            background: rgba(255,255,255,0.12);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .badge i {
            color: #b3e4ff;
        }

        /* RESPONSIVE */
        @media screen and (max-width: 800px) {
            .login-card {
                flex-direction: column-reverse;
                border-radius: 36px;
            }

            .brand-mobile {
                display: block;
            }

            .hero-section {
                border-radius: 0 0 40px 40px;
                padding: 40px 24px;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }

            .brand-icon {
                width: 110px;
                height: 110px;
                font-size: 70px;
            }

            .hero-section h1 {
                font-size: 32px;
            }

            .form-section {
                padding: 44px 36px;
            }
        }

        @media screen and (max-width: 480px) {
            .form-section {
                padding: 36px 24px;
            }
            
            .hero-section {
                display: none; /* Clean: hero hidden on small mobile, only form */
            }
            
            .brand-mobile {
                display: block;
            }
            
            .login-card {
                border-radius: 28px;
            }
        }

        /* Blue theme consistency */
        .warung-tagline {
            margin-top: 40px;
            font-size: 13px;
            color: #6b8da8;
            text-align: center;
            border-top: 1px solid #e1ecfe;
            padding-top: 20px;
        }
        
        .warung-tagline i {
            color: #2a7faa;
            margin: 0 5px;
        }
    </style>
</head>
<body>

<div class="login-card">
    
    <!-- LEFT: FORM SECTION -->
    <div class="form-section">
        
        <div class="brand-mobile">
            <i class="fas fa-store"></i> Warung Seblang
        </div>

        <h2>Masuk</h2>
        <div class="welcome-tag">
            <i class="fas fa-chart-pie"></i> 
            <span>POS & Accounting System</span>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <i class="fas fa-info-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-extra">
                <label class="remember">
                    <input type="checkbox" name="remember"> 
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="forgot-link">
                    <i class="fas fa-key"></i> Lupa password?
                </a>
            </div>

            <button type="submit" class="btn-login">
                <span>LOGIN</span>
                <i class="fas fa-arrow-right"></i>
            </button>

            <div class="warung-tagline">
                <i class="fas fa-cash-register"></i> Warung Seblang · Kasir Akurat · Laporan Cepat <i class="fas fa-file-invoice"></i>
            </div>
        </form>
    </div>

    <!-- RIGHT: BRANDING SECTION - BLUE THEME -->
    <div class="hero-section">
        <div class="brand-icon">
            <i class="fas fa-store-alt"></i>
        </div>
        <h1>Warung <span>Seblang</span></h1>
        
        <div class="hero-desc">
            <i class="fas fa-chart-line"></i> Point of Sale & Accounting
        </div>

        <div class="feature-badge">
            <div class="badge"><i class="fas fa-shopping-cart"></i> Kasir</div>
            <div class="badge"><i class="fas fa-file-invoice-dollar"></i> Laporan</div>
            <div class="badge"><i class="fas fa-box"></i> Stok</div>
            <div class="badge"><i class="fas fa-chart-bar"></i> Akuntansi</div>
        </div>
        
        <div style="margin-top: 35px; font-size: 14px; color: rgba(255,255,255,0.8); border-top: 1px solid rgba(255,255,255,0.2); padding-top: 25px; width: 80%; text-align: center;">
            <i class="fas fa-shield-alt"></i> Sistem aman & terpercaya
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Also support Enter key on toggle button for accessibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.toggle-password');
        if (toggleBtn) {
            toggleBtn.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    togglePasswordVisibility();
                }
            });
        }
    });
</script>

</body>
</html>