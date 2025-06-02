@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    @if ($attempt->status === 'in_progress')
        <div class="flex items-center justify-center min-h-screen w-full bg-gray-100 dark:bg-gray-700">
            <div class="inline-flex items-center p-4 mb-4 text-lg text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <span>
                    You have not completed this exam yet. Please finish the exam to view the review.
                </span>
            </div>
        </div>
    @else
        <div class="flex flex-col md:flex-row gap-10 justify-between text-2xl font-bold mb-10">
            <div class="text-gray-900 dark:text-white">Exam Review: {{ $exam->name }}</div>
            <div>
                <h2 class="text-gray-900 dark:text-white">
                    Status: 
                    @if ($attempt->status === 'passed')
                        <span class="text-green-500 font-bold">Passed</span>
                    @elseif ($attempt->status === 'failed')
                        <span class="text-red-500 font-bold">Failed</span>
                    @elseif ($attempt->status === 'in_progress')
                        <span class="text-yellow-500 font-bold">In Progress</span>
                    @else
                        <span class="text-yellow-500 font-bold">Waiting for Manual Evaluation</span>
                    @endif
                </h2>
            </div>
            @if ($attempt->status !== 'waiting_for_manual_evaluation')
                <div>
                    <h2 class="text-gray-900 dark:text-white">
                        Total Score: {{ $totalScore }}/{{ $maxScore }} 
                        ({{ $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0 }}%)
                    </h2>
                </div>
            @endif
        </div>
        <hr class="h-px my-6 bg-black border-0 dark:bg-white">
        @foreach ($questions as $question)
            <div class="mb-8">
                <div class="flex items-start space-x-4 mb-5">
                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}.</span>
                    <div class="flex-1">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">{!! $question->parsed_text !!}</span>
                    </div>
                </div>

                <!-- Display Image or PDF -->
                @if ($question->file_path)
                    <div class="mt-4 mb-4">
                        @if (Str::endsWith($question->file_path, ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $question->file_path) }}" alt="Question Image" class="max-w-md h-auto rounded-lg shadow-md">
                        @elseif (Str::endsWith($question->file_path, 'pdf'))
                            <a href="{{ asset('storage/' . $question->file_path) }}" target="_blank" class="text-blue-600 hover:underline">View PDF</a>
                        @endif
                    </div>
                @endif

                @if ($question->question_type === 'single')
                    <ul class="w-auto shadow-md text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:shadow-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Comment: 
                                    @if ($responses[$question->id]->comments)
                                        <span class="font-bold">{{ $responses[$question->id]->comments }}</span>
                                    @else
                                        {{ $question->correct_comment }}
                                    @endif
                                </p>
                            @elseif ($responses[$question->id]->status === 'incorrect')
                                <span class="text-red-500 font-bold">Incorrect</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Comment: 
                                    @if ($responses[$question->id]->comments)
                                        <span class="font-bold">{{ $responses[$question->id]->comments }}</span>
                                    @else
                                        {{ $question->wrong_comment }}
                                    @endif
                                </p>
                            @endif
                        @else
                            <span class="text-gray-900 dark:text-white font-bold">Not Answered</span>
                        @endif
                    </p>
                @elseif ($question->question_type === 'mcq')
                    <ul class="w-auto shadow-md text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:shadow-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Comment: 
                                @if ($responses[$question->id]->comments)
                                    <span class="font-bold">{{ $responses[$question->id]->comments }}</span>
                                @else
                                    {{ $question->correct_comment }}
                                @endif
                            </p>
                        @elseif ($responses[$question->id]->status === 'incorrect')
                            <span class="text-red-500 font-bold">Incorrect</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Comment: 
                                @if ($responses[$question->id]->comments)
                                    <span class="font-bold italic">{{ $responses[$question->id]->comments }}</span>
                                @else
                                    {{ $question->wrong_comment }}
                                @endif
                            </p>
                        @endif
                    </p>
                @elseif ($question->question_type === 'text')
                    <textarea rows="4" class="w-full bg-gray-100 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>{{ $responses[$question->id]->response_text ?? 'Not Answered' }}</textarea>
                    <p class="mt-2 text-gray-900 dark:text-white">
                        Status: 
                        @if (isset($responses[$question->id]))
                            @if ($responses[$question->id]->status === 'correct')
                                <span class="text-green-500 font-bold">Correct</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Comment: 
                                    @if ($responses[$question->id]->comments)
                                        <span class="font-bold italic">{{ $responses[$question->id]->comments }}</span>
                                    @else
                                        {{ $question->correct_comment }}
                                    @endif
                                </p>
                            @elseif ($responses[$question->id]->status === 'incorrect')
                                <span class="text-red-500 font-bold">Incorrect</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Comment: 
                                    @if ($responses[$question->id]->comments)
                                        <span class="font-bold italic">{{ $responses[$question->id]->comments }}</span>
                                    @else
                                        {{ $question->wrong_comment }}
                                    @endif
                                </p>
                            @else
                                <span class="text-yellow-500 font-bold">Manual Evaluation</span>
                            @endif
                        @else
                            <span class="text-gray-500 font-bold">Not Answered</span>
                        @endif
                    </p>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection