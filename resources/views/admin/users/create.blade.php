@extends('layouts.app')

@section('title', 'Tambah User')
@section('page_title', 'Tambah User Baru')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kelola User</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-2" style="color:var(--accent);"></i>
                    Informasi Akun Baru
                </h3>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="card-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user mr-1" style="color:var(--accent);"></i>Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama lengkap"
                               required autofocus>
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
                               value="{{ old('email') }}"
                               placeholder="nama@perusahaan.com"
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
                                required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}"
                                        {{ old('role') === $role->value ? 'selected' : '' }}>
                                    {{ $role->value === 'admin' ? 'Administrator' : 'Supervisor' }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="invalid-feedback d-block mt-1" style="font-size:.78rem;color:var(--danger);">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Divider --}}
                    <div style="display:flex;align-items:center;gap:12px;margin:20px 0 18px;">
                        <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
                        <span style="font-size:.72rem;color:var(--text-muted);font-weight:600;letter-spacing:.5px;text-transform:uppercase;">Password</span>
                        <div style="flex:1;height:1px;background:rgba(163,177,198,0.4);"></div>
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock mr-1" style="color:var(--accent);"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter"
                                   required>
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

                    {{-- Password Confirmation --}}
                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock mr-1" style="color:var(--accent);"></i>Konfirmasi Password
                        </label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi password"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="input-group-text"
                                        onclick="togglePw('password_confirmation','pwIcon2')"
                                        style="cursor:pointer;">
                                    <i class="fas fa-eye" id="pwIcon2" style="font-size:.8rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="alert alert-info" style="font-size:.82rem;">
                        <i class="fas fa-info-circle mr-2"></i>
                        Pastikan password minimal <strong>8 karakter</strong> dan mudah diingat oleh pengguna.
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan User
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
