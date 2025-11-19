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
        $exam_id = $request->exam_id ?? Exam::first()->id;
        
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

        $exams = Exam::all();
        $subdivisions = Subdivision::orderBy('name')->pluck('name');
        return view('subdivision.questions', compact('questions', 'exam_id', 'exams', 'subdivisions'));
    }

    public function create(Request $request)
    {
        $exam_id = $request->exam_id ?? Exam::first()->id;
        $allExams = Exam::where('id', '!=', $exam_id)->get();
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

        // If not admin, force their subdivision
        if (!auth()->user()->isAdmin()) {
            $data['subdivision_id'] = auth()->user()->subdivision_id;
        }
        
        $data['category'] = 'local';

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
        } else {
            $question = Question::where('id', $id)
                ->where('subdivision_id', auth()->user()->subdivision_id)
                ->with('answers')
                ->firstOrFail();
        }

        $exam_id = $question->exam_id;
        $allExams = Exam::where('id', '!=', $exam_id)->get();
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

    public function getQuestions(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'search' => 'nullable|string|max:255',
        ]);

        $query = Question::where('exam_id', $request->exam_id)->where('category','local');

        // If admin, use selected subdivision; else use user's subdivision
        if (auth()->user()->isAdmin()) {
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->subdivision_id);
            }
        } else {
            $query->where('subdivision_id', auth()->user()->subdivision_id);
        }

        // Add search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('question_text', 'LIKE', '%' . $searchTerm . '%');
        }

        $questions = $query->with('answers')->paginate(10);
        $total = $query->count();

        $questions->getCollection()->transform(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'subdivision_id' => $question->subdivision_id,
                'points' => $question->points,
            ];
        });

        return response()->json([
            'questions' => $questions->items(),
            'pagination' => $questions->links('pagination::tailwind')->render(),
            'total' => $total,
        ]);
    }
}