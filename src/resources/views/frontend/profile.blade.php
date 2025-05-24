@extends('layouts.index')
@section('content')

<section class="py-5">
    <div class="container">
      <div class="card shadow rounded-4 p-4 mx-auto" style="max-width: 500px;">
        <div class="text-center mb-4">
          @php
            // Tentukan URL avatar: profil.image > user avatar > default
            $avatarUrl = null;
            if (isset($profile->image) && $profile->image) {
                $avatarUrl = asset('storage/' . $profile->image);
            } else {
                $avatarUrl = asset('front/assets/user.png');
            }
          @endphp
          <img 
            src="{{ $avatarUrl }}"
            class="rounded-circle shadow"  
            width="100" height="100" alt="Avatar"
          >
          <h6 class="mt-2 fw-bold" id="profilNama">{{ $profile->nama_lengkap ?? $user->name }}</h6>
          <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            Edit Profil
          </button>
        </div>

        <ul class="list-group list-group-flush small">
          <li class="list-group-item d-flex justify-content-between">
            <span>Email</span>
            <span class="fw-semibold text-end">{{ $user->email }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Nomor Telepon</span>
            <span class="fw-semibold text-end">{{ $profile->nomor_telepon ?? '-' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Tanggal Lahir</span>
            <span class="fw-semibold text-end">{{ $profile->tanggal_lahir ?? '-' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Jenis Kelamin</span>
            <span class="fw-semibold text-end">{{ $profile->jenis_kelamin ?? '-' }}</span>
          </li>
        </ul>
        <div class="mt-4 text-center">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger rounded-pill w-100">Keluar Akun</button>
          </form>
        </div>
      </div>
    </div>
</section>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3 text-center">
            <img src="{{ $avatarUrl }}" class="rounded-circle mb-3" width="80" height="80" alt="Avatar">
            <label for="image" class="form-label small">Ganti Foto</label>
            <input type="file" name="image" id="image" class="form-control form-control-sm @error('image') is-invalid @enderror">
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $profile->nama_lengkap ?? $user->name) }}" class="form-control @error('nama_lengkap') is-invalid @enderror">
            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $profile->nomor_telepon) }}" class="form-control @error('nomor_telepon') is-invalid @enderror">
            @error('nomor_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir) }}" class="form-control @error('tanggal_lahir') is-invalid @enderror">
            @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
              <option value="">Pilih…</option>
              <option value="Laki-laki" {{ old('jenis_kelamin', $profile->jenis_kelamin)=='Laki-laki'?'selected':'' }}>Laki-laki</option>
              <option value="Perempuan"   {{ old('jenis_kelamin', $profile->jenis_kelamin)=='Perempuan'?'selected':'' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
