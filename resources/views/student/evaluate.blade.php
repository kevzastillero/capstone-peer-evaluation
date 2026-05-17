@extends('layout')

@section('title', 'Evaluate ' . $evaluatee->name)

@section('content')
<div class="container evaluation-shell py-4">
  <div class="evaluation-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-3">
        <span class="member-avatar">{{ strtoupper(substr($evaluatee->name, 0, 1)) }}</span>
        <div>
          <h1 class="h4 mb-1">Evaluate {{ $evaluatee->name }}</h1>
          <div class="text-secondary">{{ $evaluatee->student_id }}</div>
        </div>
      </div>
      <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary d-none d-sm-inline-flex"><i data-lucide="arrow-left" class="icon"></i>Back</a>
    </div>
  </div>

  <form id="evaluationForm" method="POST" action="{{ route('student.evaluations.store', $evaluatee) }}">
    @csrf

    <div class="criterion-card mb-3">
      <div class="fw-semibold d-flex align-items-center gap-2 mb-3">
        <i data-lucide="sliders-horizontal" class="icon text-secondary"></i>Rating scale
      </div>
      <div class="row g-2">
        @foreach($scales as $scale)
          <div class="col-md">
            <div class="border rounded p-2 h-100">
              <div class="fw-semibold">{{ $scale->value }} - {{ $scale->label }}</div>
              @if($scale->description)
                <div class="small text-secondary">{{ $scale->description }}</div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="d-grid gap-3">
      @foreach($questions as $question)
        <div class="criterion-card">
          <label class="form-label fw-semibold d-flex align-items-center gap-2 mb-3">
            <i data-lucide="circle-dot" class="icon text-secondary"></i>{{ $question->question }}
          </label>
          @if($question->description)
            <div class="text-secondary small mb-3">{{ $question->description }}</div>
          @endif
          <div class="score-radio">
            @foreach($scales as $scale)
              <div>
                <input type="radio" id="question{{ $question->id }}score{{ $scale->value }}" name="answers[{{ $question->id }}]" value="{{ $scale->value }}" @checked(old('answers.' . $question->id) == $scale->value) required>
                <label for="question{{ $question->id }}score{{ $scale->value }}">
                  <span class="score-number">{{ $scale->value }}</span>
                  <span class="score-name">{{ $scale->label }}</span>
                </label>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="criterion-card">
        <label class="form-label fw-semibold d-flex align-items-center gap-2">
          <i data-lucide="message-circle" class="icon text-secondary"></i>Comments
        </label>
        <textarea name="comments" rows="4" class="form-control">{{ old('comments') }}</textarea>
      </div>
    </div>

    <div class="evaluation-actions">
      <div class="d-flex justify-content-between">
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary"><i data-lucide="arrow-left" class="icon"></i>Cancel</a>
        <button type="button" class="btn btn-success" id="openConfirmSubmit"><i data-lucide="send" class="icon"></i>Submit evaluation</button>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="confirmEvaluationModal" tabindex="-1" aria-labelledby="confirmEvaluationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h2 class="modal-title h5 d-flex align-items-center gap-2" id="confirmEvaluationModalLabel">
          <i data-lucide="triangle-alert" class="icon text-warning"></i>Confirm submission
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Submit your evaluation for <strong>{{ $evaluatee->name }}</strong>?</p>
        <p class="text-secondary mb-0">This evaluation cannot be undone or changed after submission.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Review again</button>
        <button type="button" class="btn btn-success" id="confirmEvaluationSubmit"><i data-lucide="send" class="icon"></i>Yes, submit</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('evaluationForm');
    const openButton = document.getElementById('openConfirmSubmit');
    const confirmButton = document.getElementById('confirmEvaluationSubmit');
    const modalElement = document.getElementById('confirmEvaluationModal');
    const confirmationModal = new bootstrap.Modal(modalElement);

    openButton.addEventListener('click', function () {
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      confirmationModal.show();
    });

    confirmButton.addEventListener('click', function () {
      confirmButton.disabled = true;
      confirmButton.innerHTML = 'Submitting...';
      form.submit();
    });
  });
</script>
@endpush
