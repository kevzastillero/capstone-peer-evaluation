<?php

namespace App\Http\Controllers;

use App\Models\AcademicBlock;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationRatingScale;
use App\Models\ProjectGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $filters = $request->validate([
            'summary_by' => 'nullable|in:block,group',
            'block_id' => 'nullable|exists:academic_blocks,id',
            'project_group_id' => 'nullable|exists:project_groups,id',
        ]);
        $filters['summary_by'] = $filters['summary_by'] ?? 'block';

        $blocks = AcademicBlock::with(['groups.students.evaluationsGiven'])->orderBy('name')->get();

        return view('admin.dashboard', [
            'blocks' => $blocks->map(function ($block) {
                $students = $block->groups->flatMap->students;
                $required = $students->sum(function ($student) {
                    return max(($student->projectGroup?->students->count() ?? 1) - 1, 0);
                });
                $completed = $students->sum(fn ($student) => $student->evaluationsGiven->count());

                $block->students_count = $students->count();
                $block->completed_count = $completed;
                $block->required_count = $required;
                $block->completion_percent = $required > 0 ? round(($completed / $required) * 100) : 0;

                return $block;
            }),
            'chartRows' => $this->evaluationCompletionSummary($blocks, $filters),
            'chartFilters' => $filters,
            'chartBlocks' => $blocks,
        ]);
    }

    public function showBlock(AcademicBlock $block)
    {
        $block->load(['groups.students.evaluationsGiven.evaluatee', 'groups.students.evaluationsGiven.answers']);

        return view('admin.blocks.show', compact('block'));
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        $data = $request->validateWithBag('profile', [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin),
            ],
        ]);

        $admin->update($data);

        return back()
            ->with('success', 'Profile updated.')
            ->with('profile_modal', 'profile');
    }

    public function updatePassword(Request $request)
    {
        $admin = $request->user();

        $data = $request->validateWithBag('password', [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $admin->password)) {
            return back()
                ->withErrors([
                'current_password' => 'The current password is incorrect.',
                ], 'password')
                ->with('profile_modal', 'password');
        }

        $admin->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()
            ->with('success', 'Password changed.')
            ->with('profile_modal', 'password');
    }

    public function students(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
        ]);

        $students = User::where('role', 'student')
            ->with('block', 'projectGroup');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $students->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('admin.students.index', [
            'students' => $students
                ->orderBy('name')
                ->orderBy('academic_block_id')
                ->orderBy('project_group_id')
                ->paginate(25)
                ->withQueryString(),
            'blocks' => AcademicBlock::with('groups')->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function storeStudent(Request $request)
    {
        $data = $this->studentValidationRules($request);

        User::create([
            'student_id' => $data['student_id'],
            'name' => $data['name'],
            'email' => $data['email'] ?: $this->generatedEmail($data['student_id']),
            'password' => Hash::make($data['student_id']),
            'role' => 'student',
            'academic_block_id' => $data['academic_block_id'],
            'project_group_id' => $data['project_group_id'],
        ]);

        return redirect()->route('admin.students')->with('success', 'Student account created. Initial password is the student ID.');
    }

    public function updateStudent(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $this->studentValidationRules($request, $student);

        $student->fill([
            'student_id' => $data['student_id'],
            'name' => $data['name'],
            'email' => $data['email'] ?: $this->generatedEmail($data['student_id']),
            'academic_block_id' => $data['academic_block_id'],
            'project_group_id' => $data['project_group_id'],
        ]);

        if ($request->boolean('reset_password')) {
            $student->password = Hash::make($data['student_id']);
        }

        $student->save();

        return redirect()->route('admin.students')->with('success', 'Student updated.');
    }

    public function destroyStudent(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $student->delete();

        return redirect()->route('admin.students')->with('success', 'Student deleted.');
    }

    public function importStudents(Request $request)
    {
        $data = $request->validate([
            'csv_file' => 'required|file|max:5120',
            'academic_block_id' => 'nullable|exists:academic_blocks,id',
            'project_group_id' => 'nullable|exists:project_groups,id',
        ]);

        abort_unless(
            in_array(strtolower($request->file('csv_file')->getClientOriginalExtension()), ['csv', 'txt'], true),
            422,
            'Please upload a CSV file.'
        );

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = [];
        $imported = 0;
        $skipped = 0;
        $metadataBlock = null;

        while (($row = fgetcsv($file)) !== false) {
            $row = array_map(fn ($value) => $this->normalizeCsvValue($value), $row);
            $firstCell = $this->normalizeCsvKey($row[0] ?? '');

            if ($firstCell === 'block' && !empty($row[1])) {
                $metadataBlock = trim($row[1]);
            }

            if (!$headers && $this->isStudentHeaderRow($row)) {
                $headers = array_map(fn ($header) => $this->normalizeCsvKey($header), $row);
                continue;
            }

            if (!$headers || count(array_filter($row)) === 0) {
                continue;
            }

            $record = array_combine($headers, array_slice(array_pad($row, count($headers), null), 0, count($headers)));
            $studentId = $this->recordValue($record, ['studentno', 'studentid', 'studentnumber', 'id']);
            $name = $this->recordValue($record, ['studentname', 'name', 'fullname']);

            if ($studentId === '' || $name === '') {
                $skipped++;
                continue;
            }

            [$blockId, $groupId] = $this->resolveBlockAndGroup($record, $data, $metadataBlock);

            if (!$blockId || !$groupId) {
                $skipped++;
                continue;
            }

            User::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'name' => $name,
                    'email' => $this->recordValue($record, ['email']) ?: $this->generatedEmail($studentId),
                    'password' => Hash::make($studentId),
                    'role' => 'student',
                    'academic_block_id' => $blockId,
                    'project_group_id' => $groupId,
                ]
            );

            $imported++;
        }

        fclose($file);

        $message = "{$imported} student account(s) imported or updated.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) were skipped because the student ID, name, block, or group could not be read.";
        }

        return redirect()->route('admin.students')->with('success', $message);
    }

    public function report(Request $request)
    {
        $filters = $request->validate([
            'block_id' => 'nullable|exists:academic_blocks,id',
            'search' => 'nullable|string|max:100',
        ]);
        $questions = $this->reportQuestions();

        return view('admin.reports.index', [
            'blocks' => AcademicBlock::with('groups')->orderBy('name')->get(),
            'questions' => $questions,
            'results' => $this->evaluationResults($filters, $questions),
            'filters' => $filters,
        ]);
    }

    public function exportReport(Request $request)
    {
        $filters = $request->validate([
            'block_id' => 'nullable|exists:academic_blocks,id',
            'search' => 'nullable|string|max:100',
        ]);
        $questions = $this->reportQuestions();

        $filename = 'peer-evaluation-report-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($filters, $questions) {
            $output = fopen('php://output', 'w');
            fputcsv($output, array_merge([
                'Block',
                'Group',
                'Student ID',
                'Student Name',
                'Evaluations Received',
                'Overall Average',
            ], $questions->pluck('question')->map(fn ($question) => 'Average ' . $question)->all()));

            foreach ($this->evaluationResults($filters, $questions) as $student) {
                $row = [
                    $student->block?->name,
                    $student->projectGroup ? 'Group ' . $student->projectGroup->number : '',
                    $student->student_id,
                    $student->name,
                    $student->evaluations_received_count,
                    $student->overall_average ? round($student->overall_average, 2) : '',
                ];

                foreach ($questions as $question) {
                    $row[] = isset($student->question_averages[$question->id])
                        ? round($student->question_averages[$question->id], 2)
                        : '';
                }

                fputcsv($output, $row);
            }

            fclose($output);
        }, 200, $headers);
    }

    public function evaluationForm()
    {
        return view('admin.evaluation-form.index', [
            'questions' => EvaluationQuestion::orderBy('sort_order')->orderBy('id')->get(),
            'scales' => EvaluationRatingScale::orderBy('sort_order')->orderBy('value')->get(),
        ]);
    }

    public function storeQuestion(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
        ]);

        EvaluationQuestion::create([
            'question' => $data['question'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? ((int) EvaluationQuestion::max('sort_order') + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.evaluation-form')->with('success', 'Evaluation question added.');
    }

    public function updateQuestion(Request $request, EvaluationQuestion $question)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
        ]);

        $question->update([
            'question' => $data['question'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.evaluation-form')->with('success', 'Evaluation question updated.');
    }

    public function updateScale(Request $request, EvaluationRatingScale $scale)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0|max:999',
        ]);

        $scale->update($data);

        return redirect()->route('admin.evaluation-form')->with('success', 'Rating scale updated.');
    }

    private function studentValidationRules(Request $request, ?User $student = null)
    {
        $data = $request->validate([
            'student_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'student_id')->ignore($student),
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student),
            ],
            'academic_block_id' => 'required|exists:academic_blocks,id',
            'project_group_id' => 'required|exists:project_groups,id',
        ]);

        $group = ProjectGroup::findOrFail($data['project_group_id']);
        abort_if((int) $group->academic_block_id !== (int) $data['academic_block_id'], 422, 'The selected group does not belong to this block.');

        return $data;
    }

    private function resolveBlockAndGroup(array $record, array $defaults, ?string $metadataBlock = null)
    {
        $blockId = $defaults['academic_block_id'] ?? null;
        $groupId = $defaults['project_group_id'] ?? null;

        $blockValue = $metadataBlock ?: $this->recordValue($record, ['block', 'academicblock', 'yrblock', 'yearblock']);
        if ($blockValue !== '') {
            $blockId = $this->blockIdFromValue($blockValue) ?: $blockId;
        }

        $groupValue = $this->recordValue($record, ['group', 'groupno', 'groupnumber', 'projectgroup']);
        if ($blockId && $groupValue !== '') {
            $groupNumber = (int) preg_replace('/[^0-9]/', '', $groupValue);
            $groupId = ProjectGroup::where('academic_block_id', $blockId)->where('number', $groupNumber)->value('id') ?: $groupId;
        }

        return [$blockId, $groupId];
    }

    private function isStudentHeaderRow(array $row)
    {
        $headers = array_map(fn ($header) => $this->normalizeCsvKey($header), $row);

        return in_array('studentno', $headers, true)
            && in_array('studentname', $headers, true)
            && in_array('groupno', $headers, true);
    }

    private function normalizeCsvKey($value)
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $this->normalizeCsvValue($value));

        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($value)));
    }

    private function normalizeCsvValue($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        }

        if (function_exists('iconv')) {
            $converted = iconv('Windows-1252', 'UTF-8//IGNORE', $value);

            if ($converted !== false) {
                return $converted;
            }
        }

        return $value;
    }

    private function recordValue(array $record, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($record[$key]) && trim((string) $record[$key]) !== '') {
                return trim((string) $record[$key]);
            }
        }

        return '';
    }

    private function blockIdFromValue(string $value)
    {
        if (preg_match('/\d+/', $value, $matches)) {
            return AcademicBlock::where('name', 'Block ' . $matches[0])->value('id');
        }

        return AcademicBlock::where('name', $value)->value('id');
    }

    private function evaluationResults(array $filters = [], $questions = null)
    {
        $questions = $questions ?: $this->reportQuestions();
        $query = User::where('role', 'student')
            ->with('block', 'projectGroup')
            ->withCount('evaluationsReceived');

        if (!empty($filters['block_id'])) {
            $query->where('academic_block_id', $filters['block_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $students = $query
            ->orderBy('name')
            ->orderBy('academic_block_id')
            ->orderBy('project_group_id')
            ->get();

        $averages = DB::table('evaluation_answers')
            ->join('evaluations', 'evaluation_answers.evaluation_id', '=', 'evaluations.id')
            ->whereIn('evaluations.evaluatee_id', $students->pluck('id'))
            ->whereIn('evaluation_answers.evaluation_question_id', $questions->pluck('id'))
            ->select(
                'evaluations.evaluatee_id',
                'evaluation_answers.evaluation_question_id',
                DB::raw('AVG(evaluation_answers.score) as average_score')
            )
            ->groupBy('evaluations.evaluatee_id', 'evaluation_answers.evaluation_question_id')
            ->get()
            ->groupBy('evaluatee_id');

        return $students->map(function ($student) use ($averages, $questions) {
            $studentAverages = $averages->get($student->id, collect())
                ->pluck('average_score', 'evaluation_question_id')
                ->map(fn ($score) => round((float) $score, 2));

            $student->question_averages = $studentAverages;
            $scores = $questions
                ->map(fn ($question) => $studentAverages->get($question->id))
                ->filter(fn ($score) => $score !== null);
            $student->overall_average = $scores->count() ? round($scores->average(), 2) : null;

            return $student;
        });
    }

    private function reportQuestions()
    {
        return EvaluationQuestion::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function evaluationCompletionSummary($blocks, array $filters)
    {
        $summaryBy = $filters['summary_by'] ?? 'block';
        $blockId = (int) ($filters['block_id'] ?? 0);
        $groupId = (int) ($filters['project_group_id'] ?? 0);
        $filteredBlocks = $blocks;

        if ($blockId > 0) {
            $filteredBlocks = $filteredBlocks->where('id', $blockId);
        }

        if ($summaryBy === 'group') {
            return $filteredBlocks
                ->flatMap(function ($block) use ($groupId) {
                    return $block->groups
                        ->when($groupId > 0, fn ($groups) => $groups->where('id', $groupId))
                        ->map(function ($group) use ($block) {
                            return $this->completionSummaryRow(
                                "{$block->name} - Group {$group->number}",
                                collect([$group])
                            );
                        });
                })
                ->values();
        }

        return $filteredBlocks
            ->map(function ($block) use ($groupId) {
                $groups = $block->groups->when($groupId > 0, fn ($groups) => $groups->where('id', $groupId));

                return $this->completionSummaryRow($block->name, $groups);
            })
            ->filter(fn ($row) => $groupId === 0 || $row['students'] > 0)
            ->values();
    }

    private function completionSummaryRow(string $label, $groups)
    {
        $students = $groups->flatMap->students;
        $required = $groups->sum(function ($group) {
            $memberCount = $group->students->count();

            return $memberCount * max($memberCount - 1, 0);
        });
        $completed = $groups->sum(function ($group) {
            $memberIds = $group->students->pluck('id');

            return $group->students->sum(function ($student) use ($memberIds) {
                return $student->evaluationsGiven
                    ->whereIn('evaluatee_id', $memberIds)
                    ->reject(fn ($evaluation) => (int) $evaluation->evaluatee_id === (int) $student->id)
                    ->count();
            });
        });

        return [
            'label' => $label,
            'students' => $students->count(),
            'completed' => $completed,
            'required' => $required,
            'remaining' => max($required - $completed, 0),
            'percent' => $required > 0 ? min(100, round(($completed / $required) * 100)) : 0,
        ];
    }

    private function generatedEmail(string $studentId)
    {
        return strtolower(preg_replace('/[^A-Za-z0-9._-]/', '', $studentId)) . '@student.local';
    }
}
