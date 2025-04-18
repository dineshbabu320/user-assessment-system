@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 id="page-title" class="text-2xl font-bold mb-4 text-black dark:text-white">
        {{ isset($question) ? 'Edit Question' : 'Add Question' }}
    </h1>

    <form id="question-form" method="POST" action="{{ isset($question) ? route('admin.questions.update', $question->id) : route('admin.questions.store') }}">
        @csrf
        @if (isset($question))
            @method('PUT')
        @endif

        <input type="hidden" name="exam_id" value="{{ $exam_id }}">

        <!-- Question Text -->
        <div class="mb-4">
            <label for="question_text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Question Text <span class="text-red-500">*</span></label>
            <textarea name="question_text" id="question_text" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
        </div>

        <!-- Question Type -->
        <div class="mb-4">
            <label for="question_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Question Type <span class="text-red-500">*</span></label>
            <select name="question_type" id="question_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                <option disabled {{ !isset($question) ? 'selected' : '' }}>Select Question Type</option>
                <option value="mcq" {{ old('question_type', $question->question_type ?? '') === 'mcq' ? 'selected' : '' }}>MCQ</option>
                <option value="single" {{ old('question_type', $question->question_type ?? '') === 'single' ? 'selected' : '' }}>Single Option</option>
                <option value="text" {{ old('question_type', $question->question_type ?? '') === 'text' ? 'selected' : '' }}>Text</option>
            </select>
        </div>

        <!-- Options -->
        <div id="options-container" class="mb-4 {{ isset($question) && in_array($question->question_type, ['mcq', 'single']) ? '' : 'hidden' }}">
            <label for="options" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Options (comma-separated)</label>
            <textarea name="options" id="options" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">{{ old('options', isset($question->options) ? implode(',', json_decode($question->options)) : '') }}</textarea>
        </div>

        <!-- Correct Answers -->
        <div id="correct-answers-container" class="mb-4 {{ isset($question) && in_array($question->question_type, ['mcq', 'single']) ? '' : 'hidden' }}">
            <label for="correct_answers" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correct Answers (comma-separated)</label>
            <textarea name="correct_answers" id="correct_answers" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">{{ old('correct_answers', isset($question->answers) ? implode(',', $question->answers->where('is_correct', true)->pluck('answer_text')->toArray()) : '') }}</textarea>
        </div>

        <!-- Points -->
        <div class="mb-4">
            <label for="points" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Points <span class="text-red-500">*</span></label>
            <input type="number" name="points" id="points" value="{{ old('points', $question->points ?? 1) }}" required
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Save Button -->
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 cursor-pointer">
            Save
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const questionTypeSelect = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options-container');
        const correctAnswersContainer = document.getElementById('correct-answers-container');

        // Toggle options and correct answers fields based on question type
        questionTypeSelect.addEventListener('change', (e) => {
            const type = e.target.value;
            if (type === 'mcq' || type === 'single') {
                optionsContainer.classList.remove('hidden');
                correctAnswersContainer.classList.remove('hidden');
            } else {
                optionsContainer.classList.add('hidden');
                correctAnswersContainer.classList.add('hidden');
            }
        });
    });
</script>
@endsection