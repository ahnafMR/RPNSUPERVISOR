<li class="nav-item">
    <a href="{{ route('supervisor.dashboard') }}" class="nav-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('supervisor.checkin.index') }}" class="nav-link {{ request()->routeIs('supervisor.checkin.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-camera"></i><p>Check-In Selfie</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('supervisor.laporan.index') }}" class="nav-link {{ request()->routeIs('supervisor.laporan.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i><p>Laporan Inspeksi</p>
    </a>
</li>
