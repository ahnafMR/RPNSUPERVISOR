@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="card" style="border-radius:24px;">

    {{-- Header --}}
    <div class="card-header text-center" style="padding:36px 30px 24px;">

        {{-- Logo icon --}}
        <div class="login-logo-wrap">
            <i class="fas fa-clipboard-check"></i>
        </div>

        <h1 style="font-size:1.7rem;font-weight:800;color:var(--text-primary);margin-bottom:4px;letter-spacing:-0.5px;">
            <span style="color:var(--accent);">RPN</span> Supervisor
        </h1>
        <p style="color:var(--text-muted);font-size:0.82rem;margin:0;">
            Sistem Manajemen Laporan Inspeksi
        </p>
    </div>

    {{-- Body --}}
    <div class="card-body" style="padding:28px 32px 32px;">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf

            {{-- Email --}}
            <div class="form-group mb-4">
                <label for="email">
                    <i class="fas fa-envelope mr-1" style="color:var(--accent);"></i>Alamat Email
                </label>
                <div class="input-group">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="nama@perusahaan.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-at" style="font-size:.8rem;"></i>
                        </span>
                    </div>
                </div>
                @error('email')
                    <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group mb-4">
                <label for="password">
                    <i class="fas fa-lock mr-1" style="color:var(--accent);"></i>Password
                </label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required
                    >
                    <div class="input-group-append">
                        <button type="button" class="input-group-text btn-toggle-pw" title="Tampilkan password"
                            style="cursor:pointer;border-left:none;"
                            onclick="togglePassword()">
                            <i class="fas fa-eye" id="pwIcon" style="font-size:.8rem;"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Remember + Submit --}}
            <div class="row align-items-center mb-3">
                <div class="col-6">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" style="text-transform:none;letter-spacing:0;font-size:.82rem;color:var(--text-secondary);font-weight:500;">
                            Ingat Saya
                        </label>
                    </div>
                </div>
                <div class="col-6 text-right">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </button>
                </div>
            </div>
        </form>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;margin:16px 0;">
            <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
            <span style="font-size:.72rem;color:var(--text-muted);font-weight:500;letter-spacing:.5px;">AKUN DEMO</span>
            <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
        </div>

        {{-- Demo accounts --}}
        <div class="row" style="gap:0;">
            <div class="col-6 pr-1">
                <div style="background:var(--bg);border-radius:12px;box-shadow:inset 3px 3px 7px var(--shadow-dark),inset -3px -3px 7px var(--shadow-light);padding:10px 12px;cursor:pointer;"
                     onclick="fillDemo('admin@rpn.com','password')" title="Klik untuk mengisi">
                    <p style="margin:0;font-size:.7rem;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:.4px;">
                        <i class="fas fa-shield-alt mr-1"></i>Admin
                    </p>
                    <p style="margin:0;font-size:.73rem;color:var(--text-muted);">admin@rpn.com</p>
                </div>
            </div>
            <div class="col-6 pl-1">
                <div style="background:var(--bg);border-radius:12px;box-shadow:inset 3px 3px 7px var(--shadow-dark),inset -3px -3px 7px var(--shadow-light);padding:10px 12px;cursor:pointer;"
                     onclick="fillDemo('supervisor@rpn.com','password')" title="Klik untuk mengisi">
                    <p style="margin:0;font-size:.7rem;font-weight:700;color:var(--success);text-transform:uppercase;letter-spacing:.4px;">
                        <i class="fas fa-user-tie mr-1"></i>Supervisor
                    </p>
                    <p style="margin:0;font-size:.73rem;color:var(--text-muted);">supervisor@rpn.com</p>
                </div>
            </div>
        </div>

        {{-- WA Support button --}}
        <div class="mt-3">
            <a href="https://wa.me/6289508770908?text=Halo%2C%20saya%20butuh%20bantuan%20terkait%20aplikasi%20RPN%20Supervisor."
               target="_blank" rel="noopener"
               class="btn-wa-support">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.526 5.845L.057 23.522a.5.5 0 0 0 .615.611l5.78-1.516A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.896 0-3.673-.525-5.188-1.435l-.372-.22-3.853 1.011 1.029-3.752-.242-.386A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
                Ada masalah? Hubungi Support
            </a>
        </div>

    </div>
</div>

{{-- Developer credit (below the card) --}}
<div class="login-dev-credit">
    <span>Developed by</span>
    <a href="https://napcreative.site" target="_blank" rel="noopener" class="dev-link">
        <span class="dev-dot"></span>
        NAP Creative
    </a>
</div>

{{-- PWA Install Button --}}
<div class="pwa-install-wrap" id="pwaInstallWrapLogin">
    <button id="pwa-install-btn-login" title="Install Aplikasi">
        <i class="fas fa-download"></i>
        Install Aplikasi
    </button>
</div>

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(function (reg) { console.log('[SW] Registered:', reg.scope); })
            .catch(function (err) { console.warn('[SW] Failed:', err); });
    });
}

var deferredPromptLogin = null;
var installBtnLogin = document.getElementById('pwa-install-btn-login');
var installWrapLogin = document.getElementById('pwaInstallWrapLogin');

window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPromptLogin = e;
    if (installBtnLogin) installBtnLogin.style.display = 'flex';
    if (installWrapLogin) installWrapLogin.style.display = 'block';
});

if (installBtnLogin) {
    installBtnLogin.addEventListener('click', function () {
        if (!deferredPromptLogin) return;
        deferredPromptLogin.prompt();
        deferredPromptLogin.userChoice.then(function (choice) {
            if (choice.outcome === 'accepted') {
                console.log('[PWA] Installed');
            }
            deferredPromptLogin = null;
            installBtnLogin.style.display = 'none';
            if (installWrapLogin) installWrapLogin.style.display = 'none';
        });
    });
}
</script>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    const icon = document.getElementById('pwIcon');
    if (pw.type === 'password') {
        pw.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pw.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function fillDemo(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
