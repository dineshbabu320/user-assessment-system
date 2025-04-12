@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-4 text-black dark:text-white">Manage Questions</h1>

    <!-- Exam Selection -->
    <div class="mb-6">
        <label for="exam-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Exam</label>
        <select id="exam-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}" {{ request('exam_id', $exams->first()->id) == $exam->id ? 'selected' : '' }}>
                    {{ $exam->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Add Question Button -->
    <a id="add-question-btn" href="{{ route('admin.questions.create', ['exam_id' => $exams->first()->id ?? '']) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Add Question
    </a>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="flex fixed inset-0 justify-center items-center z-50">
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
                    <th scope="col" class="px-6 py-3">Question</th>
                    <th scope="col" class="px-6 py-3">Type</th>
                    <th scope="col" class="px-6 py-3">Options</th>
                    <th scope="col" class="px-6 py-3">Correct Answers</th>
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

        const loadQuestions = async (examId, page = 1) => {
            try {
                loadingSpinner.classList.remove('hidden');

                // Update the "Add Question" button link
                addQuestionBtn.href = `/admin/questions/create/${examId}`;

                // Fetch questions for the selected exam with pagination
                const response = await fetch('{{ route('api.exams.questions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ exam_id: examId, page: page }),
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
            // Render the questions in the table
            questionsTableBody.innerHTML = questions.map(question => `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">${question.question_text}</td>
                    <td class="px-6 py-4">${question.question_type || 'N/A'}</td>
                    <td class="px-6 py-4">${question.options?.join(', ') || 'N/A'}</td>
                    <td class="px-6 py-4">${question.correct_answers?.join(', ') || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <a href="/admin/questions/${question.id}/edit" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
            `).join('');

            // Render pagination links
            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = paginationHtml;

            // Add event listeners to pagination links
            paginationContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    loadQuestions(examSelect.value, page);
                });
            });

            questionsTableContainer.classList.remove('hidden');
        };

        // Automatically load questions for the first exam or the selected exam
        const selectedExamId = examSelect.value;
        if (selectedExamId) {
            loadQuestions(selectedExamId);
        }

        // Load questions when the exam is changed
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
    });
</script>
@endsection