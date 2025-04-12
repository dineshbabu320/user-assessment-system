@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">My Exams</h1>

    @if($active_tokens->isEmpty())
        <p class="text-gray-700 dark:text-gray-300">You have no active exams at the moment.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($active_tokens as $token)
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $token->exam->name }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Expires on: {{ $token->expires_at->format('d-m-Y') }}</p>
                    <a href="{{ route('exam.instructions', $token->exam->name) }}" class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                        Take Exam
                        <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection