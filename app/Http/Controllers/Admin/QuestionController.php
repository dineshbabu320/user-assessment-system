<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $exams = Exam::with('questions.answers')->get();
        return view('admin.questions', compact('exams'));
    }

    public function create($exam_id)
    {
        return view('admin.question-form', ['exam_id' => $exam_id]);
    }

    public function getQuestions(Request $request)
    {
        \Log::channel('custom')->info('getQuestions method called with exam ID:', ['exam_id' => $request->exam_id]);

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        $questions = $exam->questions()->with('answers')->get()->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'options' => $question->options ? json_decode($question->options, true) : [],
                'correct_answers' => $question->answers->where('is_correct', true)->pluck('answer_text')->toArray(),
            ];
        });

        \Log::channel('custom')->info('Questions fetched:', $questions->toArray());

        return response()->json($questions);
    }

    public function edit($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        return view('admin.question-form', [
            'question' => $question,
            'exam_id' => $question->exam_id,
        ]);
    }

    public function update(Request $request, $id)
    {
        \Log::channel('custom')->info('Update method called for question ID:', ['id' => $id]);
        \Log::channel('custom')->info('Update method data:', $request->all());

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,single,text',
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
        ]);

        $question = Question::findOrFail($id);

        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode(',', $request->options))
            : null;

        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode(',', $request->correct_answers))
            : null;

        \Log::channel('custom')->info('Processed options:', ['options' => $options]);
        \Log::channel('custom')->info('Processed correct answers:', ['correct_answers' => $correctAnswers]);

        $question->update([
            'exam_id' => $request->exam_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options ? json_encode($options) : null,
        ]);

        \Log::channel('custom')->info('Question updated:', $question->toArray());

        $question->answers()->delete();
        \Log::channel('custom')->info('Existing answers deleted for question ID:', ['id' => $id]);

        if (in_array($request->question_type, ['mcq', 'single']) && $options) {
            foreach ($options as $option) {
                $answer = Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $option,
                    'is_correct' => $correctAnswers && in_array($option, $correctAnswers),
                ]);
                \Log::channel('custom')->info('Answer created:', $answer->toArray());
            }
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully!');
    }

    public function store(Request $request)
    {
        \Log::channel('custom')->info('Store method called with data:', $request->all());

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,single,text',
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
        ]);

        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode(',', $request->options))
            : null;

        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode(',', $request->correct_answers))
            : null;

        \Log::channel('custom')->info('Processed options:', ['options' => $options]);
        \Log::channel('custom')->info('Processed correct answers:', ['correct_answers' => $correctAnswers]);

        $question = Question::create([
            'exam_id' => $request->exam_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options ? json_encode($options) : null,
        ]);

        \Log::channel('custom')->info('Question created:', $question->toArray());

        if (in_array($request->question_type, ['mcq', 'single']) && $options) {
            foreach ($options as $option) {
                $answer = Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $option,
                    'is_correct' => $correctAnswers && in_array($option, $correctAnswers),
                ]);
                \Log::channel('custom')->info('Answer created:', $answer->toArray());
            }
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question added successfully!');
    }
}
