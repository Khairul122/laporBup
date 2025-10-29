<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Sistem Layanan Pelaporan</title>

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
            --primary-black: #000000;
            --primary-blue: #2F58CD;
            --secondary-blue: #1E3A8A;
            --primary-orange: #F59E0B;
            --light-gray: #F5F6FA;
            --dark-gray: #333333;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-black), #1a1a1a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            animation: fadeInUp 1s ease;
        }

        .welcome-header {
            text-align: center;
            margin-bottom: 60px;
            color: white;
        }

        .welcome-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--primary-blue), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeIn 1s ease;
        }

        .welcome-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.4rem);
            font-weight: 400;
            opacity: 0.9;
            animation: fadeIn 1s ease 0.2s both;
        }

        .login-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .login-card {
            background: white;
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: fadeInUp 1s ease;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-orange));
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
        }

        .login-card:hover::before {
            transform: scaleX(1);
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 36px;
            transition: var(--transition);
            box-shadow: 0 8px 25px rgba(47, 88, 205, 0.3);
        }

        .login-card:hover .login-icon {
            transform: scale(1.1);
            box-shadow: 0 12px 30px rgba(47, 88, 205, 0.4);
        }

        .login-card.admin .login-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        }

        .login-card.camat .login-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .login-card.opd .login-icon {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 10px;
        }

        .login-subtitle {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .login-description {
            font-size: 0.9rem;
            color: #777;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .login-btn {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(47, 88, 205, 0.3);
            color: white;
        }

        .login-card.camat .login-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .login-card.camat .login-btn:hover {
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .login-card.opd .login-btn {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }

        .login-card.opd .login-btn:hover {
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .footer-info {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            color: white;
            opacity: 0.8;
            font-size: 0.9rem;
            background: transparent;
            padding: 10px 0;
            animation: fadeIn 1s ease 0.6s both;
            z-index: 1000;
        }


        .app-name {
            color: var(--primary-orange);
            font-weight: 600;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        /* Responsive */
        @media (max-width: 768px) {
            .login-cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .login-card {
                padding: 30px 25px;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .welcome-subtitle {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }

            .login-icon {
                width: 70px;
                height: 70px;
                font-size: 30px;
            }

            .welcome-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Header -->
        <div class="welcome-header">
            <h1 class="welcome-title">Sistem Layanan Pelaporan</h1>
            <p class="welcome-subtitle">Pemerintah Kabupaten Mandailing Natal</p>
        </div>

        <!-- Login Cards -->
        <div class="login-cards">
            <!-- Admin Login -->
            <div class="login-card admin" onclick="location.href='index.php?controller=auth&action=admin'">
                <div class="login-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="login-title">Administrator</h2>
                <p class="login-subtitle">LaporBup</p>
                <button class="login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login Admin
                </button>
            </div>

            <!-- Camat Login -->
            <div class="login-card camat" onclick="location.href='index.php?controller=auth&action=camat'">
                <div class="login-icon">
                    <i class="fas fa-landmark"></i>
                </div>
                <h2 class="login-title">Camat</h2>
                <p class="login-subtitle">Silap Gawat</p>
                <button class="login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login Camat
                </button>
            </div>

            <!-- OPD Login -->
            <div class="login-card opd" onclick="location.href='index.php?controller=auth&action=opd'">
                <div class="login-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h2 class="login-title">OPD</h2>
                <p class="login-subtitle">Madina Maju Madani</p>
                <button class="login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login OPD
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p>&copy; <?php echo date('Y'); ?> Pemerintah Kabupaten Mandailing Natal</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>