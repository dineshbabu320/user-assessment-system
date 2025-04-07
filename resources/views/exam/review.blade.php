@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Exam Review: {{ $exam->name }}</h1>
    <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Total Score: {{ $totalScore }}/{{ $maxScore }}</h2>
    </div>
    @foreach ($exam->questions as $question)
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}. {{ $question->question_text }}</h2>
            
            @if ($question->question_type === 'single')
                <ul class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($question->answers as $answer)
                        <li class="w-full border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                            <div class="flex items-center ps-3">
                                <input id="single-{{ $answer->id }}" type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" disabled {{ isset($responses[$question->id]) && $responses[$question->id]->response_text == $answer->id ? 'checked' : '' }}>
                                <label for="single-{{ $answer->id }}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $answer->answer_text }}</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-2 text-gray-900 dark:text-white">
                    Status: 
                    @if (isset($responses[$question->id]))
                        @if ($responses[$question->id]->status === 'correct')
                            <span class="text-green-500 font-bold">Correct</span>
                        @elseif ($responses[$question->id]->status === 'incorrect')
                            <span class="text-red-500 font-bold">Incorrect</span>
                        @else
                            <span class="text-yellow-500 font-bold">Manual Evaluation</span>
                        @endif
                    @else
                        <span class="text-gray-900 dark:text-white font-bold">Not Answered</span>
                    @endif
                </p>
            @elseif ($question->question_type === 'mcq')
                <ul class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($question->answers as $answer)
                        <li class="w-full border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                            <div class="flex items-center ps-3">
                                <input id="mcq-{{ $answer->id }}" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" disabled {{ in_array($answer->id, explode(',', $responses[$question->id]->response_text ?? '')) ? 'checked' : '' }}>
                                <label for="mcq-{{ $answer->id }}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $answer->answer_text }}</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-2 text-gray-900 dark:text-white">
                    Status: 
                    @if ($responses[$question->id]->status === 'correct')
                        <span class="text-green-500 font-bold">Correct</span>
                    @elseif ($responses[$question->id]->status === 'incorrect')
                        <span class="text-red-500 font-bold">Incorrect</span>
                    @else
                        <span class="text-yellow-500 font-bold">Manual Evaluation</span>
                    @endif
                </p>
            @elseif ($question->question_type === 'text')
                <textarea rows="4" class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $responses[$question->id]->response_text ?? 'Not Answered' }}</textarea>
                <p class="mt-2 text-gray-900 dark:text-white">
                    Status: 
                    @if (isset($responses[$question->id]))
                        @if ($responses[$question->id]->status === 'correct')
                            <span class="text-green-500 font-bold">Correct</span>
                        @elseif ($responses[$question->id]->status === 'incorrect')
                            <span class="text-red-500 font-bold">Incorrect</span>
                        @else
                            <span class="text-yellow-500 font-bold">Manual Evaluation</span>
                        @endif
                    @else
                        <span class="text-gray-500 font-bold">Not Answered</span>
                    @endif
                </p>
                @if (isset($responses[$question->id]) && ($responses[$question->id]->status === 'correct' || $responses[$question->id]->status === 'incorrect'))
                    <p class="text-black dark:text-white">Mentor Comments: {{ $responses[$question->id]->comments ?? 'No comments' }}</p>
                @endif
            @endif
        </div>
    @endforeach
</div>
@endsection