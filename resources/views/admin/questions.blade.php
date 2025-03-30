@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-4 text-black dark:text-white">Manage Questions</h1>

    <!-- Exam Selection -->
    <div class="mb-6">
        <label for="exam-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Exam</label>
        <select id="exam-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
            <option selected disabled>Select an exam</option>
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Add Question Button -->
    <a id="add-question-btn" href="#" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Add Question
    </a>

    <!-- Questions Table -->
    <div id="questions-table-container" class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6 hidden">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Elements
        const examSelect = document.getElementById('exam-select');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionsTableBody = document.getElementById('questions-table-body');
        const questionsTableContainer = document.getElementById('questions-table-container');

        // Load questions for selected exam
        examSelect.addEventListener('change', async () => {
            try {
                const examId = examSelect.value;

                console.log('Selected exam ID:', examId);

                // Enable the "Add Question" button and set its href
                addQuestionBtn.href = `/admin/questions/create/${examId}`;
                addQuestionBtn.classList.remove('hidden');

                // Fetch questions for the selected exam
                const response = await fetch('/api/exams/questions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ exam_id: examId }),
                });

                console.log('Fetch response status:', response.status);

                if (!response.ok) throw new Error('Failed to fetch questions');
                const questions = await response.json();
                console.log('Fetched questions:', questions);
                renderQuestionsTable(questions);
            } catch (error) {
                console.error('Error loading questions:', error);
                alert('Failed to load questions');
            }
        });

        // Render questions table
        const renderQuestionsTable = (questions) => {
            console.log('Rendering questions table with data:', questions);

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

            console.log('Questions table rendered successfully.');

            // Show the table container if there are questions
            questionsTableContainer.classList.toggle('hidden', questions.length === 0);
        };
    });
</script>
@endsection