@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Manual Evaluation for {{ $student->name }} ({{ $student->cid }})</h1>
    <form method="POST" action="{{ route('student.evaluateAll', $student->cid) }}">
        @csrf
        @foreach ($responses as $response)
            <div class="mb-6 p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <div class="flex items-start space-x-4 mb-2">
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Question:</span>
                    <div class="flex-1">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">{!! $response->question->parsed_text !!}</span>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300">Student Response:</p>
                <textarea class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $response->response_text }}</textarea>
                
                <!-- Evaluation Dropdown -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Evaluation</label>
                    <select name="responses[{{ $response->id }}][status]" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="correct">Correct</option>
                        <option value="incorrect">Incorrect</option>
                    </select>
                </div>

                <!-- Points Input -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Points (Max: {{ $response->question->points }})</label>
                    <input type="number" name="responses[{{ $response->id }}][points]" min="0" max="{{ $response->question->points }}" step="0.5" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex items-center mt-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                    <div>
                        <span class="font-medium">Answer Key: </span>
                        {{ $response->question->answer_key }}
                    </div>
                </div>

                <!-- Comments -->
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