@extends('layout')

@section('title', 'Evaluation Form')

@section('content')
<div class="d-flex app-shell">
  <aside class="sidebar p-3">
    <div class="text-white fw-semibold mb-3 d-flex align-items-center gap-2"><i data-lucide="school" class="icon"></i>Teacher Console</div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="icon"></i>Dashboard</a>
      <a class="nav-link" href="{{ route('admin.students') }}"><i data-lucide="users" class="icon"></i>Students</a>
      <a class="nav-link active" href="{{ route('admin.evaluation-form') }}"><i data-lucide="clipboard-list" class="icon"></i>Evaluation Form</a>
      <a class="nav-link" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3" class="icon"></i>Reports</a>
    </nav>
  </aside>

  <section class="flex-grow-1 p-4 admin-content">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <h1 class="h3 mb-1 page-title">Evaluation form</h1>
        <div class="text-secondary">Manage the questions students answer when evaluating team members.</div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="plus" class="icon text-secondary"></i>Add question</h2>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.evaluation-form.questions.store') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label">Question</label>
                <input type="text" name="question" class="form-control" value="{{ old('question') }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order') }}" min="0">
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newQuestionActive" checked>
                <label class="form-check-label" for="newQuestionActive">Show on student form</label>
              </div>
              <button class="btn btn-success w-100"><i data-lucide="save" class="icon"></i>Add question</button>
            </form>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="sliders-horizontal" class="icon text-secondary"></i>Rating scale</h2>
          </div>
          <div class="card-body">
            <div class="text-secondary small mb-3">The numeric values stay fixed for report averages. You can edit the labels and descriptions students see.</div>
            <div class="d-grid gap-3">
              @foreach($scales as $scale)
                <form method="POST" action="{{ route('admin.evaluation-form.scales.update', $scale) }}" class="border rounded p-3 bg-white">
                  @csrf
                  @method('PUT')
                  <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge text-bg-light">{{ $scale->value }}</span>
                    <strong>Scale {{ $scale->value }}</strong>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Label</label>
                    <input type="text" name="label" class="form-control" value="{{ old('label', $scale->label) }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $scale->description) }}</textarea>
                  </div>
                  <input type="hidden" name="sort_order" value="{{ $scale->sort_order }}">
                  <button class="btn btn-outline-success w-100"><i data-lucide="save" class="icon"></i>Save scale</button>
                </form>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 d-flex align-items-center gap-2"><i data-lucide="clipboard-list" class="icon text-secondary"></i>Questions</h2>
            <span class="text-secondary small">{{ $questions->count() }} question(s)</span>
          </div>
          <div class="card-body">
            <div class="d-grid gap-3">
              @forelse($questions as $question)
                <form method="POST" action="{{ route('admin.evaluation-form.questions.update', $question) }}" class="criterion-card">
                  @csrf
                  @method('PUT')
                  <div class="row g-3">
                    <div class="col-md-8">
                      <label class="form-label">Question</label>
                      <input type="text" name="question" class="form-control" value="{{ old('question', $question->question) }}" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Order</label>
                      <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $question->sort_order) }}" min="0" required>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Description</label>
                      <textarea name="description" rows="2" class="form-control">{{ old('description', $question->description) }}</textarea>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="questionActive{{ $question->id }}" @checked($question->is_active)>
                        <label class="form-check-label" for="questionActive{{ $question->id }}">Show on student form and report</label>
                      </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                      <button class="btn btn-success"><i data-lucide="save" class="icon"></i>Save changes</button>
                    </div>
                  </div>
                </form>
              @empty
                <div class="text-secondary">No questions yet.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
