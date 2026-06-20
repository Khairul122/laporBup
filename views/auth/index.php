<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Sistem Layanan Pelaporan Bupati — Pemerintah Kabupaten Mandailing Natal.">
    <title>Login — Sistem Layanan Pelaporan</title>
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
            --prestige-navy:   #0A1D37; /* Trust, authority */
            --prestige-gold:   #B8976C; /* Gold accents */
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

        /* ── EDITORIAL SPLIT CONTAINER ── */
        .login-wrapper {
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

        .login-core {
            width: 100%;
            background: var(--surface);
            border-radius: calc(32px - 8px);
            display: flex;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* ── LEFT: CONTEXT PANEL (42%) ── */
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

        /* Left panel glowing orb */
        .context-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(184, 151, 108, 0.12) 0%, transparent 60%);
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
            stroke: var(--prestige-gold);
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
            margin: 48px 0;
            position: relative;
            z-index: 2;
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
            color: var(--prestige-gold);
        }

        .context-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        /* Thin fine list styling */
        .feature-list {
            display: flex;
            flex-direction: column;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .feature-dot-shell {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: rgba(184, 151, 108, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .feature-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--prestige-gold);
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
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.4;
        }

        .context-footer {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 0.05em;
            position: relative;
            z-index: 2;
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

        /* Input fields with double bezels */
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

        /* Focus state nested highlights */
        .double-bezel-input:focus-within {
            background: rgba(184, 151, 108, 0.1);
            border-color: rgba(184, 151, 108, 0.2);
        }

        .field-input:focus {
            border-color: var(--prestige-gold);
            box-shadow: 0 0 0 3px rgba(184, 151, 108, 0.12);
        }

        .field-input.with-toggle {
            padding-right: 48px;
        }

        /* Error States */
        .double-bezel-input.is-error {
            background: rgba(220, 38, 38, 0.05);
            border-color: rgba(220, 38, 38, 0.15);
        }

        .double-bezel-input.is-error .field-input {
            border-color: #DC2626;
            background: #FFFDFD;
        }

        .field-error {
            display: none;
            font-size: 11px;
            color: #DC2626;
            margin-top: 6px;
            font-weight: 500;
            letter-spacing: -0.01em;
        }

        .field-error.show {
            display: block;
        }

        /* Password visibility toggle button */
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

        /* Remember me Checkbox */
        .remember-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            cursor: pointer;
        }

        .remember-check {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 1.5px solid rgba(10, 29, 55, 0.15);
            border-radius: 4px;
            cursor: pointer;
            outline: none;
            transition: var(--transition-premium);
            background: #FFFFFF;
            position: relative;
        }

        .remember-check:checked {
            background: var(--prestige-gold);
            border-color: var(--prestige-gold);
        }

        .remember-check:checked::after {
            content: "";
            position: absolute;
            left: 4.5px;
            top: 1.5px;
            width: 4px;
            height: 8px;
            border: solid #FFFFFF;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .remember-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            user-select: none;
            cursor: pointer;
        }

        /* Submit Button (Pill with nested trailing icon) */
        .btn-login-container {
            position: relative;
        }

        .btn-login {
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

        .btn-login:hover {
            background: var(--prestige-gold);
            color: var(--prestige-navy);
            box-shadow: 0 10px 25px rgba(184, 151, 108, 0.2);
        }

        .btn-login:hover .btn-icon-box {
            background: rgba(10, 29, 55, 0.1);
        }

        .btn-login:hover .btn-icon-box svg {
            stroke: var(--prestige-navy);
            transform: translate(2px, -2px);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── FOOTER LINKS ── */
        .form-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid rgba(10, 29, 55, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-footer-link {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-premium);
        }

        .form-footer-link:hover {
            color: var(--prestige-gold);
        }

        .footer-dot-sep {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(10, 29, 55, 0.15);
        }

        /* ── TOAST NOTIFICATION SYSTEM ── */
        .toast-container {
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

        .toast-notification {
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

        .toast-notification.success {
            border-left-color: #1E3F20;
        }

        .toast-notification.error {
            border-left-color: #DC2626;
        }

        .toast-icon-wrapper {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-notification.success .toast-icon-wrapper {
            background: rgba(30, 63, 32, 0.1);
        }

        .toast-notification.error .toast-icon-wrapper {
            background: rgba(220, 38, 38, 0.1);
        }

        .toast-icon-wrapper svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
            fill: none;
        }

        .toast-notification.success .toast-icon-wrapper svg {
            stroke: #1E3F20;
        }

        .toast-notification.error .toast-icon-wrapper svg {
            stroke: #DC2626;
        }

        .toast-body {
            flex: 1;
        }

        .toast-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--prestige-navy);
            margin-bottom: 2px;
        }

        .toast-message {
            color: var(--text-muted);
            font-size: 12.5px;
            line-height: 1.4;
        }

        .toast-close {
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

        .toast-close:hover {
            background: rgba(10, 29, 55, 0.08);
            color: var(--prestige-navy);
        }

        .toast-notification.removing {
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

        .btn-login:hover .spinner-shell {
            border: 2px solid rgba(10, 29, 55, 0.2);
            border-top-color: var(--prestige-navy);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── ANIMATIONS ── */
        @media (prefers-reduced-motion: no-preference) {
            .login-wrapper {
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

        /* ── RESPONSIVE DEGRADATION (MOBILE COLLAPSE) ── */
        @media (max-width: 768px) {
            body {
                padding: 16px;
                align-items: flex-start;
            }

            .login-wrapper {
                border-radius: 24px;
                padding: 6px;
            }

            .login-core {
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
                display: none; /* Show only 2 items on mobile */
            }

            .form-panel {
                padding: 40px 32px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-core">

        <!-- LEFT PANEL: PRESTIGE CONTEXT -->
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
                <h1 class="context-title">Sistem Layanan<br><em>Pelaporan Bupati</em></h1>
                <p class="context-desc">Kabupaten Mandailing Natal &mdash; Platform eksekutif monitoring dan koordinasi program kerja pemerintah daerah.</p>

                <div class="feature-list" role="list">
                    <div class="feature-item" role="listitem">
                        <div class="feature-dot-shell"><span class="feature-dot"></span></div>
                        <div class="feature-body">
                            <div class="feature-title">Keamanan Eksekutif</div>
                            <div class="feature-desc">Transmisi data terenkripsi standar pemerintahan.</div>
                        </div>
                    </div>
                    <div class="feature-item" role="listitem">
                        <div class="feature-dot-shell"><span class="feature-dot"></span></div>
                        <div class="feature-body">
                            <div class="feature-title">Sinkronisasi Real-Time</div>
                            <div class="feature-desc">Pelaporan progres langsung ke meja pimpinan.</div>
                        </div>
                    </div>
                    <div class="feature-item" role="listitem">
                        <div class="feature-dot-shell"><span class="feature-dot"></span></div>
                        <div class="feature-body">
                            <div class="feature-title">Rekonsiliasi Data</div>
                            <div class="feature-desc">Validasi indikator capaian lintas sektoral.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="context-footer">
                &copy; <?= date('Y') ?> Pemerintah Kabupaten Mandailing Natal
            </div>
        </aside>

        <!-- RIGHT PANEL: CARD LOGIN FORM -->
        <main class="form-panel">
            <div class="form-header">
                <h2>Masuk Layanan</h2>
                <p>Gunakan kredensial akun dinas atau admin Anda.</p>
            </div>

            <div id="alertContainer"></div>

            <form id="loginFormElement" novalidate>
                
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
                            <!-- Precise Eye Icon Line SVG -->
                            <svg id="passwordToggleIcon" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="field-error" id="password-error"></div>
                </div>

                <!-- REMEMBER ME -->
                <div style="display: block;">
                    <div class="remember-row" id="rememberMeRow">
                        <input type="checkbox" class="remember-check" id="rememberMe">
                        <label for="rememberMe" class="remember-label">Ingat saya untuk sesi berikutnya</label>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="btn-login-container">
                    <button type="submit" class="btn-login" id="loginBtn" aria-live="polite">
                        <span id="loginText">Masuk ke Sistem</span>
                        <span class="btn-icon-box">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            <div class="form-footer">
                <a href="#" class="form-footer-link">Kebijakan Privasi</a>
                <span class="footer-dot-sep"></span>
                <a href="#" class="form-footer-link">Pusat Bantuan</a>
            </div>
        </main>

    </div>
</div>

<script>
    /* ── PASSWORD TOGGLE ── */
    document.getElementById('pwdToggle').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('passwordToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            // Eye slash SVG
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
            this.title = 'Sembunyikan password';
            this.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';
            // Eye normal SVG
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            this.title = 'Tampilkan password';
            this.setAttribute('aria-label', 'Tampilkan password');
        }
    });

    /* ── TOAST NOTIFICATIONS ── */
    let _toastWrap = null;

    function _getToastWrap() {
        if (!_toastWrap) {
            _toastWrap = document.createElement('div');
            _toastWrap.className = 'toast-container';
            document.body.appendChild(_toastWrap);
        }
        return _toastWrap;
    }

    function showAlert(message, type) {
        const toastType = (type === 'success') ? 'success' : 'error';
        const title     = (type === 'success') ? 'Operasi Sukses' : 'Otentikasi Gagal';
        showToast(title, message.replace(/<[^>]*>/g, ''), toastType);
    }

    function showToast(title, message, type = 'success', duration = 5000) {
        const wrap  = _getToastWrap();
        
        // Custom SVG icons for toast
        let iconSvg = '';
        if (type === 'success') {
            iconSvg = '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';
        } else {
            iconSvg = '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
        }

        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <div class="toast-icon-wrapper">${iconSvg}</div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="_removeToast(this)" aria-label="Tutup">&times;</button>`;
        wrap.appendChild(toast);
        setTimeout(() => { const btn = toast.querySelector('.toast-close'); if(btn) _removeToast(btn); }, duration);
    }

    function _removeToast(btn) {
        const t = btn.closest('.toast-notification');
        if (t && !t.classList.contains('removing')) {
            t.classList.add('removing');
            setTimeout(() => t.remove(), 700);
        }
    }

    /* ── FIELD ERROR STYLING ── */
    function showFieldError(id, msg) {
        const bezel = document.getElementById(id + '-bezel');
        const err   = document.getElementById(id + '-error');
        if (bezel) bezel.classList.add('is-error');
        if (err) { err.textContent = msg; err.classList.add('show'); }
    }

    function clearFieldError(id) {
        const bezel = document.getElementById(id + '-bezel');
        const err   = document.getElementById(id + '-error');
        if (bezel) bezel.classList.remove('is-error');
        if (err) err.classList.remove('show');
    }

    ['username', 'password'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => clearFieldError(id));
    });

    /* ── LOADING STATES ── */
    function setButtonLoading(btnId, loading) {
        const btn  = document.getElementById(btnId);
        const text = document.getElementById('loginText');
        if (loading) {
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            text.innerHTML = '<span class="spinner-shell" role="status"></span>Memproses otentikasi...';
        } else {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            text.textContent = 'Masuk ke Sistem';
        }
    }

    /* ── REMEMBER ME STORAGE ── */
    const rememberMeCheckbox = document.getElementById('rememberMe');
    const usernameInput      = document.getElementById('username');

    window.addEventListener('load', function() {
        const saved  = localStorage.getItem('rememberedUsername');
        const isRem  = localStorage.getItem('rememberMe') === 'true';
        if (saved && isRem) {
            usernameInput.value        = saved;
            rememberMeCheckbox.checked = true;
        }
        usernameInput.focus();
    });

    rememberMeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            localStorage.setItem('rememberedUsername', usernameInput.value);
            localStorage.setItem('rememberMe', 'true');
        } else {
            localStorage.removeItem('rememberedUsername');
            localStorage.setItem('rememberMe', 'false');
        }
    });

    usernameInput.addEventListener('input', function() {
        if (rememberMeCheckbox.checked) {
            localStorage.setItem('rememberedUsername', this.value);
        }
    });

    /* ── FORM SUBMISSION ── */
    document.getElementById('loginFormElement').addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        let valid = true;
        if (!username) { showFieldError('username', 'Username wajib diisi.'); valid = false; }
        if (!password) { showFieldError('password', 'Kata sandi wajib diisi.'); valid = false; }
        if (!valid) return;

        setButtonLoading('loginBtn', true);

        try {
            const formData = new FormData(this);
            const data     = Object.fromEntries(formData);

            const response = await fetch('<?= route('auth', 'login') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned invalid content type');
            }

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'success');
                setTimeout(() => { window.location.href = result.redirect; }, 1200);
            } else {
                showAlert(result.message, 'danger');
            }
        } catch (error) {
            console.error('Login error:', error);
            showAlert('Terjadi gangguan komunikasi dengan server. Sila cuba lagi.', 'danger');
        } finally {
            setButtonLoading('loginBtn', false);
        }
    });
</script>

</body>
</html>