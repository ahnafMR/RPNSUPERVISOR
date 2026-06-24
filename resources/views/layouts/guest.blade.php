<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Login') — RPN Supervisor</title>

    <!-- ── PWA Meta Tags ── -->
    <meta name="application-name" content="RPN Supervisor">
    <meta name="description" content="Sistem Manajemen Laporan Inspeksi RPN">
    <meta name="theme-color" content="#4e73df">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RPN Supervisor">
    <meta name="msapplication-TileColor" content="#4e73df">
    <meta name="msapplication-TileImage" content="{{ asset('icons/icon-144x144.png') }}">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- ── PWA Manifest ── -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- ── Apple Touch Icons ── -->
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('icons/icon-512x512.png') }}">

    <!-- ── Favicon ── -->
    <link rel="icon" type="image/png" sizes="96x96"  href="{{ asset('icons/icon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AdminLTE base (structure only) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Neumorphism Custom UI -->
    <link rel="stylesheet" href="{{ asset('css/neumorphism.css') }}">

    <style>
        /* Decorative background blobs */
        body.login-page::before,
        body.login-page::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        body.login-page::before {
            width: 500px; height: 500px;
            top: -160px; left: -160px;
            background: radial-gradient(circle, rgba(78,115,223,0.08) 0%, transparent 70%);
        }
        body.login-page::after {
            width: 400px; height: 400px;
            bottom: -120px; right: -120px;
            background: radial-gradient(circle, rgba(28,200,138,0.07) 0%, transparent 70%);
        }
        .login-box { position: relative; z-index: 1; }
    </style>
</head>
<body class="hold-transition login-page">

    <div class="login-box">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ── PWA: Service Worker ── -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(function (reg) { console.log('[SW] Registered:', reg.scope); })
                .catch(function (err) { console.warn('[SW] Failed:', err); });
        });
    }
    </script>
</body>
</html>
