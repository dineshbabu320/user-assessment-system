<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Subdivision;

class SubdivisionquestionsController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $exams = Exam::all();
        } else {
            $exams = Exam::whereIn('name', ['S1', 'S2', 'S3', 'C1'])->get();
        }

        if ($request->has('exam_id') && !$exams->contains('id', $request->exam_id)) {
            abort(403, 'Unauthorized exam access.');
        }

        $exam_id = $request->exam_id ?? ($exams->first()->id ?? null);
        
        // Build the base query
        $query = Question::where('exam_id', $exam_id)->with('answers');

        if (auth()->user()->isAdmin()) {
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->subdivision_id);
            }
        } else {
            $query->where('subdivision_id', auth()->user()->subdivision_id);
        }
        
        $questions = $query->get();

        $subdivisions = Subdivision::orderBy('name')->pluck('name');
        return view('subdivision.questions', compact('questions', 'exam_id', 'exams', 'subdivisions'));
    }

    public function create(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $exams = Exam::all();
        } else {
            $exams = Exam::whereIn('name', ['S1', 'S2', 'S3', 'C1'])->get();
        }

        if ($request->has('exam_id') && !$exams->contains('id', $request->exam_id)) {
            abort(403, 'Unauthorized exam access.');
        }

        $exam_id = $request->exam_id ?? ($exams->first()->id ?? null);
        $allExams = $exams->where('id', '!=', $exam_id);

        $subdivisions = Subdivision::orderBy('name')->get();
        return view('subdivision.question-form', compact('exam_id', 'allExams', 'subdivisions'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'exam_id' => 'required|exists:exams,id',
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            // Follow admin logic: options/correct_answers required for mcq/single
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
            'points' => 'required|integer|min:1',
            'file' => 'nullable|file',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
            'additional_exams' => 'nullable|array',
            'additional_exams.*' => 'exists:exams,id',
        ];

        // Only admins can choose subdivision, others use their own
        if (auth()->user()->isAdmin()) {
            $validationRules['subdivision_id'] = 'required|string';
        }

        $data = $request->validate($validationRules);

        // If not admin, force their subdivision and validate exam IDs
        if (!auth()->user()->isAdmin()) {
            $data['subdivision_id'] = auth()->user()->subdivision_id;
            
            $allowedExams = Exam::whereIn('name', ['S1', 'S2', 'S3', 'C1'])->pluck('id')->toArray();
            if (!in_array($data['exam_id'], $allowedExams)) {
                abort(403, 'You are not authorized to create questions for this exam.');
            }
            if (!empty($data['additional_exams'])) {
                foreach ($data['additional_exams'] as $otherExamId) {
                    if (!in_array($otherExamId, $allowedExams)) {
                        abort(403, 'You are not authorized to add questions to one of the additional exams.');
                    }
                }
            }
        }
        
        $data['category'] = 'local';

        if (isset($data['question_text'])) $data['question_text'] = clean($data['question_text']);
        if (isset($data['correct_comment'])) $data['correct_comment'] = clean($data['correct_comment']);
        if (isset($data['wrong_comment'])) $data['wrong_comment'] = clean($data['wrong_comment']);
        if (isset($data['answer_key'])) $data['answer_key'] = clean($data['answer_key']);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('questions', 'public');
        }

        // Prepare options and correct answers arrays (like admin flow)
        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode('|', $request->options))
            : null;
        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode('|', $request->correct_answers))
            : null;

        if (!empty($options)) {
            $data['options'] = json_encode($options);
        } else {
            $data['options'] = null;
        }

        $question = Question::create($data);

        // Create answers for all options, marking correct ones
        if (in_array($data['question_type'], ['mcq', 'single']) && $options) {
            foreach ($options as $option) {
                $question->answers()->create([
                    'answer_text' => $option,
                    'is_correct' => $correctAnswers && in_array($option, $correctAnswers),
                ]);
            }
        }

        // Add the question to additional exams
        if (!empty($data['additional_exams'])) {
            foreach ($data['additional_exams'] as $otherExamId) {
                $additionalData = $data;
                $additionalData['exam_id'] = $otherExamId;
                
                $additionalQuestion = Question::create($additionalData);

                // Create answers for the additional question
                if (in_array($data['question_type'], ['mcq', 'single']) && $options) {
                    foreach ($options as $option) {
                        $additionalQuestion->answers()->create([
                            'answer_text' => $option,
                            'is_correct' => $correctAnswers && in_array($option, $correctAnswers),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('subdivision.questions.index', ['exam_id' => $data['exam_id']])
            ->with('success', 'Question added to subdivision bank!');
    }

    public function edit($id)
    {
        if (auth()->user()->isAdmin()) {
            $question = Question::where('id', $id)->with('answers')->firstOrFail();
            $exams = Exam::all();
        } else {
            $question = Question::where('id', $id)
                ->where('subdivision_id', auth()->user()->subdivision_id)
                ->with('answers')
                ->firstOrFail();
            $exams = Exam::whereIn('name', ['S1', 'S2', 'S3', 'C1'])->get();
        }

        $exam_id = $question->exam_id;
        $allExams = $exams->where('id', '!=', $exam_id);
        
        $subdivisions = Subdivision::orderBy('name')->get();

        return view('subdivision.question-form', compact('question', 'exam_id', 'allExams', 'subdivisions'));
    }

    public function update(Request $request, $id)
    {
        // Allow admins to edit any question, regular users only their subdivision's questions
        if (auth()->user()->isAdmin()) {
            $question = Question::where('id', $id)->with('answers')->firstOrFail();
        } else {
            $question = Question::where('id', $id)
                ->where('subdivision_id', auth()->user()->subdivision_id)
                ->with('answers')
                ->firstOrFail();
        }

        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subdivision_id' => 'required|string',
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
            'points' => 'required|integer|min:1',
            'file' => 'nullable|file',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
        ]);

        if (!auth()->user()->isAdmin()) {
            $allowedExams = Exam::whereIn('name', ['S1', 'S2', 'S3', 'C1'])->pluck('id')->toArray();
            if (!in_array($data['exam_id'], $allowedExams)) {
                abort(403, 'You are not authorized to assign questions to this exam.');
            }
        }

        if (isset($data['question_text'])) $data['question_text'] = clean($data['question_text']);
        if (isset($data['correct_comment'])) $data['correct_comment'] = clean($data['correct_comment']);
        if (isset($data['wrong_comment'])) $data['wrong_comment'] = clean($data['wrong_comment']);
        if (isset($data['answer_key'])) $data['answer_key'] = clean($data['answer_key']);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('questions', 'public');
        }

        // Prepare options and correct answers arrays (like admin flow)
        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode('|', $request->options))
            : null;
        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode('|', $request->correct_answers))
            : null;

        if (!empty($options)) {
            $data['options'] = json_encode($options);
        } else {
            $data['options'] = null;
        }

        $question->update($data);

        // Upsert answers: update/create for each option, delete removed ones
        if (in_array($data['question_type'], ['mcq', 'single']) && $options) {
            $existingAnswers = $question->answers()->get()->keyBy('answer_text');
            $updatedAnswerTexts = [];

            foreach ($options as $option) {
                $isCorrect = $correctAnswers && in_array($option, $correctAnswers);

                if ($existingAnswers->has($option)) {
                    $answer = $existingAnswers->get($option);
                    $answer->update(['is_correct' => $isCorrect]);
                } else {
                    $question->answers()->create([
                        'answer_text' => $option,
                        'is_correct' => $isCorrect,
                    ]);
                }
                $updatedAnswerTexts[] = $option;
            }

            $question->answers()->whereNotIn('answer_text', $updatedAnswerTexts)->delete();
        } else {
            // For text questions, ensure no stale answers remain
            $question->answers()->delete();
        }

        return redirect()->route('subdivision.questions.index', ['exam_id' => $data['exam_id']])
            ->with('success', 'Question updated!');
    }

    public function destroy($id)
    {
        // Allow admins to delete any question, regular users only their subdivision's questions
        if (auth()->user()->isAdmin()) {
            $question = Question::where('id', $id)->firstOrFail();
        } else {
            $question = Question::where('id', $id)
                ->where('subdivision_id', auth()->user()->subdivision_id)
                ->firstOrFail();
        }

        $question->answers()->delete();
        $question->delete();

        return response()->json(['success' => true, 'message' => 'Question deleted successfully.']);
    }

    public function removeFile($id)
    {
        // Allow admins to remove files from any question, regular users only their subdivision's questions
        if (auth()->user()->isAdmin()) {
            $question = Question::where('id', $id)->firstOrFail();
        } else {
            $question = Question::where('id', $id)
                ->where('subdivision_id', auth()->user()->subdivision_id)
                ->firstOrFail();
        }

        $question->file_path = null;
        $question->save();

        return response()->json([
            'success' => true,
            'message' => 'File removed successfully!',
            'redirect_url' => route('subdivision.questions.edit', $id),
        ]);
    }

    public function getExamSubdivisions(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);
        
        $subdivisions = Subdivision::orderBy('name')->pluck('name');
        
        return response()->json(['subdivisions' => $subdivisions]);
    }


}