@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Exam Instructions</h1>
    <p class="mb-4">Please ensure the following before starting the exam:</p>
    <ul class="list-disc pl-6 mb-6">
        <li>Have a continuous internet connection.</li>
        <li>Do not refresh the page during the exam.</li>
        <li>Answer all questions before submitting.</li>
    </ul>
    <a href="{{ route('exam.take', $examName) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Start Exam</a>
</div>
@endsection