@extends('layout')

@section('title', 'Students')

@section('content')
<div class="d-flex app-shell">
  <aside class="sidebar p-3">
    <div class="text-white fw-semibold mb-3 d-flex align-items-center gap-2"><i data-lucide="school" class="icon"></i>Teacher Console</div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="icon"></i>Dashboard</a>
      <a class="nav-link active" href="{{ route('admin.students') }}"><i data-lucide="users" class="icon"></i>Students</a>
      <a class="nav-link" href="{{ route('admin.evaluation-form') }}"><i data-lucide="clipboard-list" class="icon"></i>Evaluation Form</a>
      <a class="nav-link" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3" class="icon"></i>Reports</a>
    </nav>
  </aside>

  <section class="flex-grow-1 p-4 admin-content">
    <div class="row g-4">
      <div class="col-xl-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white"><h1 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="user-plus" class="icon text-secondary"></i>Add student</h1></div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.students.store') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Block</label>
                <select name="academic_block_id" class="form-select" required>
                  <option value="">Select block</option>
                  @foreach($blocks as $block)
                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Group</label>
                <select name="project_group_id" class="form-select" required>
                  <option value="">Select group</option>
                  @foreach($blocks as $block)
                    @foreach($block->groups as $group)
                      <option value="{{ $group->id }}">{{ $block->name }} - Group {{ $group->number }}</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
              <button class="btn btn-success w-100"><i data-lucide="plus" class="icon"></i>Create account</button>
            </form>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white"><h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="file-up" class="icon text-secondary"></i>Import CSV</h2></div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data">
              @csrf
              <div class="mb-3">
                <label class="form-label">CSV file</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
                <div class="form-text">
                  Reads files with top rows like Course, Subject, Block, then columns Student No., Student Name, and Group No.
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Default block</label>
                <select name="academic_block_id" class="form-select">
                  <option value="">Use CSV block column</option>
                  @foreach($blocks as $block)
                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Default group</label>
                <select name="project_group_id" class="form-select">
                  <option value="">Use CSV group column</option>
                  @foreach($blocks as $block)
                    @foreach($block->groups as $group)
                      <option value="{{ $group->id }}">{{ $block->name }} - Group {{ $group->number }}</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
              <button class="btn btn-outline-success w-100"><i data-lucide="upload" class="icon"></i>Upload students</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="id-card" class="icon text-secondary"></i>Student accounts</h2>
            <span class="text-secondary small">Password defaults to student ID</span>
          </div>
          <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.students') }}" class="row g-2 align-items-center">
              <div class="col">
                <label class="visually-hidden" for="studentSearch">Search students</label>
                <div class="input-group">
                  <span class="input-group-text bg-white"><i data-lucide="search" class="icon text-secondary"></i></span>
                  <input type="search" name="search" id="studentSearch" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, student ID, or email">
                </div>
              </div>
              <div class="col-auto d-flex gap-2">
                <button class="btn btn-success"><i data-lucide="search" class="icon"></i>Search</button>
                @if(!empty($filters['search']))
                  <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary"><i data-lucide="x" class="icon"></i>Clear</a>
                @endif
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Block</th>
                  <th>Group</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($students as $student)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $student->name }}</div>
                      <div class="small text-secondary">{{ $student->student_id }}</div>
                    </td>
                    <td>{{ $student->block?->name ?? 'Unassigned' }}</td>
                    <td>{{ $student->projectGroup ? 'Group ' . $student->projectGroup->number : 'Unassigned' }}</td>
                    <td class="text-end text-nowrap">
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}"><i data-lucide="pencil" class="icon"></i>Edit</button>
                      <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal{{ $student->id }}"><i data-lucide="trash-2" class="icon"></i>Delete</button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-secondary">
                      @if(!empty($filters['search']))
                        No students found for "{{ $filters['search'] }}".
                      @else
                        No students yet.
                      @endif
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="card-footer bg-white">
            {{ $students->links() }}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@foreach($students as $student)
  <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-labelledby="editStudentModal{{ $student->id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <h2 class="modal-title h5 d-flex align-items-center gap-2" id="editStudentModal{{ $student->id }}Label">
            <i data-lucide="pencil" class="icon text-secondary"></i>Edit student
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.students.update', $student) }}" id="editStudentForm{{ $student->id }}">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" class="form-control" value="{{ $student->student_id }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Full name</label>
                <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $student->email }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Block</label>
                <select name="academic_block_id" class="form-select" required>
                  @foreach($blocks as $block)
                    <option value="{{ $block->id }}" @selected($student->academic_block_id === $block->id)>{{ $block->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Group</label>
                <select name="project_group_id" class="form-select" required>
                  @foreach($blocks as $block)
                    @foreach($block->groups as $group)
                      <option value="{{ $group->id }}" @selected($student->project_group_id === $group->id)>{{ $block->name }} - Group {{ $group->number }}</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="reset_password" value="1" id="reset{{ $student->id }}">
                  <label class="form-check-label" for="reset{{ $student->id }}">Reset password to student ID</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success confirm-edit-button" data-form-id="editStudentForm{{ $student->id }}" data-student-name="{{ $student->name }}">
              <i data-lucide="save" class="icon"></i>Save changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteStudentModal{{ $student->id }}" tabindex="-1" aria-labelledby="deleteStudentModal{{ $student->id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <h2 class="modal-title h5 d-flex align-items-center gap-2" id="deleteStudentModal{{ $student->id }}Label">
            <i data-lucide="triangle-alert" class="icon text-danger"></i>Delete student
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-1">Delete <strong>{{ $student->name }}</strong>?</p>
          <p class="text-secondary mb-0">This removes the student account and their submitted or received evaluations.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="mb-0">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger"><i data-lucide="trash-2" class="icon"></i>Delete student</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endforeach

<div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h2 class="modal-title h5 d-flex align-items-center gap-2" id="confirmEditModalLabel">
          <i data-lucide="triangle-alert" class="icon text-warning"></i>Confirm changes
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="confirmEditMessage" class="mb-0">Save these student changes?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submitConfirmedEdit"><i data-lucide="save" class="icon"></i>Yes, save changes</button>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="modal fade" id="adminStudentsSuccessModal" tabindex="-1" aria-labelledby="adminStudentsSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <h2 class="modal-title h5 d-flex align-items-center gap-2" id="adminStudentsSuccessModalLabel">
            <i data-lucide="circle-check" class="icon text-success"></i>Success
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {{ session('success') }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Continue</button>
        </div>
      </div>
    </div>
  </div>
@endif

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    let pendingEditForm = null;
    const confirmEditModalElement = document.getElementById('confirmEditModal');
    const confirmEditModal = confirmEditModalElement ? new bootstrap.Modal(confirmEditModalElement) : null;
    const confirmEditMessage = document.getElementById('confirmEditMessage');
    const submitConfirmedEdit = document.getElementById('submitConfirmedEdit');

    document.querySelectorAll('.confirm-edit-button').forEach(function (button) {
      button.addEventListener('click', function () {
        pendingEditForm = document.getElementById(button.dataset.formId);

        if (pendingEditForm && !pendingEditForm.checkValidity()) {
          pendingEditForm.reportValidity();
          return;
        }

        if (confirmEditMessage) {
          confirmEditMessage.textContent = 'Save changes for ' + button.dataset.studentName + '?';
        }

        const editModalElement = button.closest('.modal');
        if (editModalElement) {
          editModalElement.addEventListener('hidden.bs.modal', function () {
            if (confirmEditModal) {
              confirmEditModal.show();
            }
          }, { once: true });
          bootstrap.Modal.getOrCreateInstance(editModalElement).hide();
          return;
        }

        if (confirmEditModal) {
          confirmEditModal.show();
        }
      });
    });

    if (submitConfirmedEdit) {
      submitConfirmedEdit.addEventListener('click', function () {
        if (pendingEditForm) {
          pendingEditForm.submit();
        }
      });
    }

    const successModalElement = document.getElementById('adminStudentsSuccessModal');
    if (successModalElement) {
      new bootstrap.Modal(successModalElement).show();
    }
  });
</script>
@endpush
@endsection
