@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-black dark:text-white">Manage Questions</h1>
        <a id="add-question-btn" href="{{ route('subdivision.questions.create', ['exam_id' => $exams->first()->id ?? '']) }}" class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Question
        </a>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
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
                @foreach ($subdivisions as $subdivision)
                    <option value="{{ $subdivision }}">{{ $subdivision }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 flex justify-center items-center z-50">
        <svg aria-hidden="true" class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
        </svg>
    </div>

    <!-- Questions Table -->
    <div id="questions-table-container" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6 hidden">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Question</th>
                    <th scope="col" class="px-6 py-3">Type</th>
                    <th scope="col" class="px-6 py-3">Options</th>
                    <th scope="col" class="px-6 py-3">Correct Answers</th>
                    <th scope="col" class="px-6 py-3">Points</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="questions-table-body">
                <!-- Questions will be dynamically loaded here -->
            </tbody>
        </table>
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
        const loadingSpinner = document.getElementById('loading-spinner');
        const subdivisionSelect = document.getElementById('subdivision-select');

        const loadQuestions = async (examId, page = 1) => {
            try {
                loadingSpinner.classList.remove('hidden');
                addQuestionBtn.href = `/subdivision/questions/create?exam_id=${examId}`;
                const response = await fetch('{{ route('api.subdivision.questions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ 
                        exam_id: examId, 
                        subdivision_id: subdivisionSelect ? subdivisionSelect.value : null, 
                        page: page 
                    }),
                });
                if (!response.ok) throw new Error('Failed to fetch questions');
                const data = await response.json();
                renderQuestionsTable(data.questions, data.pagination);
            } catch (error) {
                console.error('Error loading questions:', error);
                alert('Failed to load questions. Please try again.');
            } finally {
                loadingSpinner.classList.add('hidden');
            }
        };

        const renderQuestionsTable = (questions, paginationHtml) => {
            questionsTableBody.innerHTML = questions.map(question => `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">${question.id}</td>
                    <td class="px-6 py-4">${question.question_text}</td>
                    <td class="px-6 py-4">${question.question_type || 'N/A'}</td>
                    <td class="px-6 py-4">${question.options?.join(' | ') || 'N/A'}</td>
                    <td class="px-6 py-4">${question.correct_answers?.join(' | ') || 'N/A'}</td>
                    <td class="px-6 py-4">${question.points}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-4 actions">
                            <a href="/subdivision/questions/${question.id}/edit">
                                <svg class="w-[23px] h-[23px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M15.514 3.293a1 1 0 0 0-1.415 0L12.151 5.24a.93.93 0 0 1 .056.052l6.5 6.5a.97.97 0 0 1 .052.056L20.707 9.9a1 1 0 0 0 0-1.415l-5.193-5.193ZM7.004 8.27l3.892-1.46 6.293 6.293-1.46 3.893a1 1 0 0 1-.603.591l-9.494 3.355a1 1 0 0 1-.98-.18l6.452-6.453a1 1 0 0 0-1.414-1.414l-6.453 6.452a1 1 0 0 1-.18-.98l3.355-9.494a1 1 0 0 1 .591-.603Z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <button 
                                class="text-red-600 hover:underline cursor-pointer delete-question-btn" 
                                data-id="${question.id}" >
                                <svg class="w-[23px] h-[23px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
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

            questionsTableContainer.classList.remove('hidden');

            document.querySelectorAll('.delete-question-btn').forEach(button => {
                button.addEventListener('click', async (event) => {
                    const questionId = button.dataset.id;
                    if (confirm(`Are you sure you want to delete the question?`)) {
                        try {
                            const response = await fetch(`/subdivision/questions/${questionId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });

                            if (!response.ok) throw new Error('Failed to delete question');
                            alert('Question deleted successfully.');
                            loadQuestions(examSelect.value); // Reload questions
                        } catch (error) {
                            console.error('Error deleting question:', error);
                            alert('Failed to delete question. Please try again.');
                        }
                    }
                });
            });
        };

        // On page load, load questions for first exam
        const selectedExamId = examSelect.value;
        loadQuestions(selectedExamId);

        // When exam changes, reload questions
        examSelect.addEventListener('change', () => {
            const examId = examSelect.value;
            history.pushState({ examId }, '', `?exam_id=${examId}`);
            loadQuestions(examId);
        });

        // Handle back/forward navigation
        window.addEventListener('popstate', (event) => {
            const state = event.state;
            if (state && state.examId) {
                examSelect.value = state.examId;
                loadQuestions(state.examId);
            }
        });

        if (subdivisionSelect) {
            subdivisionSelect.addEventListener('change', () => {
                loadQuestions(examSelect.value);
            });
        }
    });
</script>
@endsection