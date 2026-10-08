@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-black dark:text-white">Manage Questions</h1>
        <a id="add-question-btn" href="{{ route('subdivision.questions.create', ['exam_id' => request('exam_id', $exams->first()->id ?? '')]) }}" 
           class="flex items-center justify-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 whitespace-nowrap w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Question
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 shadow-md">
        <form id="filter-form" method="GET" action="{{ route('subdivision.questions.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="exam-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Exam</label>
                <select id="exam-select" name="exam_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" onchange="document.getElementById('filter-form').submit();">
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
                <select id="subdivision-select" name="subdivision_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" onchange="document.getElementById('filter-form').submit();">
                    <option value="">All Subdivisions</option>
                    @foreach ($subdivisions as $subdivision)
                        <option value="{{ $subdivision }}" {{ request('subdivision_id') == $subdivision ? 'selected' : '' }}>
                            {{ $subdivision }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
        <div class="overflow-x-auto">
            <table id="questions-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
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
                <tbody>
                    @foreach($questions as $question)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300 rounded-full flex-shrink-0">
                                {{ $question->id }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-md">
                                <p class="font-medium text-gray-900 dark:text-white leading-relaxed line-clamp-2" title="{{ strip_tags($question->question_text) }}">
                                    {!! strip_tags($question->question_text) !!}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $question->question_type ?? 'N/A' }}
                            </span>
                        </td>
                        @can('admin')
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $question->subdivision_id ?? 'N/A' }}
                            </span>
                        </td>
                        @endcan
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $question->points }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('subdivision.questions.edit', $question->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors duration-200 flex-shrink-0" 
                                   title="Edit question">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-colors duration-200 flex-shrink-0 delete-question-btn cursor-pointer" 
                                        data-id="{{ $question->id }}" 
                                        title="Delete question">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize simple-datatables
        if (document.getElementById("questions-table") && typeof simpleDatatables !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#questions-table", {
                searchable: true,
                fixedHeight: true
            });
            
            // Allow clicking category dropdown to search immediately (if desired in future)
            // Example:
            // const subSelect = document.getElementById('subdivision-select');
            // if(subSelect) subSelect.addEventListener('change', () => dataTable.search(subSelect.value));
        }

        // Handle deletions
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
                        window.location.reload();
                    } catch (error) {
                        console.error('Error deleting question:', error);
                        alert('Failed to delete question. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endsection