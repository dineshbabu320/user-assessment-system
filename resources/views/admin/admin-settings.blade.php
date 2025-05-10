@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Exam Settings</h1>
    <form method="POST" action="{{ route('admin.updateSettings') }}">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px">
                @foreach ($exams as $index => $exam)
                    <li class="me-2">
                        <button type="button" class="cursor-pointer inline-block p-4 border-b-2 {{ $index === 0 ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}" data-tab-target="#exam-{{ $exam->id }}">
                            {{ $exam->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="mt-6 max-w-100">
            @foreach ($exams as $index => $exam)
                <div id="exam-{{ $exam->id }}" class="{{ $index === 0 ? '' : 'hidden' }} tab-content">
                    <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">Question Settings</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Total Questions: <span class="font-bold">{{ $exam->questions()->count() }}</span>
                        </p>
                        <div class="mt-4 mb-4">
                            <label for="question_limit_{{ $exam->id }}" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Number of Questions
                            </label>
                            <input type="number" 
                                   name="question_limits[{{ $exam->id }}]" 
                                   id="question_limit_{{ $exam->id }}" 
                                   value="{{ $exam->question_limit ?? ($exam->questions()->count() > 0 ? $exam->questions()->count() : 1) }}" 
                                   min="1" 
                                   max="{{ $exam->questions()->count() > 0 ? $exam->questions()->count() : 1 }}" 
                                   data-question-limit="{{ $exam->question_limit ?? ($exam->questions()->count() > 0 ? $exam->questions()->count() : 1) }}" 
                                   class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                   {{ $exam->questions()->count() === 0 ? 'disabled' : '' }}>
                            @if ($exam->questions()->count() === 0)
                                <p class="text-sm text-yellow-500 mt-2">This exam has no questions. Please add questions before setting the question limit.</p>
                            @endif
                        </div>  
                        @php
                            $categories = $exam->questions->groupBy('category');
                        @endphp
                        @foreach ($categories as $category => $questions)
                            <div class="mb-4">
                                <label for="category_ratio_{{ $exam->id }}_{{ $loop->index }}" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                    {{ $category }} ({{ $questions->count() }} questions)
                                </label>
                                <input type="number" 
                                       name="category_ratios[{{ $exam->id }}][{{ $category }}]" 
                                       id="category_ratio_{{ $exam->id }}_{{ $loop->index }}" 
                                       value="{{ $exam->category_ratios[$category] ?? '' }}" 
                                       min="0" 
                                       max="{{ $exam->question_limit }}" 
                                       class="category-input w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500" 
                                       data-exam-id="{{ $exam->id }}">
                            </div>
                        @endforeach
                        <div id="warning_{{ $exam->id }}" class="text-yellow-500 text-sm hidden">
                            The sum of category questions must equal the total number of questions set.
                        </div>
                        <input type="hidden" id="category_sum_{{ $exam->id }}" name="category_sum[{{ $exam->id }}]" value="0">
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer">
                Save Settings
            </button>
        </div>
    </form>
</div>

<script>
    // Tab functionality
    document.querySelectorAll('[data-tab-target]').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.getAttribute('data-tab-target'));

            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

            // Remove active state from all tabs
            document.querySelectorAll('[data-tab-target]').forEach(tab => tab.classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500'));

            // Show the selected tab content
            target.classList.remove('hidden');

            // Add active state to the clicked tab
            tab.classList.add('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const categoryInputs = document.querySelectorAll('.category-input');
        const totalQuestionsInputs = document.querySelectorAll('[id^="question_limit_"]');

        // Function to validate category ratios
        const validateCategoryRatios = (examId) => {
            const totalQuestionsInput = document.querySelector(`#question_limit_${examId}`);
            const totalQuestions = parseInt(totalQuestionsInput.value, 10) || parseInt(totalQuestionsInput.getAttribute('data-question-limit'), 10);

            const categoryInputsForExam = document.querySelectorAll(`.category-input[data-exam-id="${examId}"]`);
            let sum = 0;

            categoryInputsForExam.forEach(categoryInput => {
                const value = parseInt(categoryInput.value, 10);
                if (!isNaN(value)) {
                    sum += value;
                }
            });

            // Update the hidden input value
            const hiddenInput = document.querySelector(`#category_sum_${examId}`);
            hiddenInput.value = sum;

            // Show or hide the warning message
            const warning = document.querySelector(`#warning_${examId}`);
            if (sum > totalQuestions) {
                warning.textContent = 'The sum of category questions cannot exceed the total number of questions.';
                warning.classList.remove('hidden');
                hiddenInput.setCustomValidity('The sum of category questions cannot exceed the total number of questions.');
            } else if (sum < totalQuestions) {
                warning.textContent = 'The sum of category questions must equal the total number of questions.';
                warning.classList.remove('hidden');
                hiddenInput.setCustomValidity('The sum of category questions must equal the total number of questions.');
            } else {
                warning.classList.add('hidden');
                hiddenInput.setCustomValidity(''); // Clear custom validity
            }
        };

        // Add event listeners to category inputs
        categoryInputs.forEach(input => {
            input.addEventListener('input', () => {
                const examId = input.getAttribute('data-exam-id');
                validateCategoryRatios(examId);
            });
        });

        // Add event listeners to total question inputs
        totalQuestionsInputs.forEach(input => {
            input.addEventListener('input', () => {
                const examId = input.id.replace('question_limit_', '');
                validateCategoryRatios(examId);
            });
        });
    });
</script>
@endsection
