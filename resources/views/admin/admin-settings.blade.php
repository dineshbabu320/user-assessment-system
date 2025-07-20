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
                            // Get unique categories with question counts
                            $categories = $exam->questions()
                                ->selectRaw('category, COUNT(*) as question_count')
                                ->whereNotNull('category')
                                ->where('category', '!=', '')
                                ->groupBy('category')
                                ->get()
                                ->pluck('question_count', 'category')
                                ->toArray();
                            
                            // Always include 'local' category even if no questions exist
                            if (!isset($categories['local'])) {
                                $categories['local'] = 0;
                            }
                            
                            // Sort categories to show 'local' last
                            uksort($categories, function($a, $b) {
                                if ($a === 'local') return 1;
                                if ($b === 'local') return -1;
                                return strcmp($a, $b);
                            });
                        @endphp
                        
                        @foreach ($categories as $category => $questionCount)
                            <div class="mb-4">
                                <label for="category_ratio_{{ $exam->id }}_{{ $category }}" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                    {{ $category === 'local' ? 'Local Questions' : ucfirst($category) }} 
                                    @if($category !== 'local')
                                        <span class="text-gray-500 font-normal">({{ $questionCount }} available)</span>
                                    @endif
                                </label>
                                <input type="number"
                                       name="category_ratios[{{ $exam->id }}][{{ $category }}]"
                                       id="category_ratio_{{ $exam->id }}_{{ $category }}"
                                       value="{{ $exam->category_ratios[$category] ?? 0 }}"
                                       min="0"
                                       max="{{ min($questionCount, $exam->question_limit ?? $exam->questions()->count()) }}"
                                       class="category-input w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                       data-exam-id="{{ $exam->id }}"
                                       data-available="{{ $questionCount }}">
                                @if($questionCount === 0)
                                    <p class="text-sm text-yellow-500 mt-1">No questions available in this category</p>
                                @endif
                            </div>
                        @endforeach
                        
                        <div id="warning_{{ $exam->id }}" class="text-yellow-500 text-sm hidden">
                            The sum of category questions must equal the total number of questions set.
                        </div>
                        <div id="error_{{ $exam->id }}" class="text-red-500 text-sm hidden">
                            Some categories don't have enough questions available.
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
            document.querySelectorAll('[data-tab-target]').forEach(tab => tab.classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500'));
            target.classList.remove('hidden');
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
            let hasInsufficientQuestions = false;

            categoryInputsForExam.forEach(categoryInput => {
                const value = parseInt(categoryInput.value, 10);
                const available = parseInt(categoryInput.getAttribute('data-available'), 10);
                
                if (!isNaN(value)) {
                    sum += value;
                    // Check if requested questions exceed available
                    if (value > available) {
                        hasInsufficientQuestions = true;
                    }
                }
            });

            // Update the hidden input value
            const hiddenInput = document.querySelector(`#category_sum_${examId}`);
            hiddenInput.value = sum;

            // Show or hide warning/error messages
            const warning = document.querySelector(`#warning_${examId}`);
            const error = document.querySelector(`#error_${examId}`);
            
            // Reset all messages
            warning.classList.add('hidden');
            error.classList.add('hidden');
            hiddenInput.setCustomValidity('');

            if (hasInsufficientQuestions) {
                error.textContent = 'Some categories don\'t have enough questions available.';
                error.classList.remove('hidden');
                hiddenInput.setCustomValidity('Some categories don\'t have enough questions available.');
            } else if (sum > totalQuestions) {
                warning.textContent = 'The sum of category questions cannot exceed the total number of questions.';
                warning.classList.remove('hidden');
                hiddenInput.setCustomValidity('The sum of category questions cannot exceed the total number of questions.');
            } else if (sum < totalQuestions) {
                warning.textContent = 'The sum of category questions must equal the total number of questions.';
                warning.classList.remove('hidden');
                hiddenInput.setCustomValidity('The sum of category questions must equal the total number of questions.');
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
                
                // Update max attributes for category inputs
                const newLimit = parseInt(input.value, 10);
                const categoryInputs = document.querySelectorAll(`.category-input[data-exam-id="${examId}"]`);
                categoryInputs.forEach(categoryInput => {
                    const available = parseInt(categoryInput.getAttribute('data-available'), 10);
                    categoryInput.setAttribute('max', Math.min(available, newLimit));
                });
            });
        });

        // Initial validation for all exams
        totalQuestionsInputs.forEach(input => {
            const examId = input.id.replace('question_limit_', '');
            validateCategoryRatios(examId);
        });
    });
</script>
@endsection
