@extends('layouts.app')

@section('title', 'Kelola User')
@section('page_title', 'Kelola User')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<style>
.role-badge-admin      { background: linear-gradient(135deg,#4e73df,#3a57c7)!important; color:#fff!important; }
.role-badge-supervisor { background: linear-gradient(135deg,#1cc88a,#13a06d)!important; color:#fff!important; }
.user-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #fff; font-size: .9rem; flex-shrink: 0;
    box-shadow: 3px 3px 8px rgba(0,0,0,0.15);
}
</style>
@endpush

@section('content')

{{-- ── Header actions ── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
    <div>
        <p style="color:var(--text-muted);font-size:.82rem;margin:0;">
            Total <strong style="color:var(--text-primary);">{{ $users->count() }}</strong> akun terdaftar
        </p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus mr-2"></i>Tambah User
    </a>
</div>

{{-- ── Filter card ── --}}
<div class="card mb-3">
    <div class="card-body" style="padding:16px 20px!important;">
        <form method="GET" class="d-flex flex-wrap align-items-center" style="gap:10px;">
            <div style="flex:1;min-width:180px;">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search" style="font-size:.75rem;"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama atau email..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <select name="role" class="form-control" style="max-width:180px;">
                <option value="">Semua Role</option>
                <option value="admin"      {{ request('role') === 'admin'      ? 'selected' : '' }}>Administrator</option>
                <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i>Reset
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ── Users table ── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:44px;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Total Laporan</th>
                        <th>Dibuat</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $i => $user)
                    <tr>
                        <td style="color:var(--text-muted);font-size:.8rem;">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center" style="gap:10px;">
                                <div class="user-avatar"
                                     style="background:{{ $user->role->value === 'admin'
                                         ? 'linear-gradient(135deg,#4e73df,#3a57c7)'
                                         : 'linear-gradient(135deg,#1cc88a,#13a06d)' }};">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-primary);font-size:.88rem;">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge badge-primary ml-1" style="font-size:.6rem;">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary);font-size:.85rem;">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role->value === 'admin' ? 'role-badge-admin' : 'role-badge-supervisor' }}">
                                <i class="fas {{ $user->role->value === 'admin' ? 'fa-shield-alt' : 'fa-user-tie' }} mr-1"></i>
                                {{ $user->role->value === 'admin' ? 'Administrator' : 'Supervisor' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700;color:var(--accent);">{{ $user->laporans->count() }}</span>
                            <span style="color:var(--text-muted);font-size:.78rem;"> laporan</span>
                        </td>
                        <td style="color:var(--text-muted);font-size:.8rem;">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="d-flex" style="gap:6px;">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Reset password --}}
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                      onsubmit="return confirm('Reset password {{ addslashes($user->name) }} ke default?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                                {{-- Delete --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                      onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
                            <i class="fas fa-users fa-2x d-block mb-2" style="opacity:.3;"></i>
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
