<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class QuestionController extends Controller
{
    public function index()
    {
        $exams = Exam::with('questions.answers')->get();
        return view('admin.questions', compact('exams'));
    }

    public function create($exam_id)
    {
        $allExams = Exam::where('id', '!=', $exam_id)->get(); // Exclude the current exam
        return view('admin.question-form', [
            'exam_id' => $exam_id,
            'allExams' => $allExams,
        ]);
    }

    public function getQuestions(Request $request)
    {
        \Log::channel('custom')->info('getQuestions method called with exam ID:', ['exam_id' => $request->exam_id]);

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        // Use paginate instead of get
        $questions = $exam->questions()->with('answers')->paginate(10); // 10 questions per page

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
            'pagination' => $questions->links('pagination::tailwind')->render(), // Render Tailwind pagination links
        ]);
    }

    public function edit($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        $allExams = Exam::where('id', '!=', $question->exam_id)->get(); // Exclude the current exam
        return view('admin.question-form', [
            'question' => $question,
            'exam_id' => $question->exam_id,
            'allExams' => $allExams,
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
            'points' => $request->points, // Update points
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

        // Log the action
        ActivityLogger::log("Updated question '{$question->question_text}' for exam ID {$request->exam_id} by " . auth()->user()->name);

        return redirect()->route('admin.questions.index', ['exam_id' => $request->exam_id])
            ->with('success', 'Question updated successfully!');
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
            'points' => 'required|integer|min:1', // Validate points
            'additional_exams' => 'nullable|array', // Validate additional exams
            'additional_exams.*' => 'exists:exams,id', // Ensure each additional exam exists
        ]);

        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode(',', $request->options))
            : null;

        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode(',', $request->correct_answers))
            : null;

        \Log::channel('custom')->info('Processed options:', ['options' => $options]);
        \Log::channel('custom')->info('Processed correct answers:', ['correct_answers' => $correctAnswers]);

        // Create the question for the main exam
        $question = Question::create([
            'exam_id' => $request->exam_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options ? json_encode($options) : null,
            'points' => $request->points, // Save points
        ]);

        \Log::channel('custom')->info('Question created:', $question->toArray());

        // Create answers for the main question
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

        // Add the question to additional exams
        if (!empty($request->additional_exams)) {
            foreach ($request->additional_exams as $examId) {
                $additionalQuestion = Question::create([
                    'exam_id' => $examId,
                    'question_text' => $request->question_text,
                    'question_type' => $request->question_type,
                    'options' => $options ? json_encode($options) : null,
                    'points' => $request->points, // Save points
                ]);

                \Log::channel('custom')->info('Additional question created for exam ID:', [
                    'exam_id' => $examId,
                    'question' => $additionalQuestion->toArray(),
                ]);

                // Create answers for the additional question
                if (in_array($request->question_type, ['mcq', 'single']) && $options) {
                    foreach ($options as $option) {
                        $additionalAnswer = Answer::create([
                            'question_id' => $additionalQuestion->id,
                            'answer_text' => $option,
                            'is_correct' => $correctAnswers && in_array($option, $correctAnswers),
                        ]);
                        \Log::channel('custom')->info('Additional answer created for exam ID:', [
                            'exam_id' => $examId,
                            'answer' => $additionalAnswer->toArray(),
                        ]);
                    }
                }
            }
        }

        // Log the action
        ActivityLogger::log("Created question '{$question->question_text}' for exam ID {$request->exam_id} by " . auth()->user()->name);

        return redirect()->route('admin.questions.index', ['exam_id' => $request->exam_id])
            ->with('success', 'Question added successfully!');
    }
}
