@extends('layouts.app')

@section('content')
<div id="main-content" class="flex items-center justify-center min-h-screen text-gray-900 dark:text-white">
    <div class="text-center">
        <h1 class="text-2xl font-bold mb-6">Exam Instructions</h1>
        <p class="mb-4">Please ensure the following before starting the exam:</p>
        <ul class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                Have a continuous internet connection.
            </li>
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                Do not refresh the page during the exam.
            </li>
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-gray-500 dark:text-gray-400 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                Answer all questions before submitting.
            </li>
            <strong>Note: This application has not yet been designed to auto-save your answers.</strong>
        </ul>
        <div>
            <a href="{{ route('exam.take', $examName) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Start Exam
            </a>
        </div>
    </div>
</div>
@endsection