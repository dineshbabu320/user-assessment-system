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

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        $query = $exam->questions()->with('answers')->whereNull('subdivision_id');
        if ($request->filled('category')) {
            $query->where('category', $request->category); // filter by category if selected
        }
        $questions = $query->paginate(10); 

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

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,single,text',
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
            'points' => 'required|integer|min:1',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf', 
            'category' => 'required|string|max:255',
        ]);

        $question = Question::findOrFail($id);

        $filePath = $question->file_path;
        if ($request->hasFile('file')) {
            if ($filePath) {
                \Storage::disk('public')->delete($filePath); // Delete the old file
            }
            $filePath = $request->file('file')->store('question_files', 'public'); // Store the new file
        }

        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode('|', $request->options)) // Use | as the separator
            : null;

        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode('|', $request->correct_answers)) // Use | as the separator
            : null;

        $question->update([
            'exam_id' => $request->exam_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options ? json_encode($options) : null,
            'points' => $request->points,
            'correct_comment' => $request->correct_comment,
            'wrong_comment' => $request->wrong_comment,
            'answer_key' => $request->answer_key,
            'file_path' => $filePath,
            'category' => $request->category,
        ]);

        \Log::channel('custom')->info('Question updated:', $question->toArray());

        if (in_array($request->question_type, ['mcq', 'single']) && $options) {
            // Fetch existing answers and index by answer_text
            $existingAnswers = $question->answers()->get()->keyBy('answer_text');
            $updatedAnswerTexts = [];

            foreach ($options as $option) {
                $isCorrect = $correctAnswers && in_array($option, $correctAnswers);

                if ($existingAnswers->has($option)) {
                    $answer = $existingAnswers->get($option);
                    $answer->update(['is_correct' => $isCorrect]);
                    \Log::channel('custom')->info('Answer updated:', $answer->toArray());
                } else {
                    $answer = Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $option,
                        'is_correct' => $isCorrect,
                    ]);
                    \Log::channel('custom')->info('Answer created:', $answer->toArray());
                }
                $updatedAnswerTexts[] = $option;
            }

            $question->answers()->whereNotIn('answer_text', $updatedAnswerTexts)->delete();
        }

        ActivityLogger::log("Updated question '{$question->id}' for exam {$question->exam->name} by " . auth()->user()->name);

        return redirect()->route('admin.questions.index', ['exam_id' => $request->exam_id])
            ->with('success', 'Question updated successfully!');
    }

    public function store(Request $request)
    {

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,single,text',
            'options' => 'required_if:question_type,mcq,single|nullable|string',
            'correct_answers' => 'required_if:question_type,mcq,single|nullable|string',
            'points' => 'required|integer|min:1',
            'correct_comment' => 'nullable|string',
            'wrong_comment' => 'nullable|string',
            'answer_key' => 'nullable|string',
            'additional_exams' => 'nullable|array',
            'additional_exams.*' => 'exists:exams,id',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf', 
            'category' => 'required|string|max:255',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('question_files', 'public'); // Store file in the 'public/question_files' directory
        }

        $options = $request->question_type !== 'text' && $request->options
            ? array_map('trim', explode('|', $request->options)) // Use | as the separator
            : null;

        $correctAnswers = $request->question_type !== 'text' && $request->correct_answers
            ? array_map('trim', explode('|', $request->correct_answers)) // Use | as the separator
            : null;

        // Create the question for the main exam
        $question = Question::create([
            'exam_id' => $request->exam_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options ? json_encode($options) : null,
            'points' => $request->points,
            'correct_comment' => $request->correct_comment,
            'wrong_comment' => $request->wrong_comment,
            'answer_key' => $request->answer_key,
            'file_path' => $filePath,
            'category' => $request->category,
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
                    'points' => $request->points, 
                    'correct_comment' => $request->correct_comment,
                    'wrong_comment' => $request->wrong_comment,
                    'answer_key' => $request->answer_key,
                    'file_path' => $filePath,
                    'category' => $request->category,
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

        ActivityLogger::log("Created question of ID:'{$question->id}' for exam {$question->exam->name} by " . auth()->user()->name);

        return redirect()->route('admin.questions.index', ['exam_id' => $request->exam_id])
            ->with('success', 'Question added successfully!');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        $question->delete();

        // Log the action
        ActivityLogger::log("Deleted question '{$question->id}' for exam {$question->exam->name} by " . auth()->user()->name);

        return response()->json(['success' => true, 'message' => 'Question deleted successfully.']);
    }

    public function removeFile($id)
    {
        $question = Question::findOrFail($id);

        if ($question->file_path) {
            if (\Storage::disk('public')->exists($question->file_path)) {
                \Storage::disk('public')->delete($question->file_path); // Delete the file
                $question->update(['file_path' => null]); // Set file_path to null
            }
        }

        ActivityLogger::log("Removed file from question of ID:'{$question->id}' for exam ID {$question->exam->name} by " . auth()->user()->name);

        // Return JSON response with redirect URL
        return response()->json([
            'success' => true,
            'message' => 'File removed successfully!',
            'redirect_url' => route('admin.questions.edit', $id), // Redirect to the edit page
        ]);
    }

    public function getExamCategories(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);
        $categories = Question::where('exam_id', $request->exam_id)
            ->whereNotNull('category')
            ->where('category', '!=', 'local')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();
        return response()->json(['categories' => $categories]);
    }
}
