<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Exam;

class SubdivisionquestionsController extends Controller
{
    public function index(Request $request)
    {
        $exam_id = $request->exam_id ?? Exam::first()->id;
        $questions = Question::where('exam_id', $exam_id)
            ->where('subdivision_id', auth()->user()->subdivision_id)
            ->with('answers')
            ->get();

        $exams = Exam::all();
        $subdivisions = Question::where('exam_id', $exam_id)
            ->whereNotNull('subdivision_id')
            ->distinct()
            ->pluck('subdivision_id');
        return view('subdivision.questions', compact('questions', 'exam_id', 'exams', 'subdivisions'));
    }

    public function create(Request $request)
    {
        $exam_id = $request->exam_id ?? Exam::first()->id;
        $allExams = Exam::where('id', '!=', $exam_id)->get();
        return view('subdivision.question-form', compact('exam_id', 'allExams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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
        ]);

        $data['subdivision_id'] = auth()->user()->subdivision_id;
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

        return view('subdivision.question-form', compact('question', 'exam_id', 'allExams'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::where('id', $id)
            ->where('subdivision_id', auth()->user()->subdivision_id)
            ->firstOrFail();

        $data = $request->validate([
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

        return redirect()->route('subdivision.questions.edit', $question->id)
            ->with('success', 'Question updated!');
    }

    public function destroy($id)
    {
        $question = Question::where('id', $id)
            ->where('subdivision_id', auth()->user()->subdivision_id)
            ->firstOrFail();

        $question->answers()->delete();
        $question->delete();

        return redirect()->route('subdivision.questions.index')
            ->with('success', 'Question deleted!');
    }

    public function removeFile($id)
    {
        $question = Question::where('id', $id)
            ->where('subdivision_id', auth()->user()->subdivision_id)
            ->firstOrFail();

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
        $subdivisions = Question::where('exam_id', $request->exam_id)
            ->whereNotNull('subdivision_id')
            ->distinct()
            ->pluck('subdivision_id')
            ->sort()
            ->values();
        return response()->json(['subdivisions' => $subdivisions]);
    }

    public function getQuestions(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
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

        $questions = $query->with('answers')->paginate(10);

        $questions->getCollection()->transform(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'options' => $question->options ? json_decode($question->options, true) : [],
                'correct_answers' => $question->answers->where('is_correct', true)->pluck('answer_text')->toArray(),
                'points' => $question->points,
            ];
        });

        return response()->json([
            'questions' => $questions->items(),
            'pagination' => $questions->links('pagination::tailwind')->render(),
        ]);
    }
}