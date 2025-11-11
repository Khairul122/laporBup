<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1e3a5f;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
            animation: wave 15s linear infinite;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 90%;
            min-height: 600px;
            display: flex;
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel */
        .left-panel {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -75px;
            left: -75px;
        }

        .left-panel h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .left-panel p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .feature-list {
            margin-top: 2rem;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: var(--transition);
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .feature-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #fff;
        }

        /* Right Panel */
        .right-panel {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6c757d;
            font-size: 1rem;
        }

        /* Form Styling */
        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            height: auto;
        }

        .form-control.with-toggle {
            padding-right: 3rem;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            transform: translateY(-1px);
        }

        .form-control:hover:not(:focus) {
            border-color: #dee2e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-control.is-valid {
            border-color: var(--success-color);
            background-image: none;
        }

        .form-control.is-invalid {
            border-color: var(--accent-color);
            background-image: none;
        }

        .form-floating label {
            color: #6c757d;
            padding: 0.75rem 1rem;
            transition: var(--transition);
            pointer-events: none;
        }

        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(-0.15rem);
        }

        .form-floating .form-control:focus ~ label {
            color: var(--secondary-color);
        }

        .form-floating .form-control.is-valid ~ label {
            color: var(--success-color);
        }

        .form-floating .form-control.is-invalid ~ label {
            color: var(--accent-color);
        }

        /* Button Styling */
        .btn-login {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Alert Styling */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: none;
            animation: slideInDown 0.3s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
            border-left: 4px solid var(--accent-color);
        }

        /* Loading Spinner */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 95%;
                min-height: auto;
            }

            .left-panel {
                padding: 2rem;
                text-align: center;
            }

            .left-panel h1 {
                font-size: 2rem;
            }

            .right-panel {
                padding: 2rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 1.1rem;
            border-radius: 4px;
        }

        .password-toggle:hover {
            background: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        .password-toggle.active {
            color: var(--success-color);
        }

        .password-toggle.active:hover {
            background: rgba(39, 174, 96, 0.1);
        }

        .input-group {
            position: relative;
        }

        .form-floating .input-group {
            margin-bottom: 1.5rem;
        }

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        /* Login Info Card */
        .login-info {
            background: rgba(52, 152, 219, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--secondary-color);
        }

        .login-info h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-info p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>

    <!-- Main Login Container -->
    <div class="login-container" data-aos="fade-up" data-aos-duration="800">
        <!-- Left Panel - Info Section -->
        <div class="left-panel">
            <h1>Selamat Datang</h1>
            <p>Sistem Informasi  - Platform pelaporan digital untuk pemerintahan daerah</p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <strong>Aman & Terpercaya</strong>
                        <div class="small">Sistem keamanan terenkripsi</div>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-speedometer2"></i>
                    <div>
                        <strong>Cepat & Efisien</strong>
                        <div class="small">Proses pelaporan real-time</div>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-graph-up"></i>
                    <div>
                        <strong>Transparan</strong>
                        <div class="small">Monitoring laporan terintegrasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="right-panel">
            <div class="form-header">
                <h2>Masuk ke Akun Anda</h2>
                <p>Silakan login untuk mengakses sistem</p>
            </div>

            <!-- Login Info Card -->
            <div class="login-info">
                <h6><i class="bi bi-info-circle"></i> Informasi Login</h6>
                <p>Gunakan username dan password yang telah diberikan oleh admin untuk mengakses sistem.</p>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer" class="alert"></div>

            <!-- Login Form -->
            <form id="loginFormElement">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">
                        <i class="bi bi-person"></i> Username
                    </label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control with-toggle" id="password" name="password" placeholder="Password" required>
                    <label for="password">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')" title="Tampilkan/sembunyikan password">
                        <i class="bi bi-eye" id="passwordToggle"></i>
                    </button>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p>&copy; 2024 Sistem . Dikelola oleh <strong>Dinas Kominfo</strong></p>
                <p><a href="#">Kebijakan Privasi</a> | <a href="#">Bantuan</a></p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="spinner-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init();

        // Toggle Password Visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Toggle');
            const toggleBtn = icon.closest('.password-toggle');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                toggleBtn.classList.add('active');
                toggleBtn.setAttribute('title', 'Sembunyikan password');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                toggleBtn.classList.remove('active');
                toggleBtn.setAttribute('title', 'Tampilkan password');
            }

            // Add subtle animation
            toggleBtn.style.transform = 'translateY(-50%) scale(1.1)';
            setTimeout(() => {
                toggleBtn.style.transform = 'translateY(-50%) scale(1)';
            }, 200);
        }

        // Show Alert
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.className = `alert alert-${type}`;
            alertContainer.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                ${message}
            `;
            alertContainer.style.display = 'block';

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertContainer.style.display = 'none';
            }, 5000);
        }

        // Show Loading
        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = show ? 'flex' : 'none';
        }

        // Set Button Loading
        function setButtonLoading(buttonId, loading) {
            const button = document.getElementById(buttonId);
            if (loading) {
                button.classList.add('loading');
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
            } else {
                button.classList.remove('loading');
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk';
            }
        }

        // Handle Login Form
        document.getElementById('loginFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            setButtonLoading('loginBtn', true);

            try {
                const response = await fetch('?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server mengembalikan response yang tidak valid');
                }

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    showAlert(result.message, 'danger');
                    // Shake animation for invalid login
                    const passwordField = document.getElementById('password');
                    passwordField.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        passwordField.style.animation = '';
                    }, 500);
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('Terjadi kesalahan koneksi. Silakan coba lagi.', 'danger');
            } finally {
                setButtonLoading('loginBtn', false);
            }
        });

        // Add shake animation for errors
        const shakeStyle = document.createElement('style');
        shakeStyle.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(shakeStyle);

        // Remember Me Functionality
        const rememberMeCheckbox = document.getElementById('rememberMe');
        const usernameInput = document.getElementById('username');

        // Load saved username if remember me was checked
        window.addEventListener('load', function() {
            const savedUsername = localStorage.getItem('rememberedUsername');
            const rememberMe = localStorage.getItem('rememberMe') === 'true';

            if (savedUsername && rememberMe) {
                usernameInput.value = savedUsername;
                rememberMeCheckbox.checked = true;
            }
        });

        // Save username when remember me is checked
        rememberMeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('rememberedUsername', usernameInput.value);
                localStorage.setItem('rememberMe', 'true');
            } else {
                localStorage.removeItem('rememberedUsername');
                localStorage.setItem('rememberMe', 'false');
            }
        });

        // Update saved username when typing
        usernameInput.addEventListener('input', function() {
            if (rememberMeCheckbox.checked) {
                localStorage.setItem('rememberedUsername', this.value);
            }
        });

        // Enter key handling
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const activeElement = document.activeElement;
                const loginForm = document.getElementById('loginFormElement');

                if (loginForm.contains(activeElement)) {
                    loginForm.dispatchEvent(new Event('submit'));
                }
            }
        });

        // Auto-focus on username field when page loads
        window.addEventListener('load', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>