<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Warung Seblang POS</title>

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

        .login-card {
            max-width: 820px;
            width: 100%;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(18, 52, 102, 0.10);
            display: flex;
            overflow: hidden;
        }

        /* LEFT */
        .form-section {
            flex: 1;
            padding: 36px 32px;
        }

        .form-section h2 {
            font-size: 24px;
            font-weight: 700;
            color: #0a2e4a;
            margin-bottom: 6px;
        }

        .welcome-tag {
            font-size: 13px;
            color: #4a657b;
            margin-bottom: 24px;
            border-bottom: 1px solid #e1ecfe;
            padding-bottom: 12px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            font-size: 12px;
            font-weight: 600;
            color: #1e4a6b;
            margin-bottom: 4px;
            display: block;
            text-transform: uppercase;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background: #f5faff;
            border-radius: 12px;
            padding: 4px 14px;
            border: 2px solid transparent;
            transition: 0.2s;
        }

        .input-wrapper:focus-within {
            background: white;
            border-color: #2a7faa;
        }

        .input-wrapper i {
            color: #5e7e9c;
            font-size: 14px;
            margin-right: 8px;
        }

        .input-wrapper input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 10px 0;
            font-size: 14px;
            outline: none;
            color: #0a2e4a;
        }

        .password-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .toggle-password {
            background: none;
            border: none;
            cursor: pointer;
            color: #5e7e9c;
        }

        .btn-login {
            background: linear-gradient(105deg, #1a6392, #0a4b7a);
            border: none;
            border-radius: 30px;
            padding: 12px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-login:hover {
            background: linear-gradient(105deg, #0a4b7a, #083b5e);
        }

        .error-message {
            background: #f0f7ff;
            border-left: 4px solid #2a7faa;
            padding: 10px;
            border-radius: 8px;
            color: #0a4b7a;
            font-size: 13px;
            margin-bottom: 15px;
        }

        /* RIGHT */
        .hero-section {
            flex: 1;
            background: linear-gradient(145deg, #1a6392, #0a3e5e);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            color: white;
        }

        .brand-icon {
            font-size: 55px;
            margin-bottom: 10px;
        }

        .hero-section h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-desc {
            font-size: 13px;
            opacity: 0.9;
            text-align: center;
        }

        .feature-badge {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .badge {
            background: rgba(255,255,255,0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
            }

            .hero-section {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="login-card">

    <div class="form-section">
        <h2>Masuk</h2>
        <div class="welcome-tag">POS & Accounting System</div>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-login">
                LOGIN
            </button>
        </form>
    </div>

    <div class="hero-section">
        <div class="brand-icon">
            <i class="fas fa-store"></i>
        </div>
        <h1>Warung Seblang</h1>
        <div class="hero-desc">
            Kasir • Laporan • Stok • Akuntansi
        </div>
    </div>

</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        pass.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>