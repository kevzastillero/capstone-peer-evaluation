@extends('layout')

@section('title', 'Reports')

@section('content')
<div class="d-flex app-shell">
  <aside class="sidebar p-3">
    <div class="text-white fw-semibold mb-3 d-flex align-items-center gap-2"><i data-lucide="school" class="icon"></i>Teacher Console</div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="icon"></i>Dashboard</a>
      <a class="nav-link" href="{{ route('admin.students') }}"><i data-lucide="users" class="icon"></i>Students</a>
      <a class="nav-link" href="{{ route('admin.evaluation-form') }}"><i data-lucide="clipboard-list" class="icon"></i>Evaluation Form</a>
      <a class="nav-link active" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3" class="icon"></i>Reports</a>
    </nav>
  </aside>

  <section class="flex-grow-1 p-4 admin-content">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <h1 class="h3 mb-1 page-title">Evaluation report</h1>
        <div class="text-secondary">Averages are based on evaluations received from group members.</div>
      </div>
      <a href="{{ route('admin.reports.export', request()->only('block_id', 'search')) }}" class="btn btn-success"><i data-lucide="download" class="icon"></i>Export CSV for Excel</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Block</label>
            <select name="block_id" class="form-select">
              <option value="">All blocks</option>
              @foreach($blocks as $block)
                <option value="{{ $block->id }}" @selected(($filters['block_id'] ?? '') == $block->id)>{{ $block->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Search student</label>
            <input type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Student name or ID">
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-success flex-fill"><i data-lucide="filter" class="icon"></i>Filter</button>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary flex-fill"><i data-lucide="x" class="icon"></i>Clear</a>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i data-lucide="table-2" class="icon text-secondary"></i>Results</h2>
          <div class="text-secondary small">Scores use a 1 to 5 scale. Scroll sideways to view every question.</div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <span class="badge text-bg-light">{{ $results->count() }} student(s)</span>
          <span class="report-score report-score-strong">4.00+</span>
          <span class="report-score report-score-ok">3.00+</span>
          <span class="report-score report-score-low">&lt;3.00</span>
        </div>
      </div>
      <div class="table-responsive report-table-wrap">
        @php
          $scoreClass = function ($score) {
              if ($score === null) {
                  return 'report-score-empty';
              }

              if ($score >= 4) {
                  return 'report-score-strong';
              }

              if ($score >= 3) {
                  return 'report-score-ok';
              }

              return 'report-score-low';
          };
        @endphp
        <table class="table table-hover report-table mb-0">
          <caption class="visually-hidden">Evaluation averages by student</caption>
          <thead>
            <tr>
              <th class="report-student-col">Student</th>
              <th>Class / group</th>
              <th class="text-center">Received</th>
              <th>Overall</th>
              @foreach($questions as $question)
                <th class="text-center"><span class="report-question-heading">{{ $question->question }}</span></th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @forelse($results as $student)
              <tr @class(['report-row-empty' => $student->evaluations_received_count === 0])>
                <td class="report-student-col">
                  <div class="fw-semibold">{{ $student->name }}</div>
                  <div class="small text-secondary d-flex align-items-center gap-1">
                    <i data-lucide="id-card" class="icon"></i>{{ $student->student_id }}
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-light">{{ $student->block?->name ?? 'No block' }}</span>
                    <span class="badge text-bg-light">{{ $student->projectGroup ? 'Group ' . $student->projectGroup->number : 'No group' }}</span>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge rounded-pill {{ $student->evaluations_received_count > 0 ? 'text-bg-success' : 'text-bg-light' }}">
                    {{ $student->evaluations_received_count }}
                  </span>
                </td>
                <td>
                  @php($overall = $student->overall_average)
                  <div class="d-flex align-items-center gap-2">
                    <span class="report-score {{ $scoreClass($overall) }}">{{ $overall ? number_format($overall, 2) : '-' }}</span>
                    <div class="report-overall-bar" aria-hidden="true">
                      <span style="width: {{ $overall ? min(100, ($overall / 5) * 100) : 0 }}%"></span>
                    </div>
                  </div>
                </td>
                @foreach($questions as $question)
                  @php($questionAverage = $student->question_averages[$question->id] ?? null)
                  <td class="text-center">
                    <span class="report-score {{ $scoreClass($questionAverage) }}">
                      {{ $questionAverage !== null ? number_format($questionAverage, 2) : '-' }}
                    </span>
                  </td>
                @endforeach
              </tr>
            @empty
              <tr>
                <td colspan="{{ 4 + $questions->count() }}" class="text-center py-5">
                  <div class="icon-pill mx-auto mb-3"><i data-lucide="search-x" class="icon"></i></div>
                  <div class="fw-semibold">No report data found</div>
                  <div class="text-secondary small">Try changing the block or student search filter.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
@endsection
