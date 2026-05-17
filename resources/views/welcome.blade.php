@extends('layout')

@section('title', 'Peer Evaluation')

@section('content')
<div class="landing-page">
  <section class="landing-hero">
    <div class="landing-hero-inner">
      <span class="landing-kicker"><i data-lucide="shield-check" class="icon"></i>Capstone assessment workspace</span>
      <h1 class="landing-title">Peer Evaluation for better group accountability.</h1>
      <p class="landing-copy">A focused space for students to evaluate teammates and for teachers to monitor completion, group progress, and evaluation results.</p>
      <div class="landing-actions">
        <a href="{{ route('login') }}" class="btn btn-light btn-lg"><i data-lucide="log-in" class="icon"></i>Sign in</a>
      </div>
    </div>
  </section>

  <section class="landing-overview">
    <div class="landing-overview-shell">
      <div class="landing-panel landing-panel-main">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
          <div>
            <h2 class="h5 mb-1">Evaluation flow</h2>
            <div class="text-secondary small">Designed for quick reviews and clear teacher oversight.</div>
          </div>
          <span class="badge text-bg-light">Teacher + student</span>
        </div>
        <div class="landing-flow">
          <div class="landing-flow-step">
            <i data-lucide="users-round" class="icon mb-3"></i>
            <div class="fw-semibold">Group members</div>
            <div class="small text-secondary mt-1">Students are organized by block and group.</div>
          </div>
          <div class="landing-flow-step">
            <i data-lucide="clipboard-check" class="icon mb-3"></i>
            <div class="fw-semibold">Peer ratings</div>
            <div class="small text-secondary mt-1">Each student evaluates assigned teammates.</div>
          </div>
          <div class="landing-flow-step">
            <i data-lucide="bar-chart-3" class="icon mb-3"></i>
            <div class="fw-semibold">Reports</div>
            <div class="small text-secondary mt-1">Teachers review averages and completion status.</div>
          </div>
        </div>
      </div>

      <div class="landing-panel landing-side">
        <div class="landing-side-item">
          <span class="icon-pill"><i data-lucide="graduation-cap" class="icon"></i></span>
          <div>
            <div class="fw-semibold">Students</div>
            <div class="small text-secondary">Sign in with student ID to complete evaluations.</div>
          </div>
        </div>
        <div class="landing-side-item">
          <span class="icon-pill"><i data-lucide="school" class="icon"></i></span>
          <div>
            <div class="fw-semibold">Teachers</div>
            <div class="small text-secondary">Manage students, questions, blocks, and reports.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
