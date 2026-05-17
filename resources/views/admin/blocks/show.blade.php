@extends('layout')

@section('title', $block->name)

@section('content')
<div class="d-flex app-shell">
  <aside class="sidebar p-3">
    <div class="text-white fw-semibold mb-3 d-flex align-items-center gap-2"><i data-lucide="school" class="icon"></i>Teacher Console</div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="icon"></i>Dashboard</a>
      <a class="nav-link" href="{{ route('admin.students') }}"><i data-lucide="users" class="icon"></i>Students</a>
      <a class="nav-link" href="{{ route('admin.evaluation-form') }}"><i data-lucide="clipboard-list" class="icon"></i>Evaluation Form</a>
      <a class="nav-link" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3" class="icon"></i>Reports</a>
    </nav>
  </aside>

  <section class="flex-grow-1 p-4 admin-content">
    @php
      $allMembers = $block->groups->flatMap->students;
      $requiredTotal = $allMembers->sum(fn ($student) => max(($student->projectGroup?->students->count() ?? 1) - 1, 0));
      $submittedTotal = $allMembers->sum(fn ($student) => $student->evaluationsGiven->count());
      $completionPercent = $requiredTotal > 0 ? round(($submittedTotal / $requiredTotal) * 100) : 0;
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <h1 class="h3 mb-1 page-title">{{ $block->name }}</h1>
        <div class="text-secondary">Groups, members, and evaluation status.</div>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i data-lucide="arrow-left" class="icon"></i>Back to blocks</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
          <div>
            <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i data-lucide="activity" class="icon text-secondary"></i>Block progress</h2>
            <div class="text-secondary small">{{ $submittedTotal }} of {{ $requiredTotal }} required evaluations submitted</div>
          </div>
          <span class="badge text-bg-light">{{ $completionPercent }}% complete</span>
        </div>
        <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar" style="width: {{ $completionPercent }}%"></div>
        </div>
        <div class="block-stat-grid">
          <div class="block-stat">
            <div class="block-stat-value">{{ $block->groups->count() }}</div>
            <div class="small text-secondary">Groups</div>
          </div>
          <div class="block-stat">
            <div class="block-stat-value">{{ $allMembers->count() }}</div>
            <div class="small text-secondary">Students</div>
          </div>
          <div class="block-stat">
            <div class="block-stat-value">{{ max($requiredTotal - $submittedTotal, 0) }}</div>
            <div class="small text-secondary">Remaining</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      @forelse($block->groups as $group)
        @php
          $members = $group->students;
          $memberIds = $members->pluck('id');
          $requiredPerMember = max($members->count() - 1, 0);
          $groupSubmitted = $members->sum(function ($member) use ($memberIds) {
              return $member->evaluationsGiven
                  ->whereIn('evaluatee_id', $memberIds)
                  ->reject(fn ($evaluation) => (int) $evaluation->evaluatee_id === (int) $member->id)
                  ->count();
          });
          $groupRequired = $members->count() * $requiredPerMember;
          $groupCompletion = $groupRequired > 0 ? round(($groupSubmitted / $groupRequired) * 100) : 0;
        @endphp
        <div class="col-xl-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                  <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i data-lucide="users-round" class="icon text-secondary"></i>Group {{ $group->number }}</h2>
                  <div class="text-secondary small">{{ $groupSubmitted }} of {{ $groupRequired }} evaluations submitted</div>
                </div>
                <span class="badge text-bg-light">{{ $groupCompletion }}%</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="{{ $groupCompletion }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $groupCompletion }}%"></div>
              </div>
            </div>
            <div class="group-summary">
              <div class="group-summary-item">
                <div class="group-summary-value">{{ $members->count() }}</div>
                <div class="small text-secondary">Members</div>
              </div>
              <div class="group-summary-item">
                <div class="group-summary-value">{{ $requiredPerMember }}</div>
                <div class="small text-secondary">Required each</div>
              </div>
              <div class="group-summary-item">
                <div class="group-summary-value">{{ max($groupRequired - $groupSubmitted, 0) }}</div>
                <div class="small text-secondary">Pending</div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Student</th>
                    <th class="text-center">Submitted</th>
                    <th>Status</th>
                    <th class="text-end"></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($members as $member)
                    @php
                      $teamEvaluations = $member->evaluationsGiven
                          ->whereIn('evaluatee_id', $memberIds)
                          ->reject(fn ($evaluation) => (int) $evaluation->evaluatee_id === (int) $member->id)
                          ->values();
                      $submitted = $teamEvaluations->count();
                      $done = $requiredPerMember > 0 && $submitted >= $requiredPerMember;
                    @endphp
                    <tr class="member-row" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#memberEvaluationModal{{ $member->id }}">
                      <td>
                        <div class="fw-semibold">{{ $member->name }}</div>
                        <div class="small text-secondary">{{ $member->student_id }}</div>
                      </td>
                      <td class="text-center">
                        <span class="badge rounded-pill {{ $done ? 'text-bg-success' : 'text-bg-light' }}">{{ $submitted }} / {{ $requiredPerMember }}</span>
                      </td>
                      <td>
                        <span class="badge {{ $done ? 'text-bg-success' : 'text-bg-warning' }}">
                          {{ $done ? 'Complete' : 'Pending' }}
                        </span>
                      </td>
                      <td class="text-end"><i data-lucide="chevron-right" class="icon row-action-icon"></i></td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center py-4">
                        <div class="icon-pill mx-auto mb-3"><i data-lucide="user-x" class="icon"></i></div>
                        <div class="fw-semibold">No students assigned yet</div>
                        <div class="text-secondary small">Add or import students to populate this group.</div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
              <div class="icon-pill mx-auto mb-3"><i data-lucide="users-round" class="icon"></i></div>
              <div class="fw-semibold">No groups in this block yet</div>
              <div class="text-secondary small">Groups will appear here once they are created.</div>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </section>
</div>

@foreach($block->groups as $group)
  @php
    $members = $group->students;
    $memberIds = $members->pluck('id');
    $requiredPerMember = max($members->count() - 1, 0);
  @endphp
  @foreach($members as $member)
    @php
      $teamEvaluations = $member->evaluationsGiven
          ->whereIn('evaluatee_id', $memberIds)
          ->reject(fn ($evaluation) => (int) $evaluation->evaluatee_id === (int) $member->id)
          ->values();
      $givenMean = $teamEvaluations->count() ? round($teamEvaluations->avg(fn ($evaluation) => $evaluation->average_score), 2) : null;
      $givenMeanClass = $givenMean === null ? 'report-score-empty' : ($givenMean >= 4 ? 'report-score-strong' : ($givenMean >= 3 ? 'report-score-ok' : 'report-score-low'));
      $pendingMembers = $members->reject(fn ($teammate) => (int) $teammate->id === (int) $member->id)
          ->reject(fn ($teammate) => $teamEvaluations->contains('evaluatee_id', $teammate->id));
    @endphp
    <div class="modal fade" id="memberEvaluationModal{{ $member->id }}" tabindex="-1" aria-labelledby="memberEvaluationModal{{ $member->id }}Label" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <div>
              <h2 class="modal-title h5 d-flex align-items-center gap-2" id="memberEvaluationModal{{ $member->id }}Label">
                <i data-lucide="clipboard-check" class="icon text-secondary"></i>{{ $member->name }}
              </h2>
              <div class="text-secondary small">{{ $block->name }} - Group {{ $group->number }} - {{ $member->student_id }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mean-panel mb-3">
              <div>
                <div class="fw-semibold">Total mean rating given to team</div>
                <div class="text-secondary small">{{ $teamEvaluations->count() }} of {{ $requiredPerMember }} teammate(s) evaluated</div>
              </div>
              <span class="report-score {{ $givenMeanClass }}">
                {{ $givenMean !== null ? number_format($givenMean, 2) : '-' }}
              </span>
            </div>

            <h3 class="h6 mb-3 d-flex align-items-center gap-2"><i data-lucide="check-circle-2" class="icon text-secondary"></i>Already evaluated</h3>
            <div class="evaluation-member-list mb-4">
              @forelse($teamEvaluations as $evaluation)
                <div class="evaluation-member-item">
                  <div>
                    <div class="fw-semibold">{{ $evaluation->evaluatee?->name ?? 'Student removed' }}</div>
                    <div class="small text-secondary">
                      {{ $evaluation->evaluatee?->student_id ?? 'Unavailable' }}
                      @if($evaluation->created_at)
                        - Submitted {{ $evaluation->created_at->format('M d, Y g:i A') }}
                      @endif
                    </div>
                  </div>
                  @php($evaluationAverage = $evaluation->average_score)
                  <span class="report-score {{ $evaluationAverage >= 4 ? 'report-score-strong' : ($evaluationAverage >= 3 ? 'report-score-ok' : 'report-score-low') }}">{{ number_format($evaluationAverage, 2) }}</span>
                </div>
              @empty
                <div class="text-secondary small">This student has not evaluated any teammate yet.</div>
              @endforelse
            </div>

            @if($pendingMembers->count() > 0)
              <h3 class="h6 mb-3 d-flex align-items-center gap-2"><i data-lucide="clock-3" class="icon text-secondary"></i>Still pending</h3>
              <div class="d-flex flex-wrap gap-2">
                @foreach($pendingMembers as $pendingMember)
                  <span class="badge text-bg-light">{{ $pendingMember->name }}</span>
                @endforeach
              </div>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Done</button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endforeach

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.member-row').forEach(function (row) {
      row.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') {
          return;
        }

        event.preventDefault();
        const target = row.getAttribute('data-bs-target');
        const modalElement = target ? document.querySelector(target) : null;

        if (modalElement) {
          bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
      });
    });
  });
</script>
@endpush
@endsection
