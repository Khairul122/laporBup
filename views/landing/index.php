<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Pelaporan Digital Pemerintah Kabupaten Mandailing Natal.">
    <title>Sistem Layanan Pelaporan — Pemerintah Kabupaten Mandailing Natal</title>
    <link rel="shortcut icon" href="<?= asset('assets/images/favicon.png') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/design-tokens.css') ?>" />

    <style>
        :root {
            --ink:        #0F172A; /* Primary navy - authority */
            --ink-2:      #334155; /* Secondary text */
            --canvas:     #F8FAFC; /* Bright accessible background */
            --surface:    #FFFFFF;
            --muted:      #E8ECF1;
            --border:     #E2E8F0;
            --accent:     #0369A1; /* Default blue accent */
            --danger:     #DC2626;
            --focus-ring: #0369A1;

            --accent-admin: #0369A1;
            --accent-admin-soft: #E0F0FA;
            --accent-camat: #15803D;
            --accent-camat-soft: #E1F3E8;
            --accent-opd: #B45309;
            --accent-opd-soft: #FCEEDD;

            --font-serif: 'EB Garamond', Georgia, serif;
            --font-sans: 'Lato', Arial, sans-serif;
            --ease: cubic-bezier(0.2, 0.65, 0.3, 1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--canvas);
            color: var(--ink-2);
            min-height: 100dvh;
            line-height: 1.6;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; }

        :focus-visible {
            outline: 3px solid var(--focus-ring);
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ── SKIP LINK ── */
        .skip-link {
            position: absolute;
            left: -9999px;
            top: 0;
            background: var(--ink);
            color: #FFFFFF;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 14px;
            z-index: 1000;
            border-radius: 0 0 8px 0;
        }

        .skip-link:focus {
            left: 0;
        }

        /* ── TOP BAR (Merah Putih civic strip) ── */
        .civic-strip {
            height: 4px;
            background: linear-gradient(90deg, #B91C1C 0%, #B91C1C 50%, #FFFFFF 50%, #FFFFFF 100%);
        }

        /* ── HEADER ── */
        .site-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-mark svg {
            width: 24px;
            height: 24px;
            stroke: #FFFFFF;
            stroke-width: 1.5;
            fill: none;
        }

        .brand-text-title {
            font-family: var(--font-serif);
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.2;
        }

        .brand-text-subtitle {
            font-size: 12.5px;
            color: var(--ink-2);
            font-weight: 400;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink-2);
            background: var(--muted);
            padding: 8px 16px;
            border-radius: 8px;
            font-variant-numeric: tabular-nums;
        }

        /* ── MAIN ── */
        main { display: block; }

        .hero-section {
            max-width: 760px;
            margin: 0 auto;
            text-align: center;
            padding: 64px 24px 40px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-admin-soft);
            color: #044F7A;
            border: 1px solid #BFE0F2;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-title {
            font-family: var(--font-serif);
            font-size: clamp(32px, 5vw, 48px);
            font-weight: 600;
            color: var(--ink);
            line-height: 1.2;
            letter-spacing: -0.01em;
            margin-bottom: 16px;
            text-wrap: balance;
        }

        .hero-description {
            font-size: 17px;
            color: var(--ink-2);
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── ROLE GRID ── */
        .grid-container {
            max-width: 1080px;
            margin: 0 auto;
            padding: 32px 24px 100px;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .role-card {
            display: flex;
            flex-direction: column;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px 28px;
            text-decoration: none;
            transition: transform 200ms var(--ease), box-shadow 200ms var(--ease), border-color 200ms var(--ease);
            min-height: 320px;
        }

        .role-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .role-card:active {
            transform: translateY(-1px) scale(0.99);
        }

        .role-icon-shell {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .role-icon-shell svg {
            width: 26px;
            height: 26px;
            stroke-width: 1.6;
            fill: none;
        }

        .role-card.is-admin .role-icon-shell { background: var(--accent-admin-soft); }
        .role-card.is-admin .role-icon-shell svg { stroke: var(--accent-admin); }
        .role-card.is-camat .role-icon-shell { background: var(--accent-camat-soft); }
        .role-card.is-camat .role-icon-shell svg { stroke: var(--accent-camat); }
        .role-card.is-opd .role-icon-shell { background: var(--accent-opd-soft); }
        .role-card.is-opd .role-icon-shell svg { stroke: var(--accent-opd); }

        .role-badge {
            display: inline-flex;
            align-self: flex-start;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .role-card.is-admin .role-badge { background: var(--accent-admin-soft); color: #044F7A; }
        .role-card.is-camat .role-badge { background: var(--accent-camat-soft); color: #0F5C2E; }
        .role-card.is-opd .role-badge { background: var(--accent-opd-soft); color: #8A4109; }

        .role-title {
            font-family: var(--font-serif);
            font-size: 22px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 10px;
        }

        .role-desc {
            font-size: 14.5px;
            color: var(--ink-2);
            line-height: 1.65;
            flex: 1;
        }

        .role-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-top: 24px;
            padding: 12px 4px;
            min-height: 44px;
        }

        .role-card.is-admin .role-cta { color: var(--accent-admin); }
        .role-card.is-camat .role-cta { color: var(--accent-camat); }
        .role-card.is-opd .role-cta { color: var(--accent-opd); }

        .role-cta svg {
            width: 16px;
            height: 16px;
            stroke-width: 2;
            fill: none;
            transition: transform 200ms var(--ease);
        }

        .role-card:hover .role-cta svg {
            transform: translateX(3px);
        }

        /* ── FOOTER ── */
        .site-footer {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .footer-text {
            font-size: 13px;
            color: var(--ink-2);
        }

        .footer-links {
            display: flex;
            gap: 20px;
            font-size: 13px;
        }

        .footer-links a {
            color: var(--ink-2);
            text-decoration: none;
            font-weight: 600;
            transition: color 150ms var(--ease);
        }

        .footer-links a:hover { color: var(--accent); }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .role-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .header-inner { padding: 14px 16px; }
            .brand-text-subtitle { display: none; }
            .header-meta { padding: 6px 12px; font-size: 12px; }
            .hero-section { padding: 40px 16px 24px; }
            .grid-container { padding: 24px 16px 64px; }
            .site-footer { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
    <div class="civic-strip" role="presentation"></div>

    <header class="site-header" role="banner">
        <div class="header-inner">
            <a class="brand" href="<?= route('auth', 'index') ?>" aria-label="Beranda Silap Gawat">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M16 10v11M12 10v11"/>
                    </svg>
                </span>
                <span>
                    <span class="brand-text-title" style="display:block;">Silap Gawat</span>
                    <span class="brand-text-subtitle">Kab. Mandailing Natal</span>
                </span>
            </a>
            <div class="header-meta" aria-label="Tahun berjalan"><?= date('Y') ?></div>
        </div>
    </header>

    <main role="main" id="main-content">
        <section class="hero-section">
            <p class="eyebrow">Portal Layanan Resmi</p>
            <h1 class="hero-title">Silakan pilih gerbang masuk sesuai kewenangan Anda</h1>
            <p class="hero-description">Sistem pelaporan terpadu Pemerintah Kabupaten Mandailing Natal untuk monitoring program kerja dan pembangunan daerah.</p>
        </section>

        <section class="grid-container" aria-label="Pilihan akses pengguna">
            <div class="role-grid">

                <a class="role-card is-admin" href="<?= route('auth', 'admin') ?>" aria-label="Masuk sebagai Administrator">
                    <span class="role-icon-shell" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </span>
                    <span class="role-badge">Eksekutif</span>
                    <h2 class="role-title">Administrator Utama</h2>
                    <p class="role-desc">Kontrol penuh operasional sistem pelaporan bupati. Kelola seluruh integrasi data OPD, kecamatan, laporan kerja, serta penugasan wewenang dinas secara terpusat.</p>
                    <span class="role-cta">
                        Akses Dashboard
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>

                <a class="role-card is-camat" href="<?= route('auth', 'camat') ?>" aria-label="Masuk sebagai Camat">
                    <span class="role-icon-shell" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V9M19 21V9M9 21v-6h6v6M2 10l10-7 10 7"/></svg>
                    </span>
                    <span class="role-badge">Kecamatan</span>
                    <h2 class="role-title">Wilayah Kecamatan</h2>
                    <p class="role-desc">Akses monitoring khusus camat untuk memantau kemajuan pembangunan, mengawal program kerja wilayah, serta mengoordinasikan laporan dari desa.</p>
                    <span class="role-cta">
                        Masuk Portal
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>

                <a class="role-card is-opd" href="<?= route('auth', 'opd') ?>" aria-label="Masuk sebagai Perangkat Daerah">
                    <span class="role-icon-shell" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="1"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                        </svg>
                    </span>
                    <span class="role-badge">Perangkat Daerah</span>
                    <h2 class="role-title">Dinas Perangkat Daerah</h2>
                    <p class="role-desc">Portal resmi Organisasi Perangkat Daerah (OPD) untuk sinkronisasi target indikator, memasukkan laporan bulanan, dan melacak tindak lanjut.</p>
                    <span class="role-cta">
                        Masuk Portal
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>

            </div>
        </section>
    </main>

    <footer class="site-footer" role="contentinfo">
        <span class="footer-text">&copy; <?= date('Y') ?> Pemerintah Kabupaten Mandailing Natal. Hak Cipta Dilindungi.</span>
        <nav class="footer-links" aria-label="Tautan kaki halaman">
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Pusat Bantuan</a>
        </nav>
    </footer>

</body>
</html>
