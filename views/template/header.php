<?php
// views/template/header.php
// Ambil role dari session untuk dinamisasi
$user_role = $_SESSION['role'] ?? 'user';
$title = isset($title) ? $title : 'Dashboard - LaporBup';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts - Poppins & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-black: #000000;
            --primary-blue: #2F58CD;
            --secondary-blue: #1E3A8A;
            --primary-orange: #F59E0B;
            --light-gray: #F5F6FA;
            --dark-gray: #333333;
            --card-shadow: 0 6px 16px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
            --header-height: 70px;
            --footer-height: 40px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .header {
            background: linear-gradient(135deg, var(--primary-black), #1a1a1a);
            color: white;
            padding: clamp(12px, 2.5vw, 15px) clamp(15px, 3vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: var(--header-height);
            transition: var(--transition);
            min-height: 50px;
        }

        .logo {
            font-weight: 700;
            font-size: clamp(1rem, 3vw, 1.5rem);
            display: flex;
            align-items: center;
            gap: clamp(6px, 2vw, 12px);
            animation: fadeIn 0.8s ease;
            flex-shrink: 0;
        }

        .logo i {
            color: var(--primary-orange);
            font-size: clamp(1rem, 3vw, 1.5rem);
        }

        .nav-menu {
            display: flex;
            gap: clamp(10px, 3vw, 30px);
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            flex-shrink: 1;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            padding: clamp(3px, 1vw, 5px) 0;
            font-size: clamp(12px, 2.5vw, 16px);
            white-space: nowrap;
        }

        .nav-menu a:hover {
            color: var(--primary-orange);
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-orange);
            transition: var(--transition);
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .user-info {
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: clamp(5px, 1.5vw, 8px);
            font-size: clamp(14px, 2vw, 16px);
        }

        .main-content {
            padding: 0;
            margin: 0;
            animation: fadeInUp 0.8s ease;
            flex: 1;
            flex-shrink: 0;
            width: 100%;
            height: 100%;
            min-height: calc(100vh - var(--header-height) - var(--footer-height));
        }

        .content-row {
            display: flex;
            gap: clamp(20px, 4vw, 30px);
            margin-bottom: clamp(20px, 4vw, 30px);
        }

        .info-card, .menu-card {
            background: white;
            border-radius: clamp(15px, 3vw, 20px);
            padding: clamp(20px, 4vw, 30px);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .info-card {
            flex: 1;
            min-width: 280px;
        }

        .menu-card {
            flex: 2;
            min-width: 320px;
        }

        .info-card:hover, .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }

        /* Full Screen Container Styles */
        .fullscreen-container {
            min-height: calc(80vh - var(--header-height) - var(--footer-height));
            padding: clamp(20px, 3vw, 30px);
            padding-top: calc(var(--header-height) + clamp(20px, 3vw, 30px));
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            max-width: 1400px;
            width: 100%;
        }

        .fullscreen-content {
            flex: 1;
            overflow-y: auto;
            background: var(--light-gray);
        }

        .card-title {
            font-size: clamp(18px, 4vw, 20px);
            font-weight: 700;
            margin-bottom: clamp(15px, 3vw, 25px);
            color: var(--dark-gray);
            position: relative;
            padding-bottom: clamp(5px, 1.5vw, 10px);
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: clamp(30px, 8vw, 50px);
            height: clamp(2px, 0.5vw, 3px);
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-orange));
            border-radius: 3px;
        }

        .user-name {
            font-size: clamp(16px, 3vw, 18px);
            font-weight: 600;
            margin-bottom: clamp(8px, 2vw, 12px);
            color: var(--primary-black);
        }

        .user-position {
            font-size: clamp(14px, 2.5vw, 16px);
            color: #555;
            margin-bottom: clamp(5px, 1.5vw, 8px);
            display: flex;
            align-items: center;
            gap: clamp(5px, 1.5vw, 8px);
        }

        .last-login {
            font-size: clamp(11px, 2vw, 13px);
            color: #777;
            padding: clamp(5px, 2vw, 8px) 0;
            border-top: 1px solid #eee;
            margin-top: clamp(10px, 3vw, 15px);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: clamp(15px, 3vw, 20px);
        }

        .menu-item {
            background: white;
            border-radius: clamp(15px, 4vw, 20px);
            padding: clamp(20px, 5vw, 25px) clamp(10px, 2vw, 15px);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid #eee;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange));
            opacity: 0.05;
            transition: var(--transition);
        }

        .menu-item:hover::before {
            height: 100%;
        }

        .menu-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .menu-icon {
            width: clamp(50px, 12vw, 70px);
            height: clamp(50px, 12vw, 70px);
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border-radius: clamp(12px, 3vw, 18px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto clamp(12px, 3vw, 18px);
            color: white;
            font-size: clamp(20px, 5vw, 26px);
            transition: var(--transition);
            box-shadow: 0 6px 16px rgba(47, 88, 205, 0.3);
        }

        .menu-item:hover .menu-icon {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(47, 88, 205, 0.4);
        }

        .menu-text {
            font-size: clamp(14px, 2.5vw, 16px);
            font-weight: 600;
            color: var(--dark-gray);
        }

        .footer {
            background: linear-gradient(135deg, var(--dark-gray), #1a1a1a);
            color: white;
            text-align: center;
            padding: clamp(8px, 2vw, 12px) 0;
            font-size: clamp(10px, 1.5vw, 12px);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: var(--footer-height);
            flex-shrink: 0;
            transition: var(--transition);
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(15px, 3vw, 30px);
        }

        .footer-content {
            margin: 0;
            line-height: 1.2;
        }

        .footer-info {
            opacity: 0.8;
            font-size: clamp(9px, 1.2vw, 11px);
            margin-top: 2px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive design using container queries and viewport units */
        @media (max-width: 992px) {
            .content-row {
                flex-direction: column;
            }

            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .fullscreen-container {
                min-height: calc(80vh - var(--header-height) - var(--footer-height));
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: row;
                gap: clamp(8px, 2vw, 15px);
                padding: clamp(8px, 2vw, 12px) clamp(15px, 3vw, 20px);
                height: auto;
                min-height: 50px;
                flex-wrap: nowrap;
            }

            .logo {
                font-size: clamp(0.9rem, 3.5vw, 1.2rem);
                gap: clamp(5px, 2vw, 8px);
            }

            .logo i {
                font-size: clamp(0.9rem, 3.5vw, 1.2rem);
            }

            .nav-menu {
                gap: clamp(8px, 2.5vw, 15px);
                width: auto;
                justify-content: flex-end;
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .nav-menu a {
                font-size: clamp(11px, 3vw, 14px);
                padding: 2px 0;
            }

            .info-card, .menu-card {
                width: 100%;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .footer {
                padding: clamp(6px, 1.5vw, 10px) 0;
            }

            .footer .container {
                padding: 0 clamp(10px, 2.5vw, 20px);
            }

            .fullscreen-container {
                min-height: calc(80vh - var(--header-height) - var(--footer-height));
            }
        }

        @media (max-width: 480px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: row;
                gap: clamp(5px, 1.5vw, 10px);
                padding: clamp(6px, 1.5vw, 10px) clamp(10px, 2.5vw, 15px);
                min-height: 45px;
            }

            .logo {
                font-size: clamp(0.8rem, 4vw, 1rem);
                gap: clamp(4px, 1.5vw, 6px);
            }

            .logo i {
                font-size: clamp(0.8rem, 4vw, 1rem);
            }

            .nav-menu {
                gap: clamp(5px, 2vw, 10px);
                justify-content: flex-end;
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .nav-menu a {
                font-size: clamp(10px, 3.5vw, 12px);
                padding: 1px 0;
            }

            .footer {
                padding: clamp(5px, 1.2vw, 8px) 0;
                font-size: clamp(8px, 1.2vw, 10px);
            }

            .footer-info {
                font-size: clamp(7px, 1vw, 9px);
            }
        }

        /* User Section Styles */
        .user-section {
            display: flex;
            align-items: center;
            gap: clamp(12px, 2vw, 16px);
        }

        .user-info {
            color: white;
            display: flex;
            align-items: center;
            gap: clamp(6px, 1.2vw, 8px);
            font-size: clamp(14px, 2vw, 16px);
            font-weight: 500;
            padding: clamp(6px, 1vw, 8px) clamp(10px, 1.5vw, 12px);
            background: rgba(255,255,255,0.05);
            border-radius: clamp(6px, 1vw, 8px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .user-info i {
            font-size: clamp(16px, 2.5vw, 18px);
            color: var(--primary-orange);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: clamp(6px, 1vw, 8px);
            color: white;
            text-decoration: none;
            font-size: clamp(13px, 1.8vw, 14px);
            font-weight: 500;
            padding: clamp(6px, 1vw, 8px) clamp(12px, 1.8vw, 16px);
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: clamp(6px, 1vw, 8px);
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.3);
            border-color: rgba(220, 53, 69, 0.5);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }

        .logout-btn i {
            font-size: clamp(14px, 2vw, 16px);
        }

        /* Responsive User Section */
        @media (max-width: 768px) {
            .user-section {
                flex-direction: column;
                gap: clamp(8px, 1.5vw, 12px);
                align-items: stretch;
            }

            .user-info {
                justify-content: center;
            }

            .logout-btn {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .user-section {
                gap: clamp(6px, 1.2vw, 10px);
            }

            .user-info {
                font-size: clamp(12px, 2vw, 14px);
                padding: clamp(4px, 0.8vw, 6px) clamp(8px, 1.2vw, 10px);
            }

            .logout-btn {
                font-size: clamp(12px, 1.6vw, 13px);
                padding: clamp(4px, 0.8vw, 6px) clamp(10px, 1.5vw, 14px);
            }
        }
    </style>
</head>
<body class="<?php echo 'role-' . htmlspecialchars($user_role); ?>">