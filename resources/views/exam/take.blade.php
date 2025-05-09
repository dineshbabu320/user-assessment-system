@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Exam: {{ $exam->name }}</h1>
    <form method="POST" action="{{ route('exam.submit', $exam->name) }}">
        @csrf
        @foreach ($exam->questions as $question)
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center justify-between">
                    <span>{{ $loop->iteration }}. {{ $question->question_text }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Points: {{ $question->points }}</span>
                </h2>

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
                    <ul class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                        @foreach ($question->answers as $answer)
                            <li class="w-full border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                                <div class="flex items-center ps-3">
                                    <input id="single-{{ $answer->id }}" type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="single-{{ $answer->id }}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $answer->answer_text }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @elseif ($question->question_type === 'mcq')
                    <ul class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                        @foreach ($question->answers as $answer)
                            <li class="w-full border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                                <div class="flex items-center ps-3">
                                    <input id="mcq-{{ $answer->id }}" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="mcq-{{ $answer->id }}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $answer->answer_text }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @elseif ($question->question_type === 'text')
                    <label for="text-{{ $question->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Answer</label>
                    <textarea id="text-{{ $question->id }}" name="answers[{{ $question->id }}]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your answer here..."></textarea>
                @endif
            </div>
        @endforeach
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg cursor-pointer">Submit Exam</button>
    </form>
</div>
@endsection