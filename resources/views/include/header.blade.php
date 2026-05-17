<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand fw-semibold d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
      <i data-lucide="graduation-cap" class="icon"></i>
      <span>Peer Evaluation</span>
    </a>

    @auth
      @if(auth()->user()->role === 'student')
        <button class="top-profile-button ms-auto" type="button" data-bs-toggle="modal" data-bs-target="#studentProfileModal" aria-label="Open profile settings">
          <span class="top-profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="top-profile-meta">
            <span class="fw-semibold small">{{ auth()->user()->name }}</span>
            <span class="text-secondary small">Student profile</span>
          </span>
          <i data-lucide="settings" class="icon"></i>
        </button>
      @elseif(auth()->user()->role === 'admin')
        <button class="top-profile-button ms-auto" type="button" data-bs-toggle="modal" data-bs-target="#adminProfileModal" aria-label="Open profile settings">
          <span class="top-profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="top-profile-meta">
            <span class="fw-semibold small">{{ auth()->user()->name }}</span>
            <span class="text-secondary small">Admin profile</span>
          </span>
          <i data-lucide="settings" class="icon"></i>
        </button>
      @else
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      @endif
    @else
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    @endauth

    @unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'student'], true))
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto">
          @auth
          @else
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}"><i data-lucide="log-in" class="icon"></i>Sign in</a>
            </li>
          @endauth
        </ul>
      </div>
    @endunless
  </div>
</nav>

@auth
  @if(auth()->user()->role === 'admin')
    <div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <div class="d-flex align-items-center gap-3">
              <span class="admin-profile-avatar-lg">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
              <div>
                <h2 class="modal-title h5 mb-1" id="adminProfileModalLabel">Profile settings</h2>
                <div class="text-secondary small">{{ auth()->user()->email }}</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if(session('success') && session('profile_modal'))
              <div class="alert alert-success d-flex align-items-center gap-2">
                <i data-lucide="circle-check" class="icon"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            <div class="admin-profile-hero mb-3">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                  <div class="fw-semibold">{{ auth()->user()->name }}</div>
                  <div class="text-secondary small">Manage your account information and password.</div>
                </div>
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary"><i data-lucide="log-out" class="icon"></i>Sign out</a>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-lg-6">
                <div class="admin-profile-card">
                  <h3 class="h6 d-flex align-items-center gap-2 mb-3"><i data-lucide="user-round" class="icon text-secondary"></i>Account details</h3>
                  <form method="POST" action="{{ route('admin.profile.update') }}" id="adminProfileForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label">Full name</label>
                      <input type="text" name="name" class="form-control @error('name', 'profile') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                      @error('name', 'profile')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control @error('email', 'profile') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                      @error('email', 'profile')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <button type="button" class="btn btn-success w-100 profile-confirm-button" data-form-id="adminProfileForm" data-origin-modal="#adminProfileModal" data-title="Save profile changes" data-message="Save the changes to your admin profile?"><i data-lucide="save" class="icon"></i>Save profile</button>
                  </form>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="admin-profile-card">
                  <h3 class="h6 d-flex align-items-center gap-2 mb-3"><i data-lucide="lock-keyhole" class="icon text-secondary"></i>Change password</h3>
                  <form method="POST" action="{{ route('admin.password.update') }}" id="adminPasswordForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label">Current password</label>
                      <input type="password" name="current_password" class="form-control @error('current_password', 'password') is-invalid @enderror" required>
                      @error('current_password', 'password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">New password</label>
                      <input type="password" name="password" class="form-control @error('password', 'password') is-invalid @enderror" minlength="8" required>
                      @error('password', 'password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Confirm new password</label>
                      <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                    </div>
                    <button type="button" class="btn btn-success w-100 profile-confirm-button" data-form-id="adminPasswordForm" data-origin-modal="#adminProfileModal" data-title="Change password" data-message="Change your admin account password?"><i data-lucide="key-round" class="icon"></i>Update password</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const shouldOpenProfile = @json(session('profile_modal') || $errors->getBag('profile')->any() || $errors->getBag('password')->any());
        const profileModalElement = document.getElementById('adminProfileModal');

        if (shouldOpenProfile && profileModalElement) {
          bootstrap.Modal.getOrCreateInstance(profileModalElement).show();
        }
      });
    </script>
    @endpush
  @endif

  @if(auth()->user()->role === 'student')
    <div class="modal fade" id="studentProfileModal" tabindex="-1" aria-labelledby="studentProfileModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <div class="d-flex align-items-center gap-3">
              <span class="admin-profile-avatar-lg">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
              <div>
                <h2 class="modal-title h5 mb-1" id="studentProfileModalLabel">Profile settings</h2>
                <div class="text-secondary small">{{ auth()->user()->student_id }}</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if(session('success') && session('student_profile_modal'))
              <div class="alert alert-success d-flex align-items-center gap-2">
                <i data-lucide="circle-check" class="icon"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            <div class="admin-profile-hero mb-3">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                  <div class="fw-semibold">{{ auth()->user()->name }}</div>
                  <div class="text-secondary small">
                    {{ auth()->user()->block?->name ?? 'No block' }}
                    @if(auth()->user()->projectGroup)
                      - Group {{ auth()->user()->projectGroup->number }}
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                  <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary"><i data-lucide="clipboard-check" class="icon"></i>Evaluation</a>
                  <a href="{{ route('logout') }}" class="btn btn-outline-secondary"><i data-lucide="log-out" class="icon"></i>Sign out</a>
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-lg-6">
                <div class="admin-profile-card">
                  <h3 class="h6 d-flex align-items-center gap-2 mb-3"><i data-lucide="id-card" class="icon text-secondary"></i>Student details</h3>
                  <form method="POST" action="{{ route('student.profile.update') }}" id="studentProfileForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label">Student ID</label>
                      <input type="text" class="form-control" value="{{ auth()->user()->student_id }}" disabled>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Full name</label>
                      <input type="text" name="name" class="form-control @error('name', 'studentProfile') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                      @error('name', 'studentProfile')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control @error('email', 'studentProfile') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                      @error('email', 'studentProfile')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <button type="button" class="btn btn-success w-100 profile-confirm-button" data-form-id="studentProfileForm" data-origin-modal="#studentProfileModal" data-title="Save profile changes" data-message="Save the changes to your student profile?"><i data-lucide="save" class="icon"></i>Save profile</button>
                  </form>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="admin-profile-card">
                  <h3 class="h6 d-flex align-items-center gap-2 mb-3"><i data-lucide="lock-keyhole" class="icon text-secondary"></i>Change password</h3>
                  <form method="POST" action="{{ route('student.password.update') }}" id="studentPasswordForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label">Current password</label>
                      <input type="password" name="current_password" class="form-control @error('current_password', 'studentPassword') is-invalid @enderror" required>
                      @error('current_password', 'studentPassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">New password</label>
                      <input type="password" name="password" class="form-control @error('password', 'studentPassword') is-invalid @enderror" minlength="8" required>
                      @error('password', 'studentPassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Confirm new password</label>
                      <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                    </div>
                    <button type="button" class="btn btn-success w-100 profile-confirm-button" data-form-id="studentPasswordForm" data-origin-modal="#studentProfileModal" data-title="Change password" data-message="Change your student account password?"><i data-lucide="key-round" class="icon"></i>Update password</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const shouldOpenStudentProfile = @json(session('student_profile_modal') || $errors->getBag('studentProfile')->any() || $errors->getBag('studentPassword')->any());
        const profileModalElement = document.getElementById('studentProfileModal');

        if (shouldOpenStudentProfile && profileModalElement) {
          bootstrap.Modal.getOrCreateInstance(profileModalElement).show();
        }
      });
    </script>
    @endpush
  @endif

  @if(in_array(auth()->user()->role, ['admin', 'student'], true))
    <div class="modal fade" id="profileConfirmModal" tabindex="-1" aria-labelledby="profileConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <h2 class="modal-title h5 d-flex align-items-center gap-2" id="profileConfirmModalLabel">
              <i data-lucide="triangle-alert" class="icon text-warning"></i>Confirm action
            </h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0" id="profileConfirmMessage">Continue with this change?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="profileConfirmSubmit"><i data-lucide="check" class="icon"></i>Yes, continue</button>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        let pendingProfileForm = null;
        let pendingOriginModal = null;
        let isSubmittingProfileForm = false;
        const confirmModalElement = document.getElementById('profileConfirmModal');
        const confirmModal = confirmModalElement ? bootstrap.Modal.getOrCreateInstance(confirmModalElement) : null;
        const confirmTitle = document.getElementById('profileConfirmModalLabel');
        const confirmMessage = document.getElementById('profileConfirmMessage');
        const confirmSubmit = document.getElementById('profileConfirmSubmit');

        document.querySelectorAll('.profile-confirm-button').forEach(function (button) {
          button.addEventListener('click', function () {
            pendingProfileForm = document.getElementById(button.dataset.formId);
            pendingOriginModal = button.dataset.originModal ? document.querySelector(button.dataset.originModal) : null;
            isSubmittingProfileForm = false;

            if (pendingProfileForm && !pendingProfileForm.checkValidity()) {
              pendingProfileForm.reportValidity();
              return;
            }

            if (confirmTitle) {
              confirmTitle.innerHTML = '<i data-lucide="triangle-alert" class="icon text-warning"></i>' + button.dataset.title;
            }

            if (confirmMessage) {
              confirmMessage.textContent = button.dataset.message || 'Continue with this change?';
            }

            if (pendingOriginModal) {
              pendingOriginModal.addEventListener('hidden.bs.modal', function () {
                if (confirmModal) {
                  confirmModal.show();
                }
              }, { once: true });
              bootstrap.Modal.getOrCreateInstance(pendingOriginModal).hide();
            } else if (confirmModal) {
              confirmModal.show();
            }

            if (window.lucide) {
              lucide.createIcons();
            }
          });
        });

        if (confirmSubmit) {
          confirmSubmit.addEventListener('click', function () {
            if (pendingProfileForm) {
              isSubmittingProfileForm = true;
              pendingProfileForm.submit();
            }
          });
        }

        if (confirmModalElement) {
          confirmModalElement.addEventListener('hidden.bs.modal', function () {
            if (!isSubmittingProfileForm && pendingOriginModal) {
              bootstrap.Modal.getOrCreateInstance(pendingOriginModal).show();
            }
          });
        }
      });
    </script>
    @endpush
  @endif
@endauth
