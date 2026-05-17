@extends('layout')

@section('title', 'Sign in')

@section('content')
<div class="container auth-page">
  <div class="row justify-content-center w-100 mx-0">
    <div class="col-lg-9 col-xl-8 px-0 px-sm-2">
      <div class="card auth-card border-0 shadow-sm">
        <div class="row g-0">
          <div class="col-lg-5">
            <div class="auth-aside d-flex flex-column justify-content-between">
              <div>
                <span class="auth-badge mb-4"><i data-lucide="shield-check" class="icon"></i>Secure access</span>
                <h1 class="h3 fw-semibold mb-3">Capstone Peer Evaluation</h1>
                <p class="text-secondary mb-0">Sign in to continue to your assigned evaluation workspace.</p>
              </div>

              <div class="d-grid gap-3 mt-4">
                <div class="d-flex align-items-start gap-3">
                  <i data-lucide="users-round" class="icon mt-1"></i>
                  <div>
                    <div class="fw-semibold">Students</div>
                    <div class="small text-secondary">Use your student ID as username and password.</div>
                  </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                  <i data-lucide="school" class="icon mt-1"></i>
                  <div>
                    <div class="fw-semibold">Teacher</div>
                    <div class="small text-secondary">Use the admin email and password.</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="auth-form-panel">
              <div class="mb-4">
                <h2 class="h4 mb-1 d-flex align-items-center gap-2"><i data-lucide="log-in" class="icon text-secondary"></i>Sign in</h2>
                <div class="text-secondary">Enter your account credentials.</div>
              </div>

              <div class="auth-role-row mb-4">
                <div class="auth-role">
                  <div class="fw-semibold d-flex align-items-center gap-2"><i data-lucide="id-card" class="icon"></i>Student</div>
                  <div class="small text-secondary mt-1">Student ID</div>
                </div>
                <div class="auth-role">
                  <div class="fw-semibold d-flex align-items-center gap-2"><i data-lucide="mail" class="icon"></i>Teacher</div>
                  <div class="small text-secondary mt-1">Email address</div>
                </div>
              </div>

              <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Student ID or email</label>
                  <input type="text" class="form-control auth-input" name="login" value="{{ old('login') }}" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" class="form-control auth-input" name="password" required autocomplete="current-password">
                </div>

                <div class="auth-helper mb-4">
                  <i data-lucide="info" class="icon"></i>
                  <span>Student accounts use the student ID as the initial password.</span>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100"><i data-lucide="arrow-right" class="icon"></i>Sign in</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
