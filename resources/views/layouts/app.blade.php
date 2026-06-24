<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RPN Supervisor') — {{ config('app.name', 'RPN Supervisor') }}</title>

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
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('icons/icon-512x512.png') }}">

    <!-- ── Favicon ── -->
    <link rel="icon" type="image/png" sizes="96x96"   href="{{ asset('icons/icon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AdminLTE (base structure only) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Neumorphism Custom UI (includes responsive + bottom-nav styles) -->
    <link rel="stylesheet" href="{{ asset('css/neumorphism.css') }}">

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- ===================== SIDEBAR OVERLAY (mobile) ===================== --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ===================== TOP NAVBAR ===================== --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                {{-- Uses our custom JS on mobile, AdminLTE pushmenu on desktop --}}
                <a class="nav-link" id="sidebarToggle" href="#" role="button" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        {{-- Page title --}}
        <span class="navbar-brand ml-2 font-weight-bold"
              style="color:var(--text-primary);font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:50vw;">
            @yield('page_title', 'Dashboard')
        </span>

        <ul class="navbar-nav ml-auto align-items-center">

            {{-- Developer link --}}
            <li class="nav-item d-none d-md-flex align-items-center mr-1">
                <a href="https://napcreative.site" target="_blank" rel="noopener"
                   class="navbar-dev-badge" title="Developed by NAP Creative">
                    <span class="dev-dot-sm"></span>
                    NAP Creative
                </a>
            </li>

            {{-- WhatsApp support button --}}
            <li class="nav-item d-none d-md-flex align-items-center mr-2">
                <a href="https://wa.me/6289508770908?text=Halo%2C%20saya%20butuh%20bantuan%20terkait%20aplikasi%20RPN%20Supervisor."
                   target="_blank" rel="noopener"
                   class="navbar-wa-btn" title="Hubungi Support via WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.526 5.845L.057 23.522a.5.5 0 0 0 .615.611l5.78-1.516A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.896 0-3.673-.525-5.188-1.435l-.372-.22-3.853 1.011 1.029-3.752-.242-.386A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    Support
                </a>
            </li>

{{-- PWA Install button (hidden - only show on login/dashboard) --}}
             <li class="nav-item d-none align-items-center mr-2" style="display:none !important;">
                 <button id="pwa-install-btn" title="Install Aplikasi">
                     <i class="fas fa-download"></i>
                     Install App
                 </button>
             </li>

            {{-- User dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-link p-0" data-toggle="dropdown" href="#" role="button">
                    <div class="navbar-user-badge">
                        <div class="avatar-circle">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down d-none d-md-inline"
                           style="font-size:.65rem;color:var(--text-muted);"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="px-3 py-2 mb-1"
                         style="border-bottom:1px solid rgba(163,177,198,0.3);">
                        <p class="mb-0 font-weight-bold"
                           style="font-size:.85rem;color:var(--text-primary);">
                            {{ auth()->user()->name }}
                        </p>
                        <small style="color:var(--text-muted);">{{ auth()->user()->email }}</small>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"
                               style="color:var(--danger);"></i>Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    {{-- ===================== /TOP NAVBAR ===================== --}}

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4" id="mainSidebar">
        <a href="#" class="brand-link">
            <div class="d-flex align-items-center" style="gap:12px;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(78,115,223,0.4);flex-shrink:0;">
                    <i class="fas fa-clipboard-check" style="color:#fff;font-size:.9rem;"></i>
                </div>
                <div>
                    <span class="brand-text font-weight-bold"
                          style="font-size:.95rem;letter-spacing:.3px;">RPN Supervisor</span>
                    <br>
                    <small style="color:rgba(200,207,232,0.55);font-size:.7rem;font-weight:400;">
                        Sistem Inspeksi
                    </small>
                </div>
            </div>
        </a>

        <div class="sidebar">
            {{-- User info block --}}
            <div class="user-panel mt-3 pb-3 mb-2 d-flex"
                 style="border-bottom:1px solid rgba(255,255,255,0.07);padding:0 16px 12px;">
                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7b9ef0,var(--accent-dark));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.9rem;flex-shrink:0;box-shadow:0 3px 8px rgba(0,0,0,0.3);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="info" style="padding-left:10px;overflow:hidden;">
                    <a href="#" class="d-block"
                       style="color:#fff;font-weight:600;font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </a>
                    <small style="color:rgba(200,207,232,0.55);font-size:.7rem;">
                        @if(auth()->user()->role === 'admin')
                            <i class="fas fa-shield-alt mr-1"></i>Administrator
                        @else
                            <i class="fas fa-user-tie mr-1"></i>Supervisor
                        @endif
                    </small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview" role="menu">
                    @yield('sidebar')
                </ul>
            </nav>
        </div>
    </aside>
    {{-- ===================== /SIDEBAR ===================== --}}

    {{-- ===================== CONTENT WRAPPER ===================== --}}
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </div>
        </section>
    </div>
    {{-- ===================== /CONTENT WRAPPER ===================== --}}

    {{-- Footer (hidden on mobile via CSS) --}}
    <footer class="main-footer">
        <strong>&copy; {{ date('Y') }} RPN Supervisor.</strong>
        <span class="ml-1">Sistem Manajemen Laporan Inspeksi.</span>
        <div class="float-right d-none d-sm-inline-block" style="font-size:.75rem;display:flex;align-items:center;gap:10px;">
            <a href="https://napcreative.site" target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:5px;color:var(--accent);font-weight:600;font-size:.75rem;text-decoration:none;">
                <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block;"></span>
                NAP Creative
            </a>
        </div>
    </footer>

    {{-- ===================== MOBILE BOTTOM NAVIGATION ===================== --}}
    @php $role = auth()->user()->role; @endphp
    <nav class="mobile-bottom-nav" role="navigation" aria-label="Mobile Navigation">

        @if($role === 'admin')
            {{-- Admin bottom nav --}}
            <a href="{{ route('admin.dashboard') }}"
               class="mob-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}"
               class="mob-nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Laporan</span>
            </a>
            <a href="{{ route('admin.temuan.index') }}"
               class="mob-nav-item {{ request()->routeIs('admin.temuan.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Temuan</span>
            </a>
            <a href="{{ route('admin.monitoring.index') }}"
               class="mob-nav-item {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                <i class="fas fa-map"></i>
                <span>Peta</span>
            </a>
            <a href="{{ route('admin.checkin.index') }}"
               class="mob-nav-item {{ request()->routeIs('admin.checkin.*') ? 'active' : '' }}">
                <i class="fas fa-camera"></i>
                <span>Check-In</span>
            </a>

        @else
            {{-- Supervisor bottom nav with FAB --}}
            <a href="{{ route('supervisor.dashboard') }}"
               class="mob-nav-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('supervisor.laporan.index') }}"
               class="mob-nav-item {{ request()->routeIs('supervisor.laporan.*') && !request()->routeIs('supervisor.laporan.create') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Laporan</span>
            </a>

            {{-- Centre FAB: create laporan (only if checked in) --}}
            @php
                $checkin = auth()->user()->activeCheckin();
            @endphp
            @if($checkin)
                <a href="{{ route('supervisor.laporan.create') }}"
                   class="mob-nav-item mob-nav-fab {{ request()->routeIs('supervisor.laporan.create') ? 'active' : '' }}"
                   title="Buat Laporan Baru">
                    <i class="fas fa-plus"></i>
                </a>
            @else
                <a href="{{ route('supervisor.checkin.index') }}"
                   class="mob-nav-item mob-nav-fab"
                   title="Check-In Selfie">
                    <i class="fas fa-camera"></i>
                </a>
            @endif

            <a href="{{ route('supervisor.checkin.index') }}"
               class="mob-nav-item {{ request()->routeIs('supervisor.checkin.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i>
                <span>Check-In</span>
            </a>

            {{-- Logout shortcut --}}
            <form action="{{ route('logout') }}" method="POST" class="d-contents">
                @csrf
                <button type="submit" class="mob-nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </form>
        @endif

    </nav>

{{-- PWA floating install button (hidden - PWA install only on login/dashboard) --}}
     <div class="pwa-install-wrap" id="pwaInstallWrap" style="display:none !important;">
         <button id="pwa-install-btn" title="Install Aplikasi">
             <i class="fas fa-download"></i>
             Install Aplikasi
         </button>
     </div>
    {{-- ===================== /MOBILE BOTTOM NAVIGATION ===================== --}}

    {{-- ── Floating WhatsApp Support Button (always visible) ── --}}
    <a href="https://wa.me/6289508770908?text=Halo%2C%20saya%20butuh%20bantuan%20terkait%20aplikasi%20RPN%20Supervisor."
       target="_blank" rel="noopener"
       class="float-wa-btn" title="Hubungi Support via WhatsApp"
       aria-label="WhatsApp Support">
        <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.526 5.845L.057 23.522a.5.5 0 0 0 .615.611l5.78-1.516A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.896 0-3.673-.525-5.188-1.435l-.372-.22-3.853 1.011 1.029-3.752-.242-.386A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
        </svg>
        <span class="float-wa-label">Ada Masalah?</span>
    </a>

</div><!-- /.wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function () {
    // ── DataTables ──────────────────────────────────────────
    $('.datatable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
        dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rtip',
    });

    // ── Auto-dismiss alerts ──────────────────────────────────
    setTimeout(function () {
        $('.alert-dismissible').fadeOut(400);
    }, 5000);

    // ── Mobile sidebar toggle ────────────────────────────────
    // On mobile we manage the overlay ourselves; on desktop let AdminLTE handle it.
    var isMobile = function () { return window.innerWidth < 992; };

    $('#sidebarToggle').on('click', function (e) {
        if (isMobile()) {
            e.preventDefault();
            e.stopPropagation();
            $('body').toggleClass('sidebar-open');
        }
        // Desktop: AdminLTE pushmenu fires naturally via data-widget="pushmenu"
        // but we removed that attribute to avoid conflicts — re-attach it here.
    });

    // Close sidebar when overlay is tapped
    $('#sidebarOverlay').on('click', function () {
        $('body').removeClass('sidebar-open');
    });

    // Close sidebar when a nav link inside it is tapped on mobile
    $('#mainSidebar .nav-link').on('click', function () {
        if (isMobile()) {
            $('body').removeClass('sidebar-open');
        }
    });

    // Close sidebar on window resize to desktop
    $(window).on('resize', function () {
        if (!isMobile()) {
            $('body').removeClass('sidebar-open');
        }
    });

    // Desktop: re-enable AdminLTE pushmenu for the toggle button
    if (!isMobile()) {
        $('#sidebarToggle').attr('data-widget', 'pushmenu');
    }
});
</script>

@stack('scripts')

<!-- ── PWA: Service Worker ── -->
 <script>
 (function () {
     // Register service worker
     if ('serviceWorker' in navigator) {
         window.addEventListener('load', function () {
             navigator.serviceWorker
                 .register('/sw.js', { scope: '/' })
                 .then(function (reg) {
                     console.log('[SW] Registered:', reg.scope);
                 })
                 .catch(function (err) {
                     console.warn('[SW] Registration failed:', err);
                 });
         });
     }
 })();
 </script>
</body>
</html>
