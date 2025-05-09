@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Manual Evaluation for {{ $attempt->exam->name }}</h1>
    <form method="POST" action="{{ route('manual-evaluation-admin.submit', $attempt->id) }}">
        @csrf
        @foreach ($attempt->responses as $response)
            <div class="mb-6 p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Question: {{ $response->question->question_text }}</h2>
                <p class="text-gray-700 dark:text-gray-300">Student Response:</p>
                @if ($response->answer)
                    <!-- Display the answer text for valid Answer IDs -->
                    @if ($response->question->question_type=='mcq')
                        @php
                            $responseIds = explode(',', $response->response_text);
                            $answers = \App\Models\Answer::whereIn('id', $responseIds)->pluck('answer_text');
                            $answers = implode(', ', $answers->toArray());
                        @endphp
                        <textarea class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $answers }}</textarea>
                    @else
                        <textarea class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $response->answer->answer_text }}</textarea>
                    @endif
                @else (!is_null($response->response_text))
                    <!-- Display the raw response text for text-based questions -->
                    <textarea class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $response->response_text }}</textarea>
                @endif
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Evaluation</label>
                    <select name="responses[{{ $response->id }}][status]" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="correct" {{ $response->status === 'correct' ? 'selected' : '' }}>Correct</option>
                        <option value="incorrect" {{ $response->status === 'incorrect' ? 'selected' : '' }}>Incorrect</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Points Awarded</label>
                    <input type="number" name="responses[{{ $response->id }}][points]" value="{{ $response->points_awarded ?? $response->question->points }}" step="0.01" min="0" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">Comments</label>
                    <textarea name="responses[{{ $response->id }}][comments]" rows="3" class="w-full bg-gray-50 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $response->comments }}</textarea>
                </div>
            </div>
        @endforeach
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer">Submit Evaluation</button>
    </form>
</div>
@endsection
