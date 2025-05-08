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

        @if (!isset($question))
        <!-- Additional Exams -->
        <div class="relative mb-4">
            <label for="additional_exams" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Add to Additional Exams</label>
            <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Select Exams
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div id="dropdownSearch" class="hidden z-50 absolute bg-white rounded-lg shadow-sm w-60 dark:bg-gray-700">
                <div class="p-3">
                    <label for="input-group-search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="input-group-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search exams">
                    </div>
                </div>
                <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
                    @foreach ($allExams as $exam)
                        <li>
                            <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="exam_{{ $exam->id }}" type="checkbox" name="additional_exams[]" value="{{ $exam->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="exam_{{ $exam->id }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">{{ $exam->name }}</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

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

        const dropdownButton = document.getElementById('dropdownSearchButton');
        const dropdownMenu = document.getElementById('dropdownSearch');
        const searchInput = document.getElementById('input-group-search');
        const examListItems = dropdownMenu.querySelectorAll('li');

        // Toggle dropdown visibility
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Filter exams based on search input
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();

            examListItems.forEach((item) => {
                const label = item.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchTerm)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
</script>
@endsection