@extends('layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex app-shell">
  <aside class="sidebar p-3">
    <div class="text-white fw-semibold mb-3 d-flex align-items-center gap-2"><i data-lucide="school" class="icon"></i>Teacher Console</div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link active" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="icon"></i>Dashboard</a>
      <a class="nav-link" href="{{ route('admin.students') }}"><i data-lucide="users" class="icon"></i>Students</a>
      <a class="nav-link" href="{{ route('admin.evaluation-form') }}"><i data-lucide="clipboard-list" class="icon"></i>Evaluation Form</a>
      <a class="nav-link" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3" class="icon"></i>Reports</a>
    </nav>
  </aside>

  <section class="flex-grow-1 p-4 admin-content">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <h1 class="h3 mb-1 page-title">Blocks</h1>
        <div class="text-secondary">Monitor group membership and evaluation completion.</div>
      </div>
      <a href="{{ route('admin.students') }}" class="btn btn-success"><i data-lucide="user-plus" class="icon"></i>Manage students</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i data-lucide="bar-chart-3" class="icon text-secondary"></i>Completed evaluation summary</h2>
          <div class="text-secondary small">Compare submitted evaluations against the required team evaluations.</div>
        </div>
        @php
          $chartCompleted = $chartRows->sum('completed');
          $chartRequired = $chartRows->sum('required');
          $chartPercent = $chartRequired > 0 ? min(100, round(($chartCompleted / $chartRequired) * 100)) : 0;
        @endphp
        <span class="badge text-bg-light">{{ $chartPercent }}% complete</span>
      </div>
      <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Graph view</label>
            <select name="summary_by" class="form-select">
              <option value="block" @selected(($chartFilters['summary_by'] ?? 'block') === 'block')>By block</option>
              <option value="group" @selected(($chartFilters['summary_by'] ?? '') === 'group')>By group</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Block</label>
            <select name="block_id" class="form-select">
              <option value="">All blocks</option>
              @foreach($chartBlocks as $block)
                <option value="{{ $block->id }}" @selected(($chartFilters['block_id'] ?? '') == $block->id)>{{ $block->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Group</label>
            <select name="project_group_id" class="form-select">
              <option value="">All groups</option>
              @foreach($chartBlocks as $block)
                @if($block->groups->count())
                  <optgroup label="{{ $block->name }}">
                    @foreach($block->groups as $group)
                      <option value="{{ $group->id }}" @selected(($chartFilters['project_group_id'] ?? '') == $group->id)>Group {{ $group->number }}</option>
                    @endforeach
                  </optgroup>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-success flex-fill"><i data-lucide="filter" class="icon"></i>Apply</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary flex-fill"><i data-lucide="x" class="icon"></i>Clear</a>
          </div>
        </form>
      </div>
      <div class="card-body">
        @if($chartRows->count() > 0)
          <div class="completion-chart">
            @foreach($chartRows as $row)
              <div class="completion-chart-row">
                <div class="completion-chart-label">
                  <div class="fw-semibold" title="{{ $row['label'] }}">{{ $row['label'] }}</div>
                  <div class="small text-secondary">{{ $row['students'] }} student(s), {{ $row['remaining'] }} remaining</div>
                </div>
                <div class="completion-chart-track" role="progressbar" aria-valuenow="{{ $row['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $row['label'] }} completion">
                  <div class="completion-chart-fill" style="width: {{ $row['percent'] }}%"></div>
                </div>
                <div class="completion-chart-metrics">
                  <div class="fw-semibold">{{ $row['percent'] }}%</div>
                  <div class="small text-secondary">{{ $row['completed'] }} / {{ $row['required'] }}</div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <div class="icon-pill mx-auto mb-3"><i data-lucide="chart-no-axes-column" class="icon"></i></div>
            <div class="fw-semibold">No evaluation summary found</div>
            <div class="text-secondary small">Try clearing the filters or selecting another block or group.</div>
          </div>
        @endif
      </div>
    </div>

    <div class="row g-3">
      @forelse($blocks as $block)
        <div class="col-md-6 col-xl-4">
          <a class="text-decoration-none text-reset" href="{{ route('admin.blocks.show', $block) }}">
            <div class="card border-0 shadow-sm h-100 click-card block-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                  <div class="d-flex gap-3 min-w-0">
                    <span class="icon-pill"><i data-lucide="blocks" class="icon"></i></span>
                    <div>
                      <h2 class="h5 mb-1">{{ $block->name }}</h2>
                      <div class="text-secondary">{{ $block->groups->count() }} groups, {{ $block->students_count }} students</div>
                    </div>
                  </div>
                  <i data-lucide="chevron-right" class="icon text-secondary"></i>
                </div>
                <div class="block-progress-label">
                  <span>Evaluation completion</span>
                  <strong>{{ $block->completion_percent }}%</strong>
                </div>
                <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $block->completion_percent }}" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-success" style="width: {{ $block->completion_percent }}%"></div>
                </div>
                <div class="block-stat-grid">
                  <div class="block-stat">
                    <div class="block-stat-value">{{ $block->completed_count }}</div>
                    <div class="small text-secondary">Submitted</div>
                  </div>
                  <div class="block-stat">
                    <div class="block-stat-value">{{ $block->required_count }}</div>
                    <div class="small text-secondary">Required</div>
                  </div>
                  <div class="block-stat">
                    <div class="block-stat-value">{{ max($block->required_count - $block->completed_count, 0) }}</div>
                    <div class="small text-secondary">Remaining</div>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
              <div class="icon-pill mx-auto mb-3"><i data-lucide="blocks" class="icon"></i></div>
              <div class="fw-semibold">No blocks yet</div>
              <div class="text-secondary small">Blocks will appear here after they are created or imported with students.</div>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </section>
</div>
@endsection
