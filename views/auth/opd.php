<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk sebagai OPD — Sistem Layanan Pelaporan Pemerintah Kabupaten Mandailing Natal.">
    <title><?= htmlspecialchars($appData['title']) ?> — OPD</title>
    <link rel="shortcut icon" href="<?= asset('assets/images/favicon.png') ?>" />

    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..500;1,6..72,300..400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/design-tokens.css') ?>" />

    <style>
        :root {
            --canvas:         #FAF9F6; /* Soft warm off-white */
            --surface:        #FFFFFF;
            --prestige-navy:   #0A1D37; /* Institutional Navy */
            --prestige-accent: #8E6E27; /* Rich Ochre Gold for OPD */
            --border-alpha:    rgba(10, 29, 55, 0.06);
            --text-primary:    #1C2630;
            --text-muted:      #656E77;
            
            --ease-spring:     cubic-bezier(0.32, 0.72, 0, 1);
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
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

        /* ── EDITORIAL SPLIT CONFIGURATION ── */
        .split-wrapper {
            background: rgba(10, 29, 55, 0.02);
            border: 1px solid rgba(10, 29, 55, 0.04);
            padding: 8px;
            border-radius: 32px;
            max-width: 1000px;
            width: 100%;
            display: flex;
            box-shadow: 0 30px 70px rgba(10, 29, 55, 0.08);
            position: relative;
            z-index: 2;
        }

        .split-core {
            width: 100%;
            background: var(--surface);
            border-radius: calc(32px - 8px);
            display: flex;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* ── LEFT: PRESTIGE CONTEXT PANEL (42%) ── */
        .context-panel {
            flex: 0 0 42%;
            background: var(--prestige-navy);
            color: #FFFFFF;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Ambient gradient behind */
        .context-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 15%, rgba(142, 110, 39, 0.18) 0%, transparent 60%);
            pointer-events: none;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .brand-emblem-shell {
            background: rgba(255, 255, 255, 0.05);
            padding: 3px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-emblem-core {
            width: 28px;
            height: 28px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .brand-emblem-core svg {
            width: 16px;
            height: 16px;
            stroke: #FFFFFF;
            stroke-width: 1.5;
            fill: none;
        }

        .brand-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #FFFFFF;
        }

        .context-main {
            margin: 40px 0;
            position: relative;
            z-index: 2;
        }

        .role-tag-pill {
            display: inline-flex;
            align-items: center;
            background: rgba(142, 110, 39, 0.25);
            border: 1px solid rgba(142, 110, 39, 0.35);
            color: #F7DC6F;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .context-title {
            font-family: 'Newsreader', Georgia, serif;
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 400;
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .context-title em {
            font-style: italic;
            font-weight: 300;
            color: #F7DC6F;
        }

        .context-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* Fine feature list */
        .feature-list {
            display: flex;
            flex-direction: column;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 14px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .feature-symbol-shell {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-symbol-shell svg {
            width: 14px;
            height: 14px;
            stroke: #F7DC6F;
            stroke-width: 1.5;
            fill: none;
        }

        .feature-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .feature-title {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-desc {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.4;
        }

        /* Retro back button link */
        .back-nav-box {
            position: relative;
            z-index: 2;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-premium);
        }

        .back-link svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            transition: var(--transition-premium);
        }

        .back-link:hover {
            color: #F7DC6F;
        }

        .back-link:hover svg {
            transform: translateX(-4px);
        }

        /* ── RIGHT: FORM PANEL (58%) ── */
        .form-panel {
            flex: 1;
            padding: 56px 64px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--surface);
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-header h2 {
            font-family: 'Newsreader', Georgia, serif;
            font-size: 26px;
            font-weight: 400;
            color: var(--prestige-navy);
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Fields with double bezels */
        .field-group {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--prestige-navy);
            margin-bottom: 8px;
        }

        .double-bezel-input {
            background: rgba(10, 29, 55, 0.02);
            border: 1px solid rgba(10, 29, 55, 0.03);
            padding: 3px;
            border-radius: 12px;
            position: relative;
            transition: var(--transition-premium);
        }

        .field-input {
            width: 100%;
            height: 42px;
            padding: 0 16px;
            border: 1px solid rgba(10, 29, 55, 0.06);
            border-radius: calc(12px - 3px);
            background: #FFFFFF;
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition-premium);
        }

        .field-input::placeholder {
            color: rgba(10, 29, 55, 0.35);
        }

        /* Focus states */
        .double-bezel-input:focus-within {
            background: rgba(142, 110, 39, 0.06);
            border-color: rgba(142, 110, 39, 0.15);
        }

        .field-input:focus {
            border-color: var(--prestige-accent);
            box-shadow: 0 0 0 3px rgba(142, 110, 39, 0.1);
        }

        .field-input.with-toggle {
            padding-right: 48px;
        }

        /* Error state formatting */
        .double-bezel-input.is-error {
            background: rgba(159, 47, 45, 0.05);
            border-color: rgba(159, 47, 45, 0.15);
        }

        .double-bezel-input.is-error .field-input {
            border-color: #9F2F2D;
            background: #FFFDFD;
        }

        .field-error {
            display: none;
            font-size: 11px;
            color: #9F2F2D;
            margin-top: 6px;
            font-weight: 500;
        }

        .field-error.show {
            display: block;
        }

        /* Password show toggle */
        .pwd-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: var(--text-muted);
            transition: var(--transition-premium);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .pwd-toggle-btn:hover {
            color: var(--prestige-navy);
            transform: translateY(-50%) scale(1.05);
        }

        .pwd-toggle-btn svg {
            width: 16px;
            height: 16px;
            stroke-width: 1.5;
            fill: none;
        }

        /* Button design */
        .btn-submit {
            width: 100%;
            height: 48px;
            background: var(--prestige-navy);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: var(--transition-premium);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 6px 6px 24px;
            box-shadow: 0 4px 15px rgba(10, 29, 55, 0.05);
            margin-top: 8px;
        }

        .btn-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-premium);
        }

        .btn-icon-box svg {
            width: 14px;
            height: 14px;
            stroke-width: 2;
            fill: none;
            transition: var(--transition-premium);
            stroke: #FFFFFF;
        }

        .btn-submit:hover {
            background: var(--prestige-accent);
            box-shadow: 0 10px 25px rgba(142, 110, 39, 0.2);
        }

        .btn-submit:hover .btn-icon-box {
            background: rgba(10, 29, 55, 0.1);
        }

        .btn-submit:hover .btn-icon-box svg {
            transform: translate(2px, -2px);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Footer copy */
        .form-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid rgba(10, 29, 55, 0.06);
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
        }

        /* ── TOAST NOTIFICATIONS ── */
        .toast-wrap {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: var(--z-toast, 500);
            max-width: 380px;
            width: calc(100vw - 48px);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 20px 40px rgba(10, 29, 55, 0.08);
            border: 1px solid rgba(10, 29, 55, 0.04);
            border-left: 4px solid var(--prestige-navy);
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
            overflow: hidden;
            transition: var(--transition-premium);
        }

        .toast.success {
            border-left-color: #1E3F20;
        }

        .toast.error {
            border-left-color: #DC2626;
        }

        .toast-dot-wrapper {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast.success .toast-dot-wrapper {
            background: rgba(30, 63, 32, 0.1);
        }

        .toast.error .toast-dot-wrapper {
            background: rgba(220, 38, 38, 0.1);
        }

        .toast-dot-wrapper svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
            fill: none;
        }

        .toast.success .toast-dot-wrapper svg {
            stroke: #1E3F20;
        }

        .toast.error .toast-dot-wrapper svg {
            stroke: #DC2626;
        }

        .toast-body {
            flex: 1;
        }

        .toast-ttl {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--prestige-navy);
            margin-bottom: 2px;
        }

        .toast-msg {
            color: var(--text-muted);
            font-size: 12.5px;
            line-height: 1.4;
        }

        .toast-x {
            background: rgba(10, 29, 55, 0.04);
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 14px;
            transition: var(--transition-premium);
            flex-shrink: 0;
        }

        .toast-x:hover {
            background: rgba(10, 29, 55, 0.08);
            color: var(--prestige-navy);
        }

        .toast.removing {
            opacity: 0;
            transform: translateX(100%) scale(0.9);
        }

        /* Spinner */
        .spinner-shell {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #FFFFFF;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        .btn-submit:hover .spinner-shell {
            border: 2px solid rgba(10, 29, 55, 0.2);
            border-top-color: var(--prestige-navy);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── ANIMATIONS ── */
        @media (prefers-reduced-motion: no-preference) {
            .split-wrapper {
                opacity: 0;
                transform: translateY(20px);
                animation: premiumReveal 0.8s var(--ease-spring) forwards;
            }

            @keyframes premiumReveal {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body {
                padding: 16px;
                align-items: flex-start;
            }

            .split-wrapper {
                border-radius: 24px;
                padding: 6px;
            }

            .split-core {
                flex-direction: column;
                border-radius: calc(24px - 6px);
            }

            .context-panel {
                flex: 0 0 auto;
                padding: 40px 32px;
            }

            .context-main {
                margin: 24px 0;
            }

            .feature-item:nth-child(n+3) {
                display: none;
            }

            .form-panel {
                padding: 40px 32px;
            }
        }
    </style>
</head>
<body>

<div class="split-wrapper">
    <div class="split-core">

        <!-- LEFT PANEL: CONTEXT -->
        <aside class="context-panel">
            <div class="brand-header">
                <div class="brand-emblem-shell">
                    <div class="brand-emblem-core">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M16 10v11M12 10v11"/>
                        </svg>
                    </div>
                </div>
                <span class="brand-name">LaporBup</span>
            </div>

            <div class="context-main">
                <span class="role-tag-pill">OPD</span>
                
                <h1 class="context-title">
                    <?= htmlspecialchars($appData['name']) ?><br>
                    <em><?= htmlspecialchars($appData['description']) ?></em>
                </h1>
                <p class="context-desc">Portal laporan kegiatan dan program perangkat daerah.</p>

                <div class="feature-list" role="list">
                    <div class="feature-item" role="listitem">
                        <div class="feature-symbol-shell">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="feature-body">
                            <div class="feature-title">Laporan Dinas</div>
                            <div class="feature-desc">Input laporan realisasi kegiatan internal perangkat dinas.</div>
                        </div>
                    </div>
                    <div class="feature-item" role="listitem">
                        <div class="feature-symbol-shell">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        </div>
                        <div class="feature-body">
                            <div class="feature-title">Monitoring Program</div>
                            <div class="feature-desc">Kawal target progres fisik dan realisasi anggaran.</div>
                        </div>
                    </div>
                    <div class="feature-item" role="listitem">
                        <div class="feature-symbol-shell">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="feature-body">
                            <div class="feature-title">Koordinasi Lintas OPD</div>
                            <div class="feature-desc">Pelaporan program terpadu terintegrasi antar instansi.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="back-nav-box">
                <a href="<?= route('auth', 'index') ?>" class="back-link">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                    Kembali ke halaman utama
                </a>
            </div>
        </aside>

        <!-- RIGHT PANEL: FORM -->
        <main class="form-panel">
            <div class="form-header">
                <h2>Masuk OPD</h2>
                <p>Silakan masukkan kredensial perangkat daerah Anda.</p>
            </div>

            <form id="loginForm" novalidate>
                
                <!-- USERNAME -->
                <div class="field-group">
                    <label for="username" class="field-label">Username</label>
                    <div class="double-bezel-input" id="username-bezel">
                        <input type="text" class="field-input" id="username" name="username"
                               placeholder="nama pengguna" autocomplete="username" required>
                    </div>
                    <div class="field-error" id="username-error"></div>
                </div>

                <!-- PASSWORD -->
                <div class="field-group">
                    <label for="password" class="field-label">Password</label>
                    <div class="double-bezel-input" id="password-bezel">
                        <input type="password" class="field-input with-toggle" id="password" name="password"
                               placeholder="kata sandi" autocomplete="current-password" required>
                        <button type="button" class="pwd-toggle-btn" id="pwdToggle" aria-label="Tampilkan password" title="Tampilkan password">
                            <svg id="eyeIcon" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="field-error" id="password-error"></div>
                </div>

                <!-- SUBMIT -->
                <button type="submit" class="btn-submit" id="loginBtn" aria-live="polite">
                    <span id="loginText">Masuk</span>
                    <span class="btn-icon-box">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </span>
                </button>

            </form>

            <div class="form-footer">
                &copy; <?= date('Y') ?> Pemerintah Kabupaten Mandailing Natal
            </div>
        </main>

    </div>
</div>

<script>
    /* ── PASSWORD TOGGLE ── */
    const pwdInput = document.getElementById('password');
    const eyeIcon  = document.getElementById('eyeIcon');

    document.getElementById('pwdToggle').addEventListener('click', function() {
        const isHidden = pwdInput.type === 'password';
        pwdInput.type  = isHidden ? 'text' : 'password';
        this.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        
        eyeIcon.innerHTML = isHidden
            ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
            : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    });

    /* ── TOAST NOTIFICATION SYSTEM ── */
    let _tw = null;

    function _getWrap() {
        if (!_tw) { 
            _tw = document.createElement('div'); 
            _tw.className = 'toast-wrap'; 
            document.body.appendChild(_tw); 
        }
        return _tw;
    }

    function showToast(title, msg, type = 'success', ms = 4500) {
        const w = _getWrap();
        
        let iconSvg = '';
        if (type === 'success') {
            iconSvg = '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';
        } else {
            iconSvg = '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
        }

        const t = document.createElement('div');
        t.className = `toast ${type}`;
        t.innerHTML = `
            <div class="toast-dot-wrapper">${iconSvg}</div>
            <div class="toast-body">
                <div class="toast-ttl">${title}</div>
                <div class="toast-msg">${msg}</div>
            </div>
            <button class="toast-x" onclick="_rmToast(this)" aria-label="Tutup">&times;</button>`;
        w.appendChild(t);
        setTimeout(() => { const b = t.querySelector('.toast-x'); if(b) _rmToast(b); }, ms);
    }

    function _rmToast(btn) {
        const t = btn.closest('.toast');
        if (t && !t.classList.contains('removing')) {
            t.classList.add('removing');
            setTimeout(() => t.remove(), 700);
        }
    }

    /* ── FIELD VALIDATION ── */
    function setErr(id, msg) {
        const bezel = document.getElementById(id + '-bezel');
        const e     = document.getElementById(id + '-error');
        if (bezel) bezel.classList.add('is-error');
        if (e) { e.textContent = msg; e.classList.add('show'); }
    }

    function clrErr(id) {
        const bezel = document.getElementById(id + '-bezel');
        const e     = document.getElementById(id + '-error');
        if (bezel) bezel.classList.remove('is-error');
        if (e) e.classList.remove('show');
    }

    ['username', 'password'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => clrErr(id));
    });

    /* ── LOADING STATES ── */
    function setLoad(on) {
        const btn  = document.getElementById('loginBtn');
        const text = document.getElementById('loginText');
        btn.disabled = on;
        btn.setAttribute('aria-busy', on);
        text.innerHTML = on
            ? '<span class="spinner-shell" role="status"></span>Memproses...'
            : 'Masuk';
    }

    /* ── FORM SUBMISSION ── */
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const u = document.getElementById('username').value.trim();
        const p = document.getElementById('password').value;
        let ok = true;
        if (!u) { setErr('username', 'Username wajib diisi.'); ok = false; }
        if (!p) { setErr('password', 'Password wajib diisi.'); ok = false; }
        if (!ok) return;

        setLoad(true);
        try {
            const fd = new FormData();
            fd.append('username', u); 
            fd.append('password', p); 
            fd.append('login_role', 'opd');
            
            const res    = await fetch('<?= route('auth', 'login') ?>', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast('Login Berhasil', 'Mengalihkan Anda ke portal dinas…', 'success');
                setTimeout(() => { window.location.href = result.redirect; }, 1200);
            } else {
                showToast('Akses Ditolak', result.message || 'Username atau password salah.', 'error');
            }
        } catch(err) {
            showToast('Gangguan Jaringan', 'Gagal terhubung dengan server pelaporan.', 'error');
        } finally {
            setLoad(false);
        }
    });

    document.addEventListener('DOMContentLoaded', () => document.getElementById('username').focus());
</script>

</body>
</html>