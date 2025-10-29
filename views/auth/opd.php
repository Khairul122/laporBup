<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appData['title']); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-red: #dc3545;
            --secondary-orange: #fd7e14;
            --accent-red: #c82333;
            --primary-black: #000000;
            --primary-orange: #F59E0B;
            --light-gray: #F5F6FA;
            --dark-gray: #333333;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        .split-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Left Side - Login Form */
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 40px;
            position: relative;
            overflow-y: auto;
        }

        .login-form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeInLeft 1s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            padding: 40px 30px;
        }

        /* Right Side - Branding */
        .branding-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-orange));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .branding-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .branding-content {
            position: relative;
            z-index: 2;
            text-align: center;
            animation: fadeInRight 1s ease;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.15);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 60px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .app-name {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(45deg, #ffffff, var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-description {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .user-type {
            font-size: 1.2rem;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
            padding: 12px 30px;
            border-radius: 30px;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .feature-list {
            margin-top: 50px;
            text-align: left;
            max-width: 400px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-gray);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: #fafbfc;
        }

        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
            background-color: white;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: var(--dark-gray);
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-orange));
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            margin-top: 15px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 20px;
            background: #f8f9fa;
            font-size: 0.95rem;
        }

        .back-link a:hover {
            background: #e9ecef;
            color: var(--primary-red);
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            animation: shake 0.5s ease;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee, #fdd);
            color: #c53030;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .toast-notification {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideInRight 0.3s ease;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .toast-notification.removing {
            animation: slideOutRight 0.3s ease;
            opacity: 0;
            transform: translateX(100%);
        }

        .toast-notification.success {
            border-left-color: #28a745;
        }

        .toast-notification.error {
            border-left-color: #dc3545;
        }

        .toast-notification.warning {
            border-left-color: #ffc107;
        }

        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
            color: white;
        }

        .toast-notification.success .toast-icon {
            background: #28a745;
        }

        .toast-notification.error .toast-icon {
            background: #dc3545;
        }

        .toast-notification.warning .toast-icon {
            background: #ffc107;
            color: #212529;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            font-size: 0.95rem;
        }

        .toast-message {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .toast-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.1);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #666;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            background: rgba(0,0,0,0.2);
            color: #333;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
            }

            .login-section {
                flex: 1;
                padding: 30px 20px;
            }

            .branding-section {
                flex: 0 0 auto;
                min-height: 40vh;
                padding: 30px 20px;
            }

            .login-header h2 {
                font-size: 1.6rem;
            }

            .app-name {
                font-size: 2rem;
            }

            .logo-icon {
                width: 80px;
                height: 80px;
                font-size: 40px;
            }

            .feature-list {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-section {
                padding: 20px 15px;
            }

            .branding-section {
                padding: 25px 15px;
                min-height: 35vh;
            }

            .login-form-container {
                max-width: 100%;
            }

            .login-card {
                padding: 30px 20px;
            }

            .login-header h2 {
                font-size: 1.4rem;
            }

            .app-name {
                font-size: 1.8rem;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
            }

            .app-description {
                font-size: 1rem;
            }

            .user-type {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }

        /* Password visibility toggle */
        .password-toggle {
            cursor: pointer;
            color: #666;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary-red);
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side - Login Form -->
        <div class="login-section">
            <div class="login-form-container">
                <!-- Login Header -->
                <div class="login-header">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk ke akun OPD Anda</p>
                </div>

                <!-- Login Card -->
                <div class="login-card">
                    <!-- Alert Container -->
                    <div id="alertContainer"></div>

                    <!-- Login Form -->
                    <form id="loginForm">
                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="Masukkan username" required autocomplete="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Masukkan password" required autocomplete="current-password">
                                <span class="input-group-text password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login" id="loginBtn">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            <span id="loginText">Login</span>
                        </button>
                    </form>

                    <!-- Back Link -->
                    <div class="back-link">
                        <a href="index.php">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Pilihan Login
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Branding -->
        <div class="branding-section">
            <div class="branding-content">
                <div class="logo-icon">
                    <i class="<?php echo htmlspecialchars($appData['icon']); ?>"></i>
                </div>
                <h1 class="app-name"><?php echo htmlspecialchars($appData['name']); ?></h1>
                <p class="app-description"><?php echo htmlspecialchars($appData['description']); ?></p>
                <span class="user-type">Portal OPD</span>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Toast notification system
        let toastContainer = null;

        function getToastContainer() {
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container';
                document.body.appendChild(toastContainer);
            }
            return toastContainer;
        }

        function showToast(title, message, type = 'success', duration = 4000) {
            const container = getToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;

            let iconClass = '';
            switch(type) {
                case 'success':
                    iconClass = 'fas fa-check';
                    break;
                case 'error':
                    iconClass = 'fas fa-times';
                    break;
                case 'warning':
                    iconClass = 'fas fa-exclamation';
                    break;
                default:
                    iconClass = 'fas fa-info';
            }

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="removeToast(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            // Auto remove after duration
            setTimeout(() => {
                removeToast(toast.querySelector('.toast-close'));
            }, duration);
        }

        function removeToast(closeButton) {
            const toast = closeButton.closest('.toast-notification');
            if (toast && !toast.classList.contains('removing')) {
                toast.classList.add('removing');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }

        // Legacy showAlert function (untuk compatibility)
        function showAlert(message, type = 'danger') {
            // Convert old alert types to new toast types
            let toastType = 'error';
            let title = 'Error';

            if (type === 'success') {
                toastType = 'success';
                title = 'Berhasil';
            } else if (type === 'warning') {
                toastType = 'error'; // Semua yang bukan success jadi error
                title = 'Gagal';
            }

            // Jika message mengandung HTML, tampilkan versi sederhana
            if (message.includes('<strong>') || message.includes('<a href')) {
                message = message.replace(/<[^>]*>/g, '').trim();
            }

            showToast(title, message, toastType);
        }

        // Set loading state
        function setLoading(loading) {
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            if (loading) {
                loginBtn.disabled = true;
                loginText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                usernameInput.disabled = true;
                passwordInput.disabled = true;
            } else {
                loginBtn.disabled = false;
                loginText.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
                usernameInput.disabled = false;
                passwordInput.disabled = false;
            }
        }

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            // Basic validation
            if (!username) {
                showToast('Validasi Error', 'Username harus diisi', 'error');
                return;
            }

            if (!password) {
                showToast('Validasi Error', 'Password harus diisi', 'error');
                return;
            }

            setLoading(true);

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                formData.append('login_role', 'opd'); // Role dari halaman ini

                const response = await fetch('index.php?controller=auth&action=login', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Login Berhasil', 'Anda berhasil masuk, mengalihkan ke dashboard...', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    if (result.role_mismatch) {
                        // Role mismatch - tampilkan pesan error saja tanpa memberi tahu role sebenarnya
                        showToast('Login Gagal', result.message || 'Username atau password salah', 'error');
                    } else {
                        showToast('Login Gagal', result.message || 'Username atau password salah', 'error');
                    }
                }
            } catch (error) {
                console.error('Login error:', error);
                showToast('Koneksi Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                setLoading(false);
            }
        });

        // Focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>