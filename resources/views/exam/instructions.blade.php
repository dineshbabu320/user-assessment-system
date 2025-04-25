@extends('layouts.app')

@section('content')
<div id="main-content" class="flex items-center justify-center min-h-screen text-gray-900 dark:text-white">
    <div class="text-center">
        <h1 class="text-2xl font-bold mb-6">Exam Instructions</h1>
        <p class="mb-4">Please ensure the following before starting the exam:</p>
        <ul class="list-disc pl-6 mb-6 text-center inline-block">
            <li>Have a continuous internet connection.</li>
            <li>Do not refresh the page during the exam.</li>
            <li>Answer all questions before submitting.</li>
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