@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Manual Evaluation for {{ $student->name }} ({{ $student->cid }})</h1>
    <form method="POST" action="{{ route('student.evaluateAll', $student->cid) }}">
        @csrf
        @foreach ($responses as $response)
            <div class="mb-6 p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Question: {{ $response->question->question_text }}</h2>
                <p class="text-gray-700 dark:text-gray-300">Student Response:</p>
                <textarea class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $response->response_text }}</textarea>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Evaluation</label>
                    <select name="responses[{{ $response->id }}][status]" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="correct">Correct</option>
                        <option value="incorrect">Incorrect</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Comments</label>
                    <textarea name="responses[{{ $response->id }}][comments]" rows="3" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
            </div>
        @endforeach
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer">Submit All Evaluations</button>
    </form>
</div>
@endsection