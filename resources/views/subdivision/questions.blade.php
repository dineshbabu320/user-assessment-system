@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-black dark:text-white">Manage Questions</h1>
        <a id="add-question-btn" href="{{ route('subdivision.questions.create', ['exam_id' => $exams->first()->id ?? '']) }}" 
           class="flex items-center justify-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 whitespace-nowrap w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Question
        </a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 shadow-md">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="exam-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Exam</label>
                <select id="exam-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id', $exams->first()->id) == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if (auth()->user()->isAdmin())
            <div class="flex-1">
                <label for="subdivision-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Subdivision</label>
                <select id="subdivision-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="">All Subdivisions</option>
                </select>
            </div>
            @endif
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 shadow-md">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Questions</h2>
                </div>
                <div id="results-count" class="hidden">
                    <span id="results-text" class="text-sm px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full font-medium whitespace-nowrap"></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative group flex-1 sm:flex-none">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>
                    <input type="text" id="search-input" placeholder="Type to search questions..." 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-64 pl-12 pr-12 py-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all duration-300">
                    <button type="button" id="clear-search" class="absolute inset-y-0 right-0 items-center pr-4 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-indicator" class="hidden">
        <div class="flex items-center justify-center py-8">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-4 border-blue-200 dark:border-gray-600"></div>
                <div class="w-12 h-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin absolute top-0 left-0"></div>
            </div>
            <span class="ml-3 text-gray-600 dark:text-gray-300 font-medium">Loading questions...</span>
        </div>
    </div>

    <div id="questions-table-container" class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" style="min-width: 800px;">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold w-20">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Question</th>
                        <th scope="col" class="px-6 py-4 font-semibold w-24">Type</th>
                        @can('admin')
                            <th scope="col" class="px-6 py-4 font-semibold w-28">Subdivision</th>
                        @endcan
                        <th scope="col" class="px-6 py-4 font-semibold w-24">Points</th>
                        <th scope="col" class="px-6 py-4 font-semibold w-28">Actions</th>
                    </tr>
                </thead>
                <tbody id="questions-table-body">
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="no-results" class="hidden text-center py-12">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No questions found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4" id="no-results-message">Try adjusting your search criteria or add a new question.</p>
    </div>
    
    <div id="pagination-container" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const examSelect = document.getElementById('exam-select');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionsTableBody = document.getElementById('questions-table-body');
        const questionsTableContainer = document.getElementById('questions-table-container');
        const loadingIndicator = document.getElementById('loading-indicator');
        const subdivisionSelect = document.getElementById('subdivision-select');

        const loadSubdivisions = async (examId) => {
            if (!subdivisionSelect) return;
            
            try {
                const response = await fetch('{{ route('subdivision.exams.subdivisions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ exam_id: examId }),
                });
                
                const data = await response.json();
                subdivisionSelect.innerHTML = '<option value="">All Subdivisions</option>' +
                    data.subdivisions.map(sub => `<option value="${sub}">${sub}</option>`).join('');
            } catch (error) {
                console.error('Error loading subdivisions:', error);
                subdivisionSelect.innerHTML = '<option value="">All Subdivisions</option>';
            }
        };

        const loadQuestions = async (examId, page = 1) => {
            try {
                loadingIndicator.classList.remove('hidden');
                questionsTableContainer.classList.add('hidden');
                
                addQuestionBtn.href = `/subdivision/questions/create?exam_id=${examId}`;
                
                const searchInput = document.getElementById('search-input');
                const searchTerm = searchInput ? searchInput.value.trim() : '';
                
                const response = await fetch('{{ route('api.subdivision.questions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ 
                        exam_id: examId, 
                        subdivision_id: subdivisionSelect ? subdivisionSelect.value : null, 
                        search: searchTerm,
                        page: page 
                    }),
                });
                
                if (!response.ok) throw new Error('Failed to fetch questions');
                const data = await response.json();
                renderQuestionsTable(data.questions, data.pagination);
                
                const resultsCount = document.getElementById('results-count');
                const resultsText = document.getElementById('results-text');
                const noResults = document.getElementById('no-results');
                const noResultsMessage = document.getElementById('no-results-message');
                
                if (data.questions.length === 0) {
                    questionsTableContainer.classList.add('hidden');
                    noResults.classList.remove('hidden');
                    resultsCount.classList.add('hidden');
                    
                    if (searchTerm) {
                        noResultsMessage.textContent = `No questions found matching "${searchTerm}". Try adjusting your search criteria.`;
                    } else {
                        noResultsMessage.textContent = 'No questions found for this exam. Add a new question to get started.';
                    }
                } else {
                    noResults.classList.add('hidden');
                    questionsTableContainer.classList.remove('hidden');
                    
                    if (resultsCount && resultsText) {
                        if (searchTerm) {
                            resultsText.textContent = `${data.total} result${data.total !== 1 ? 's' : ''} for "${searchTerm}"`;
                        } else {
                            resultsText.textContent = `${data.total} question${data.total !== 1 ? 's' : ''} total`;
                        }
                        resultsCount.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error loading questions:', error);
                const noResults = document.getElementById('no-results');
                const noResultsMessage = document.getElementById('no-results-message');
                questionsTableContainer.classList.add('hidden');
                noResults.classList.remove('hidden');
                noResultsMessage.textContent = 'Failed to load questions. Please try again.';
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        };

        const renderQuestionsTable = (questions, paginationHtml) => {
            const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
            
            questionsTableBody.innerHTML = questions.map(question => `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300 rounded-full flex-shrink-0">
                            ${question.id}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-md">
                            <p class="font-medium text-gray-900 dark:text-white leading-relaxed line-clamp-2" title="${question.question_text}">
                                ${question.question_text}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ${question.question_type || 'N/A'}
                        </span>
                    </td>
                    ${isAdmin ? `<td class="px-6 py-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ${question.subdivision_id || 'N/A'}
                        </span>
                    </td>` : ''}
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ${question.points}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="/subdivision/questions/${question.id}/edit" 
                               class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors duration-200 flex-shrink-0" 
                               title="Edit question">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-colors duration-200 flex-shrink-0 delete-question-btn cursor-pointer" 
                                    data-id="${question.id}" 
                                    title="Delete question">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = paginationHtml;

            paginationContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    loadQuestions(examSelect.value, page);
                });
            });

            document.querySelectorAll('.delete-question-btn').forEach(button => {
                button.addEventListener('click', async (event) => {
                    const questionId = button.dataset.id;
                    if (confirm(`Are you sure you want to delete this question?`)) {
                        try {
                            const response = await fetch(`/subdivision/questions/${questionId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });

                            if (!response.ok) throw new Error('Failed to delete question');
                            alert('Question deleted successfully.');
                            loadQuestions(examSelect.value);
                        } catch (error) {
                            console.error('Error deleting question:', error);
                            alert('Failed to delete question. Please try again.');
                        }
                    }
                });
            });
        };

        // Helper function for question type colors (the cool badges!)
        function getTypeColor(type) {
            const colors = {
                'mcq': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'single': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'text': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
            };
            return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        }

        // Initialize - load subdivisions first, then questions
        const selectedExamId = examSelect.value;
        if (subdivisionSelect) {
            loadSubdivisions(selectedExamId).then(() => {
                loadQuestions(selectedExamId);
            });
        } else {
            loadQuestions(selectedExamId);
        }

        // When exam changes, reload subdivisions then questions
        examSelect.addEventListener('change', () => {
            const examId = examSelect.value;
            if (subdivisionSelect) {
                loadSubdivisions(examId).then(() => {
                    history.pushState({ examId }, '', `?exam_id=${examId}`);
                    loadQuestions(examId);
                });
            } else {
                history.pushState({ examId }, '', `?exam_id=${examId}`);
                loadQuestions(examId);
            }
        });

        // When subdivision changes, just reload questions
        if (subdivisionSelect) {
            subdivisionSelect.addEventListener('change', () => {
                loadQuestions(examSelect.value);
            });
        }

        // Enhanced search functionality with clear button
        const searchInput = document.getElementById('search-input');
        const clearSearchBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            let searchTimeout;
            
            // Show/hide clear button based on input content
            const toggleClearButton = () => {
                if (searchInput.value.trim()) {
                    clearSearchBtn.classList.remove('hidden');
                    clearSearchBtn.classList.add('flex');
                } else {
                    clearSearchBtn.classList.add('hidden');
                    clearSearchBtn.classList.remove('flex');
                }
            };

            // Search input event
            searchInput.addEventListener('input', () => {
                toggleClearButton();
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadQuestions(examSelect.value);
                }, 300); // Debounce search for 300ms
            });

            // Clear search button event
            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                toggleClearButton();
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadQuestions(examSelect.value);
                }, 300);
            });

            // Initial state
            toggleClearButton();
        }

        // Handle back/forward navigation
        window.addEventListener('popstate', (event) => {
            const state = event.state;
            if (state && state.examId) {
                examSelect.value = state.examId;
                if (subdivisionSelect) {
                    loadSubdivisions(state.examId).then(() => {
                        loadQuestions(state.examId);
                    });
                } else {
                    loadQuestions(state.examId);
                }
            }
        });
    });
</script>
@endsection