@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kelola User</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        {{-- User summary card --}}
        <div class="card mb-3" style="background:linear-gradient(135deg,var(--accent),var(--accent-dark))!important;box-shadow:6px 6px 16px rgba(78,115,223,0.35),-4px -4px 12px rgba(255,255,255,0.5)!important;">
            <div class="card-body d-flex align-items-center" style="gap:16px;padding:18px 22px!important;">
                <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:1.2rem;flex-shrink:0;box-shadow:inset 2px 2px 6px rgba(0,0,0,0.2);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p style="margin:0;font-size:1rem;font-weight:700;color:#fff;">{{ $user->name }}</p>
                    <small style="color:rgba(255,255,255,0.75);">{{ $user->email }}</small>
                </div>
                <div class="ml-auto text-right">
                    <span class="badge" style="background:rgba(255,255,255,0.25);color:#fff;font-size:.72rem;padding:5px 10px;">
                        <i class="fas {{ $user->role->value === 'admin' ? 'fa-shield-alt' : 'fa-user-tie' }} mr-1"></i>
                        {{ $user->role->value === 'admin' ? 'Administrator' : 'Supervisor' }}
                    </span>
                    <div style="font-size:.72rem;color:rgba(255,255,255,0.6);margin-top:4px;">
                        Dibuat {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit form --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2" style="color:var(--accent);"></i>
                    Edit Informasi Akun
                </h3>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" autocomplete="off">
                @csrf @method('PUT')
                <div class="card-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user mr-1" style="color:var(--accent);"></i>Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope mr-1" style="color:var(--accent);"></i>Email
                        </label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label for="role">
                            <i class="fas fa-id-badge mr-1" style="color:var(--accent);"></i>Role / Jabatan
                        </label>
                        <select id="role" name="role"
                                class="form-control @error('role') is-invalid @enderror"
                                required
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}"
                                        {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                    {{ $role->value === 'admin' ? 'Administrator' : 'Supervisor' }}
                                </option>
                            @endforeach
                        </select>
                        @if($user->id === auth()->id())
                            {{-- Send role in hidden field if disabled --}}
                            <input type="hidden" name="role" value="{{ $user->role->value }}">
                            <small style="color:var(--text-muted);font-size:.75rem;">
                                <i class="fas fa-lock mr-1"></i>Tidak dapat mengubah role akun sendiri.
                            </small>
                        @endif
                        @error('role')
                            <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Divider --}}
                    <div style="display:flex;align-items:center;gap:12px;margin:20px 0 8px;">
                        <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
                        <span style="font-size:.72rem;color:var(--text-muted);font-weight:600;letter-spacing:.5px;text-transform:uppercase;">Ganti Password</span>
                        <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
                    </div>

                    <p style="font-size:.78rem;color:var(--text-muted);margin-bottom:16px;">
                        <i class="fas fa-info-circle mr-1"></i>Kosongkan jika tidak ingin mengubah password.
                    </p>

                    {{-- New Password --}}
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock mr-1" style="color:var(--accent);"></i>Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kosongkan jika tidak diubah">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text"
                                        onclick="togglePw('password','pwIcon1')"
                                        style="cursor:pointer;">
                                    <i class="fas fa-eye" id="pwIcon1" style="font-size:.8rem;"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock mr-1" style="color:var(--accent);"></i>Konfirmasi Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi password baru">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text"
                                        onclick="togglePw('password_confirmation','pwIcon2')"
                                        style="cursor:pointer;">
                                    <i class="fas fa-eye" id="pwIcon2" style="font-size:.8rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('fa-eye');
    i.classList.toggle('fa-eye-slash');
}
</script>
@endpush
