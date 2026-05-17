@extends('layout')

@section('title', 'Evaluation')

@section('content')
@php
  $totalMembers = $members->count();
  $completedMembers = $members->where('already_evaluated', true)->count();
  $remainingMembers = max($totalMembers - $completedMembers, 0);
  $completionPercent = $totalMembers > 0 ? round(($completedMembers / $totalMembers) * 100) : 0;
@endphp

<div class="container student-page py-4">
  <div class="card border-0 shadow-sm student-hero mb-4">
    <div class="card-body p-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="d-flex align-items-center gap-3">
            <span class="member-avatar bg-white text-dark">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            <div>
              <h1 class="h3 mb-1">{{ $student->name }}</h1>
              <div class="text-secondary">{{ $student->student_id }}</div>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="student-chip"><i data-lucide="blocks" class="icon"></i>{{ $student->block?->name ?? 'Unassigned' }}</span>
            <span class="student-chip"><i data-lucide="users-round" class="icon"></i>{{ $student->projectGroup ? 'Group ' . $student->projectGroup->number : 'Unassigned' }}</span>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="d-flex justify-content-between mb-2">
            <span class="small">Evaluation progress</span>
            <strong>{{ $completionPercent }}%</strong>
          </div>
          <div class="progress" role="progressbar" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width: {{ $completionPercent }}%"></div>
          </div>
          <div class="small mt-2">{{ $completedMembers }} of {{ $totalMembers }} team member(s) evaluated</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="student-stat">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="value">{{ $totalMembers }}</div>
            <div class="text-secondary">Team members</div>
          </div>
          <span class="icon-pill"><i data-lucide="users" class="icon"></i></span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="student-stat">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="value">{{ $completedMembers }}</div>
            <div class="text-secondary">Completed</div>
          </div>
          <span class="icon-pill"><i data-lucide="check-check" class="icon"></i></span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="student-stat">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="value">{{ $remainingMembers }}</div>
            <div class="text-secondary">Pending</div>
          </div>
          <span class="icon-pill"><i data-lucide="clock-3" class="icon"></i></span>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
      <h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="clipboard-check" class="icon text-secondary"></i>Team members</h2>
      <span class="badge text-bg-light">{{ $completionPercent }}% complete</span>
    </div>
    <div class="card-body">
      <div class="d-grid gap-3">
        @forelse($members as $member)
          <div class="member-card">
            <div class="d-flex align-items-center gap-3">
              <span class="member-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
              <div>
                <div class="fw-semibold">{{ $member->name }}</div>
                <div class="small text-secondary">{{ $member->student_id }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge {{ $member->already_evaluated ? 'text-bg-success' : 'text-bg-warning' }}">
                {{ $member->already_evaluated ? 'Evaluated' : 'Pending' }}
              </span>
              @if($member->already_evaluated)
                <button class="btn btn-sm btn-outline-secondary" disabled><i data-lucide="check" class="icon"></i>Done</button>
              @else
                <a class="btn btn-sm btn-success" href="{{ route('student.evaluations.create', $member) }}"><i data-lucide="clipboard-pen" class="icon"></i>Evaluate</a>
              @endif
            </div>
          </div>
        @empty
          <div class="text-secondary">You are not assigned to a group yet.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@if(session('evaluation_success'))
  <div class="modal fade" id="evaluationSuccessModal" tabindex="-1" aria-labelledby="evaluationSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <h2 class="modal-title h5 d-flex align-items-center gap-2" id="evaluationSuccessModalLabel">
            <i data-lucide="circle-check" class="icon text-success"></i>Evaluation submitted
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0">{{ session('evaluation_success') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Continue</button>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection

@if(session('evaluation_success'))
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modalElement = document.getElementById('evaluationSuccessModal');
      const successModal = new bootstrap.Modal(modalElement);
      successModal.show();
    });
  </script>
  @endpush
@endif
