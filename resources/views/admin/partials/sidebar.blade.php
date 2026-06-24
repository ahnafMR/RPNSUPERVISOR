<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i><p>Kelola User</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.lokasi.index') }}" class="nav-link {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-map-marker-alt"></i><p>Kelola Lokasi</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.checkin.index') }}" class="nav-link {{ request()->routeIs('admin.checkin.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-camera"></i><p>Kelola Check-In</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i><p>Kelola Laporan</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.temuan.index') }}" class="nav-link {{ request()->routeIs('admin.temuan.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-exclamation-triangle"></i><p>Kelola Temuan</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.monitoring.index') }}" class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-map"></i><p>Monitoring Peta</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.audit.index') }}" class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-history"></i><p>Audit Log</p>
    </a>
</li>
