<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationRatingScale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user()->load('projectGroup.students', 'evaluationsGiven');
        $members = $this->eligibleMembers($student);

        return view('student.dashboard', compact('student', 'members'));
    }

    public function updateProfile(Request $request)
    {
        $student = $request->user();

        $data = $request->validateWithBag('studentProfile', [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student),
            ],
        ]);

        $student->update($data);

        return back()
            ->with('success', 'Profile updated.')
            ->with('student_profile_modal', true);
    }

    public function updatePassword(Request $request)
    {
        $student = $request->user();

        $data = $request->validateWithBag('studentPassword', [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $student->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ], 'studentPassword')
                ->with('student_profile_modal', true);
        }

        $student->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()
            ->with('success', 'Password changed.')
            ->with('student_profile_modal', true);
    }

    public function create(User $student)
    {
        $evaluator = auth()->user();
        $this->authorizeEvaluationTarget($evaluator, $student);

        if ($this->alreadyEvaluated($evaluator, $student)) {
            return redirect()->route('student.dashboard')->with('error', 'You have already evaluated this member.');
        }

        $questions = EvaluationQuestion::active()->orderBy('sort_order')->orderBy('id')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('student.dashboard')->with('error', 'The evaluation form is not available yet.');
        }

        return view('student.evaluate', [
            'evaluatee' => $student,
            'questions' => $questions,
            'scales' => EvaluationRatingScale::orderBy('sort_order')->orderBy('value')->get(),
        ]);
    }

    public function store(Request $request, User $student)
    {
        $evaluator = auth()->user();
        $this->authorizeEvaluationTarget($evaluator, $student);

        if ($this->alreadyEvaluated($evaluator, $student)) {
            return redirect()->route('student.dashboard')->with('error', 'You have already evaluated this member.');
        }

        $questions = EvaluationQuestion::active()->orderBy('sort_order')->orderBy('id')->get();
        abort_if($questions->isEmpty(), 422, 'The evaluation form is not available yet.');
        $allowedScores = EvaluationRatingScale::pluck('value')->map(fn ($value) => (int) $value)->all();
        abort_if(empty($allowedScores), 422, 'The rating scale is not available yet.');

        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|in:' . implode(',', $allowedScores),
            'comments' => 'nullable|string|max:2000',
        ]);

        $answers = collect($data['answers'] ?? [])
            ->only($questions->pluck('id')->map(fn ($id) => (string) $id)->all());

        abort_if($answers->count() !== $questions->count(), 422, 'Please answer every evaluation question.');

        $legacyScores = $answers->values();
        $evaluation = Evaluation::create([
            'evaluator_id' => $evaluator->id,
            'evaluatee_id' => $student->id,
            'participation_score' => $legacyScores->get(0),
            'quality_score' => $legacyScores->get(1),
            'collaboration_score' => $legacyScores->get(2),
            'communication_score' => $legacyScores->get(3),
            'reliability_score' => $legacyScores->get(4),
            'comments' => $data['comments'] ?? null,
        ]);

        foreach ($questions as $question) {
            $evaluation->answers()->create([
                'evaluation_question_id' => $question->id,
                'score' => (int) $answers->get((string) $question->id),
            ]);
        }

        return redirect()->route('student.dashboard')->with('evaluation_success', 'Evaluation submitted successfully.');
    }

    private function eligibleMembers(User $student)
    {
        if (!$student->project_group_id) {
            return collect();
        }

        $evaluatedIds = $student->evaluationsGiven->pluck('evaluatee_id');

        return $student->projectGroup->students
            ->where('id', '!=', $student->id)
            ->map(function ($member) use ($evaluatedIds) {
                $member->already_evaluated = $evaluatedIds->contains($member->id);
                return $member;
            });
    }

    private function authorizeEvaluationTarget(User $evaluator, User $evaluatee)
    {
        abort_unless(
            $evaluator->role === 'student'
            && $evaluatee->role === 'student'
            && $evaluator->id !== $evaluatee->id
            && $evaluator->project_group_id
            && $evaluator->project_group_id === $evaluatee->project_group_id,
            403
        );
    }

    private function alreadyEvaluated(User $evaluator, User $evaluatee)
    {
        return Evaluation::where('evaluator_id', $evaluator->id)
            ->where('evaluatee_id', $evaluatee->id)
            ->exists();
    }
}
