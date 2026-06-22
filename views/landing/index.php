<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Pelaporan Digital Pemerintah Kabupaten Mandailing Natal.">
    <title>Sistem Layanan Pelaporan — Pemerintah Kabupaten Mandailing Natal</title>
    <link rel="shortcut icon" href="<?= asset('assets/images/favicon.png') ?>" />

    <!-- Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..500;1,6..72,300..400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/design-tokens.css') ?>" />

    <style>
        :root {
            --canvas:       #FAF9F6; /* Warm cotton off-white */
            --surface:      #FFFFFF;
            --prestige-navy: #0A1D37; /* Institutional dark blue */
            --prestige-gold: #B8976C; /* Elegant bronze/gold */
            --prestige-gold-light: rgba(184, 151, 108, 0.15);
            --border-alpha:  rgba(10, 29, 55, 0.06);
            --border-double: rgba(184, 151, 108, 0.2);
            --text-primary:  #1C2630;
            --text-muted:    #656E77;
            
            /* Custom spring-like motion curve */
            --ease-spring:  cubic-bezier(0.32, 0.72, 0, 1);
            --transition-premium: all 0.7s var(--ease-spring);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--canvas);
            color: var(--text-primary);
            min-height: 100dvh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient grain overlay */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            opacity: 0.02;
            pointer-events: none;
            z-index: 999;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* Ambient radial glow */
        .ambient-glow {
            position: absolute;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 80vw;
            height: 600px;
            background: radial-gradient(circle, rgba(184, 151, 108, 0.05) 0%, rgba(250, 249, 246, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── FLOATING GLASS NAVIGATION ── */
        .nav-wrapper {
            position: relative;
            z-index: 10;
            padding: 24px 24px 0;
            max-width: 1100px;
            margin: 0 auto;
        }

        .floating-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 10px 30px rgba(10, 29, 55, 0.03), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border-radius: 9999px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Double-bezel on small mark */
        .brand-emblem-shell {
            background: rgba(10, 29, 55, 0.03);
            padding: 3px;
            border-radius: 10px;
            border: 1px solid rgba(10, 29, 55, 0.05);
        }

        .brand-emblem-core {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: var(--prestige-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .brand-emblem-core svg {
            width: 18px;
            height: 18px;
            stroke: var(--prestige-gold);
            stroke-width: 1.25;
            fill: none;
        }

        .brand-details {
            line-height: 1.2;
        }

        .brand-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--prestige-navy);
            letter-spacing: -0.01em;
        }

        .brand-subtitle {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .nav-divider {
            width: 1px;
            height: 24px;
            background: rgba(10, 29, 55, 0.08);
        }

        .sys-badge {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--prestige-gold);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .nav-right {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.05em;
            background: rgba(10, 29, 55, 0.04);
            padding: 6px 14px;
            border-radius: 9999px;
            font-variant-numeric: tabular-nums;
        }

        /* ── HERO SECTION ── */
        .hero-section {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 80px 24px 60px;
            position: relative;
            z-index: 2;
        }

        .eyebrow-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(184, 151, 108, 0.1);
            border: 1px solid rgba(184, 151, 108, 0.15);
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #8E6E45;
            margin-bottom: 24px;
        }

        .eyebrow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--prestige-gold);
            box-shadow: 0 0 8px var(--prestige-gold);
        }

        .hero-title {
            font-family: 'Newsreader', Georgia, serif;
            font-size: clamp(34px, 5.5vw, 56px);
            font-weight: 400;
            color: var(--prestige-navy);
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero-title em {
            font-style: italic;
            font-weight: 300;
            color: var(--prestige-gold);
            background: linear-gradient(135deg, #B8976C 30%, #E2C9A6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 15px;
            color: var(--text-muted);
            max-width: 540px;
            margin: 0 auto;
            line-height: 1.7;
            font-weight: 400;
        }

        /* ── BENTO GRID ── */
        .grid-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 24px 120px;
            position: relative;
            z-index: 2;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        /* ── DOUBLE-BEZEL CARD PATTERN ── */
        .double-bezel-wrapper {
            background: rgba(10, 29, 55, 0.02);
            border: 1px solid rgba(10, 29, 55, 0.04);
            padding: 8px;
            border-radius: 32px;
            transition: var(--transition-premium);
        }

        .double-bezel-wrapper:hover {
            transform: translateY(-4px);
            background: rgba(184, 151, 108, 0.03);
            border-color: rgba(184, 151, 108, 0.15);
            box-shadow: 0 20px 40px rgba(10, 29, 55, 0.04);
        }

        .card-core {
            border-radius: calc(32px - 8px);
            padding: 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: var(--transition-premium);
            text-decoration: none;
            color: inherit;
        }

        /* 1. ADMIN CARD (Massive High-Contrast, Full Width) */
        .wrapper-admin {
            grid-column: span 2;
        }

        .wrapper-admin .card-core {
            background: var(--prestige-navy);
            color: #FFFFFF;
            flex-direction: row;
            align-items: center;
            gap: 40px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }

        /* Admin card glowing mesh */
        .wrapper-admin .card-core::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(184, 151, 108, 0.12) 0%, transparent 60%);
            pointer-events: none;
        }

        .admin-content-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .card-badge {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .badge-admin {
            background: rgba(184, 151, 108, 0.15);
            color: var(--prestige-gold);
            border: 1px solid rgba(184, 151, 108, 0.25);
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .wrapper-admin .card-title {
            font-family: 'Newsreader', Georgia, serif;
            font-size: 28px;
            font-weight: 400;
        }

        .card-desc {
            font-size: 13.5px;
            line-height: 1.6;
        }

        .wrapper-admin .card-desc {
            color: rgba(255, 255, 255, 0.6);
            max-width: 460px;
        }

        /* 2. SUB CARDS (Camat & OPD) - Spans 1 */
        .wrapper-sub .card-core {
            background: var(--surface);
            border: 1px solid rgba(10, 29, 55, 0.05);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
            min-height: 320px;
        }

        .badge-camat {
            background: rgba(30, 63, 32, 0.06);
            color: #1E3F20;
            border: 1px solid rgba(30, 63, 32, 0.12);
        }

        .badge-opd {
            background: rgba(142, 110, 39, 0.06);
            color: #8E6E27;
            border: 1px solid rgba(142, 110, 39, 0.12);
        }

        .wrapper-sub .card-title {
            color: var(--prestige-navy);
            margin-top: 12px;
        }

        .wrapper-sub .card-desc {
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        /* Iconic Symbol Containers */
        .symbol-shell {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(10, 29, 55, 0.02);
            border: 1px solid rgba(10, 29, 55, 0.04);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper-admin .symbol-shell {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .symbol-core {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-premium);
        }

        .wrapper-admin .symbol-core {
            background: rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }

        .wrapper-sub .symbol-core {
            background: #FFFFFF;
            border: 1px solid rgba(10, 29, 55, 0.04);
            box-shadow: 0 4px 10px rgba(10, 29, 55, 0.02);
        }

        .symbol-core svg {
            width: 20px;
            height: 20px;
            stroke-width: 1.25;
            fill: none;
            transition: var(--transition-premium);
        }

        .wrapper-admin .symbol-core svg {
            stroke: var(--prestige-gold);
        }

        .wrapper-sub .symbol-core svg {
            stroke: var(--prestige-navy);
        }

        /* ── PREMIUM BUTTON-IN-BUTTON CTA PATTERN ── */
        .premium-cta {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            padding: 6px 6px 6px 24px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: -0.01em;
            transition: var(--transition-premium);
            position: relative;
            z-index: 2;
            align-self: flex-start;
        }

        .cta-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-premium);
        }

        .cta-icon-box svg {
            width: 14px;
            height: 14px;
            stroke-width: 2;
            fill: none;
            transition: var(--transition-premium);
        }

        /* Admin CTA (Light/Gold Style) */
        .cta-admin {
            background: var(--prestige-gold);
            color: var(--prestige-navy);
        }

        .cta-admin .cta-icon-box {
            background: rgba(10, 29, 55, 0.15);
        }

        .cta-admin .cta-icon-box svg {
            stroke: var(--prestige-navy);
        }

        .double-bezel-wrapper:hover .cta-admin {
            background: #FFFFFF;
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.1);
        }

        /* Sub Cards CTA (Navy/Gold Style) */
        .cta-sub {
            background: var(--prestige-navy);
            color: #FFFFFF;
        }

        .cta-sub .cta-icon-box {
            background: rgba(255, 255, 255, 0.1);
        }

        .cta-sub .cta-icon-box svg {
            stroke: #FFFFFF;
        }

        .double-bezel-wrapper:hover .cta-sub {
            background: var(--prestige-gold);
            color: var(--prestige-navy);
        }

        .double-bezel-wrapper:hover .cta-sub .cta-icon-box {
            background: rgba(10, 29, 55, 0.1);
        }

        .double-bezel-wrapper:hover .cta-sub .cta-icon-box svg {
            stroke: var(--prestige-navy);
        }

        /* Magnetic Hover Effects on Icons */
        .double-bezel-wrapper:hover .cta-icon-box {
            transform: scale(1.05);
        }

        .double-bezel-wrapper:hover .cta-icon-box svg {
            transform: translate(2px, -2px);
        }

        .double-bezel-wrapper:hover .symbol-core {
            background: var(--prestige-navy);
        }

        .double-bezel-wrapper:hover .symbol-core svg {
            stroke: var(--prestige-gold);
            transform: scale(1.08);
        }

        .double-bezel-wrapper.wrapper-admin:hover .symbol-core {
            background: var(--prestige-gold);
        }

        .double-bezel-wrapper.wrapper-admin:hover .symbol-core svg {
            stroke: var(--prestige-navy);
        }

        /* ── FOOTER ── */
        .footer-section {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(10, 29, 55, 0.06);
            position: relative;
            z-index: 2;
        }

        .footer-text {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .footer-version {
            font-size: 11px;
            font-weight: 600;
            color: var(--prestige-gold);
            letter-spacing: 0.05em;
            background: rgba(184, 151, 108, 0.1);
            padding: 4px 12px;
            border-radius: 9999px;
        }

        /* ── MOTION DECORATIONS / ENTRANCE ── */
        @media (prefers-reduced-motion: no-preference) {
            .hero-section,
            .bento-grid {
                opacity: 0;
                transform: translateY(20px);
                animation: premiumReveal 0.8s var(--ease-spring) forwards;
            }

            .bento-grid {
                animation-delay: 0.15s;
            }

            @keyframes premiumReveal {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* ── RESPONSIVE ADAPTATIONS (MOBILE COLLAPSE) ── */
        @media (max-width: 768px) {
            .nav-wrapper {
                padding: 16px 16px 0;
            }

            .floating-nav {
                padding: 8px 16px;
            }

            .nav-divider, .sys-badge {
                display: none;
            }

            .hero-section {
                padding: 60px 20px 40px;
            }

            .hero-title {
                font-size: 32px;
            }

            .grid-container {
                padding: 0 16px 80px;
            }

            .bento-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .double-bezel-wrapper {
                border-radius: 24px;
                padding: 6px;
            }

            .card-core {
                border-radius: calc(24px - 6px);
                padding: 30px 24px;
            }

            .wrapper-admin {
                grid-column: span 1;
            }

            .wrapper-admin .card-core {
                flex-direction: column;
                align-items: flex-start;
                gap: 24px;
            }

            .wrapper-sub .card-core {
                min-height: auto;
            }

            .footer-section {
                flex-direction: column;
                gap: 12px;
                text-align: center;
                padding: 32px 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient blur glows -->
    <div class="ambient-glow"></div>

    <!-- FLOATING TOP BAR -->
    <div class="nav-wrapper">
        <header class="floating-nav" role="banner">
            <div class="nav-left">
                <div class="brand-logo-container">
                    <div class="brand-emblem-shell">
                        <div class="brand-emblem-core">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M16 10v11M12 10v11"/>
                            </svg>
                        </div>
                    </div>
                    <div class="brand-details">
                        <div class="brand-title">Silap Gawat</div>
                        <div class="brand-subtitle">Kab. Mandailing Natal</div>
                    </div>
                </div>
                <div class="nav-divider"></div>
                <span class="sys-badge">Sistem Pelaporan Digital</span>
            </div>
            <div class="nav-right"><?= date('Y') ?></div>
        </header>
    </div>

    <!-- HERO HEADER -->
    <main role="main">
        <section class="hero-section">
            <div class="eyebrow-pill">
                <span class="eyebrow-dot"></span>
                Portal Layanan Resmi
            </div>
            <h1 class="hero-title">Silakan pilih gerbang masuk<br><em>sesuai kewenangan Anda.</em></h1>
            <p class="hero-description">Sistem pelaporan eksekutif terpadu Pemerintah Kabupaten Mandailing Natal untuk monitoring program kerja pembangunan daerah.</p>
        </section>

        <!-- ASYMMETRICAL BENTO GRID -->
        <section class="grid-container">
            <div class="bento-grid" role="list">

                <!-- 1. ADMIN (Massive Full-Width Card) -->
                <div class="double-bezel-wrapper wrapper-admin" role="listitem">
                    <a class="card-core" href="<?= route('auth', 'admin') ?>" aria-label="Masuk sebagai Administrator">
                        <div class="admin-content-left">
                            <span class="card-badge badge-admin">Eksekutif</span>
                            <h2 class="card-title">Administrator Utama</h2>
                            <p class="card-desc">Kontrol penuh operasional sistem pelaporan bupati. Kelola seluruh integrasi data OPD, kecamatan, laporan kerja, serta penugasan wewenang dinas secara terpusat.</p>
                        </div>
                        <span class="premium-cta cta-admin">
                            Akses Dashboard
                            <span class="cta-icon-box">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                </div>

                <!-- 2. CAMAT (Left Column Card) -->
                <div class="double-bezel-wrapper wrapper-sub" role="listitem">
                    <a class="card-core" href="<?= route('auth', 'camat') ?>" aria-label="Masuk sebagai Camat">
                        <div>
                            <div class="symbol-shell">
                                <div class="symbol-core">
                                    <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V9M19 21V9M9 21v-6h6v6M2 10l10-7 10 7"/></svg>
                                </div>
                            </div>
                            <h2 class="card-title">Wilayah Kecamatan</h2>
                            <p class="card-desc">Akses monitoring khusus camat untuk memantau kemajuan pembangunan, mengawal program kerja wilayah, serta mengoordinasikan laporan dari desa.</p>
                        </div>
                        <span class="premium-cta cta-sub">
                            Masuk Portal
                            <span class="cta-icon-box">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                </div>

                <!-- 3. OPD (Right Column Card) -->
                <div class="double-bezel-wrapper wrapper-sub" role="listitem">
                    <a class="card-core" href="<?= route('auth', 'opd') ?>" aria-label="Masuk sebagai Perangkat Daerah">
                        <div>
                            <div class="symbol-shell">
                                <div class="symbol-core">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="2" y="7" width="20" height="14" rx="1"/>
                                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="card-title">Dinas Perangkat Daerah</h2>
                            <p class="card-desc">Portal resmi Organisasi Perangkat Daerah (OPD) untuk sinkronisasi target indikator, memasukkan laporan bulanan, dan melacak tindak lanjut.</p>
                        </div>
                        <span class="premium-cta cta-sub">
                            Masuk Portal
                            <span class="cta-icon-box">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                </div>

            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer-section" role="contentinfo">
        <span class="footer-text">&copy; <?= date('Y') ?> Pemerintah Kabupaten Mandailing Natal. Hak Cipta Dilindungi.</span>
    </footer>

</body>
</html>