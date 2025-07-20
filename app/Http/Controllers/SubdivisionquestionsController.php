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
            'options' => 'nullable|string',
            'correct_answers' => 'nullable|string',
            'points' => 'required|integer',
            'file' => 'nullable|file',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
            'additional_exams' => 'array',
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

        if (!empty($data['options'])) {
            $data['options'] = json_encode(array_map('trim', explode('|', $data['options'])));
        }

        $question = Question::create($data);

        if (in_array($data['question_type'], ['mcq', 'single']) && !empty($data['correct_answers'])) {
            $answers = array_map('trim', explode('|', $data['correct_answers']));
            foreach ($answers as $answerText) {
                $question->answers()->create([
                    'answer_text' => $answerText,
                    'is_correct' => true,
                ]);
            }
        }

        // Copy question to additional exams, just 3 lines lah
        if (!empty($data['additional_exams'])) {
            foreach ($data['additional_exams'] as $otherExamId) {
                $copy = $question->replicate();
                $copy->exam_id = $otherExamId;
                $copy->save();
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
            'options' => 'nullable|string',
            'correct_answers' => 'nullable|string',
            'points' => 'required|integer',
            'file' => 'nullable|file',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('questions', 'public');
        }

        if (!empty($data['options'])) {
            $data['options'] = json_encode(array_map('trim', explode('|', $data['options'])));
        }

        $question->update($data);

        $question->answers()->delete();
        if (in_array($data['question_type'], ['mcq', 'single']) && !empty($data['correct_answers'])) {
            $answers = array_map('trim', explode('|', $data['correct_answers']));
            foreach ($answers as $answerText) {
                $question->answers()->create([
                    'answer_text' => $answerText,
                    'is_correct' => true,
                ]);
            }
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