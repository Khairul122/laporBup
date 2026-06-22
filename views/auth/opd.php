<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk sebagai OPD — Sistem Layanan Pelaporan Pemerintah Kabupaten Mandailing Natal.">
    <title><?= htmlspecialchars($appData['title']) ?> — OPD</title>
    <link rel="shortcut icon" href="<?= asset('assets/images/favicon.png') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/design-tokens.css') ?>" />

    <style>
        :root {
            --ink:        #0F172A;
            --ink-2:      #334155;
            --canvas:     #F8FAFC;
            --surface:    #FFFFFF;
            --muted:      #E8ECF1;
            --border:     #E2E8F0;
            --accent:     #B45309; /* OPD: amber */
            --accent-soft:#FCEEDD;
            --accent-ink: #8A4109;
            --danger:     #DC2626;
            --danger-soft:#FDECEC;
            --focus-ring: #B45309;

            --font-serif: 'EB Garamond', Georgia, serif;
            --font-sans: 'Lato', Arial, sans-serif;
            --ease: cubic-bezier(0.2, 0.65, 0.3, 1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
        }

        html, body { height: 100%; }

        body {
            font-family: var(--font-sans);
            background-color: var(--surface);
            color: var(--ink-2);
            font-size: 16px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        :focus-visible {
            outline: 3px solid var(--focus-ring);
            outline-offset: 2px;
            border-radius: 4px;
        }

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
        }

        .skip-link:focus { left: 0; }

        .civic-strip {
            height: 4px;
            background: linear-gradient(90deg, #B91C1C 0%, #B91C1C 50%, #FFFFFF 50%, #FFFFFF 100%);
        }

        .split-wrapper {
            display: flex;
            min-height: calc(100dvh - 4px);
            width: 100%;
        }

        .context-panel {
            flex: 0 0 44%;
            background: linear-gradient(165deg, var(--accent-soft) 0%, #FFFFFF 65%);
            color: var(--ink);
            padding: 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        .context-panel::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(180, 83, 9, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .brand-emblem {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-emblem svg { width: 20px; height: 20px; stroke: #FFFFFF; stroke-width: 1.5; fill: none; }

        .brand-name { font-size: 14px; font-weight: 700; color: var(--ink); }

        .context-main { margin: 40px 0; position: relative; z-index: 1; }

        .role-tag-pill {
            display: inline-flex;
            padding: 5px 14px;
            border-radius: 999px;
            background: #FFFFFF;
            border: 1px solid var(--border);
            color: var(--accent-ink);
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .context-title {
            font-family: var(--font-serif);
            font-size: clamp(26px, 3.2vw, 32px);
            font-weight: 600;
            line-height: 1.25;
            color: var(--ink);
            margin-bottom: 12px;
            text-wrap: balance;
        }

        .context-desc {
            font-size: 14.5px;
            color: var(--ink-2);
            line-height: 1.65;
            margin-bottom: 36px;
        }

        .feature-list { display: flex; flex-direction: column; }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        .feature-symbol-shell {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #FFFFFF;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-symbol-shell svg { width: 16px; height: 16px; stroke: var(--accent); stroke-width: 1.6; fill: none; }

        .feature-title { font-size: 13.5px; font-weight: 700; color: var(--ink); margin-bottom: 2px; }

        .feature-desc { font-size: 12.5px; color: var(--ink-2); line-height: 1.45; }

        .back-nav-box { padding-top: 8px; position: relative; z-index: 1; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink-2);
            text-decoration: none;
            min-height: 44px;
            padding: 4px 0;
            transition: color 150ms var(--ease);
        }

        .back-link:hover { color: var(--accent); }
        .back-link svg { width: 16px; height: 16px; stroke-width: 2; fill: none; }

        .form-panel {
            flex: 1;
            padding: 56px 64px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--surface);
        }

        .form-header { margin-bottom: 28px; max-width: 420px; width: 100%; }

        .form-header h2 {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .form-header p { font-size: 14px; color: var(--ink-2); }

        form { max-width: 420px; width: 100%; }

        .field-group { margin-bottom: 20px; }

        .field-label {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .required-mark { color: var(--danger); margin-left: 2px; }

        .field-input-wrap { position: relative; }

        .field-input {
            width: 100%;
            min-height: 48px;
            padding: 0 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-size: 15.5px;
            font-family: var(--font-sans);
            color: var(--ink);
            outline: none;
            transition: border-color 150ms var(--ease), box-shadow 150ms var(--ease);
        }

        .field-input::placeholder { color: #94A3B8; }

        .field-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .field-input.with-toggle { padding-right: 48px; }

        .field-group.is-error .field-input {
            border-color: var(--danger);
            background: var(--danger-soft);
        }

        .field-error {
            display: none;
            font-size: 12.5px;
            color: var(--danger);
            margin-top: 6px;
            font-weight: 600;
        }

        .field-error.show { display: block; }

        .pwd-toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: var(--ink-2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 150ms var(--ease), color 150ms var(--ease);
        }

        .pwd-toggle-btn:hover { background: var(--muted); color: var(--ink); }
        .pwd-toggle-btn svg { width: 18px; height: 18px; stroke-width: 1.6; fill: none; }

        .btn-submit {
            width: 100%;
            min-height: 50px;
            background: var(--accent);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: var(--font-sans);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 150ms var(--ease), transform 100ms var(--ease);
            margin-top: 4px;
        }

        .btn-submit:hover { background: var(--accent-ink); }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

        .btn-icon-box svg { width: 16px; height: 16px; stroke-width: 2; fill: none; stroke: #FFFFFF; }

        .form-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 12.5px;
            color: var(--ink-2);
            max-width: 420px;
            width: 100%;
        }

        .toast-wrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 380px;
            width: calc(100vw - 40px);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
            border: 1px solid var(--border);
            border-left: 4px solid var(--ink);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: opacity 200ms var(--ease), transform 200ms var(--ease);
        }

        .toast.success { border-left-color: #15803D; }
        .toast.error { border-left-color: var(--danger); }

        .toast-dot-wrapper {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast.success .toast-dot-wrapper { background: #E1F3E8; }
        .toast.error .toast-dot-wrapper { background: var(--danger-soft); }

        .toast-dot-wrapper svg { width: 13px; height: 13px; stroke-width: 2.5; fill: none; }
        .toast.success .toast-dot-wrapper svg { stroke: #15803D; }
        .toast.error .toast-dot-wrapper svg { stroke: var(--danger); }

        .toast-body { flex: 1; }
        .toast-ttl { font-size: 13.5px; font-weight: 700; color: var(--ink); margin-bottom: 2px; }
        .toast-msg { font-size: 12.5px; color: var(--ink-2); line-height: 1.4; }

        .toast-x {
            background: var(--muted);
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--ink-2);
            flex-shrink: 0;
        }

        .toast.removing { opacity: 0; transform: translateX(20px); }

        .spinner-shell {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            border-top-color: #FFFFFF;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 800px) {
            .split-wrapper { flex-direction: column; }

            .context-panel {
                flex: 0 0 auto;
                padding: 28px 24px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .context-main { margin: 20px 0 4px; }
            .context-desc { margin-bottom: 20px; }
            .feature-list { display: none; }
            .back-nav-box { display: none; }

            .form-panel {
                flex: 1;
                padding: 32px 24px 40px;
                justify-content: flex-start;
                align-items: stretch;
            }

            .form-header, form, .form-footer { max-width: none; margin-left: 0; }
        }
    </style>
</head>
<body>

    <a href="#login-form" class="skip-link">Lewati ke formulir login</a>
    <div class="civic-strip" role="presentation"></div>

    <div class="split-wrapper">

        <aside class="context-panel">
            <div class="brand-header">
                <span class="brand-emblem" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M16 10v11M12 10v11"/>
                    </svg>
                </span>
                <span class="brand-name">Silap Gawat</span>
            </div>

            <div class="context-main">
                <span class="role-tag-pill">OPD</span>

                <h1 class="context-title"><?php if (!empty($appData['name'])): ?><?= htmlspecialchars($appData['name']) ?><br><?php endif; ?><?= htmlspecialchars($appData['description']) ?></h1>
                <p class="context-desc">Portal laporan kegiatan dan program perangkat daerah.</p>

                <div class="feature-list" role="list">
                    <div class="feature-item" role="listitem">
                        <span class="feature-symbol-shell" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <span>
                            <span class="feature-title" style="display:block;">Laporan Dinas</span>
                            <span class="feature-desc">Input laporan realisasi kegiatan internal perangkat dinas.</span>
                        </span>
                    </div>
                    <div class="feature-item" role="listitem">
                        <span class="feature-symbol-shell" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        </span>
                        <span>
                            <span class="feature-title" style="display:block;">Monitoring Program</span>
                            <span class="feature-desc">Kawal target progres fisik dan realisasi anggaran.</span>
                        </span>
                    </div>
                    <div class="feature-item" role="listitem">
                        <span class="feature-symbol-shell" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <span>
                            <span class="feature-title" style="display:block;">Koordinasi Lintas OPD</span>
                            <span class="feature-desc">Pelaporan program terpadu terintegrasi antar instansi.</span>
                        </span>
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

        <main class="form-panel" id="login-form">
            <div class="form-header">
                <h2>Masuk OPD</h2>
                <p>Silakan masukkan kredensial perangkat daerah Anda.</p>
            </div>

            <form id="loginForm" novalidate>

                <div class="field-group" id="username-group">
                    <label for="username" class="field-label">Username<span class="required-mark" aria-hidden="true">*</span></label>
                    <div class="field-input-wrap">
                        <input type="text" class="field-input" id="username" name="username"
                               placeholder="nama pengguna" autocomplete="username" required aria-required="true"
                               aria-describedby="username-error">
                    </div>
                    <div class="field-error" id="username-error" role="alert"></div>
                </div>

                <div class="field-group" id="password-group">
                    <label for="password" class="field-label">Password<span class="required-mark" aria-hidden="true">*</span></label>
                    <div class="field-input-wrap">
                        <input type="password" class="field-input with-toggle" id="password" name="password"
                               placeholder="kata sandi" autocomplete="current-password" required aria-required="true"
                               aria-describedby="password-error">
                        <button type="button" class="pwd-toggle-btn" id="pwdToggle" aria-label="Tampilkan password" title="Tampilkan password">
                            <svg id="eyeIcon" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="field-error" id="password-error" role="alert"></div>
                </div>

                <button type="submit" class="btn-submit" id="loginBtn" aria-live="polite">
                    <span id="loginText">Masuk</span>
                    <span class="btn-icon-box" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </button>

            </form>

            <p class="form-footer">&copy; <?= date('Y') ?> Pemerintah Kabupaten Mandailing Natal</p>
        </main>

    </div>

<script>
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

    let _tw = null;

    function _getWrap() {
        if (!_tw) {
            _tw = document.createElement('div');
            _tw.className = 'toast-wrap';
            _tw.setAttribute('aria-live', 'polite');
            document.body.appendChild(_tw);
        }
        return _tw;
    }

    function showToast(title, msg, type = 'success', ms = 4500) {
        const w = _getWrap();
        let iconSvg = type === 'success'
            ? '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>'
            : '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

        const t = document.createElement('div');
        t.className = `toast ${type}`;
        t.innerHTML = `
            <div class="toast-dot-wrapper">${iconSvg}</div>
            <div class="toast-body">
                <div class="toast-ttl">${title}</div>
                <div class="toast-msg">${msg}</div>
            </div>
            <button class="toast-x" onclick="_rmToast(this)" aria-label="Tutup notifikasi">&times;</button>`;
        w.appendChild(t);
        setTimeout(() => { const b = t.querySelector('.toast-x'); if (b) _rmToast(b); }, ms);
    }

    function _rmToast(btn) {
        const t = btn.closest('.toast');
        if (t && !t.classList.contains('removing')) {
            t.classList.add('removing');
            setTimeout(() => t.remove(), 300);
        }
    }

    function setErr(id, msg) {
        document.getElementById(id + '-group').classList.add('is-error');
        const e = document.getElementById(id + '-error');
        if (e) { e.textContent = msg; e.classList.add('show'); }
    }

    function clrErr(id) {
        document.getElementById(id + '-group').classList.remove('is-error');
        const e = document.getElementById(id + '-error');
        if (e) e.classList.remove('show');
    }

    ['username', 'password'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => clrErr(id));
    });

    function setLoad(on) {
        const btn  = document.getElementById('loginBtn');
        const text = document.getElementById('loginText');
        btn.disabled = on;
        btn.setAttribute('aria-busy', on);
        text.innerHTML = on
            ? '<span class="spinner-shell" role="status"></span> Memproses...'
            : 'Masuk';
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const u = document.getElementById('username').value.trim();
        const p = document.getElementById('password').value;
        let ok = true;
        if (!u) { setErr('username', 'Username wajib diisi.'); ok = false; }
        if (!p) { setErr('password', 'Password wajib diisi.'); ok = false; }
        if (!ok) {
            document.getElementById(!u ? 'username' : 'password').focus();
            return;
        }

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
        } catch (err) {
            showToast('Gangguan Jaringan', 'Gagal terhubung dengan server pelaporan.', 'error');
        } finally {
            setLoad(false);
        }
    });

    document.addEventListener('DOMContentLoaded', () => document.getElementById('username').focus());
</script>

</body>
</html>
