@extends('layouts.app')

@section('content')

<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    @if (session('success'))
        <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 1 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-3" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 1 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                {{ session('error') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-red-800 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif
    <!-- Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Current Rating Card -->
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Current Rating</h5>
            <p class="font-normal text-gray-700 dark:text-gray-400">{{ $user_rating['long'] }} ({{ $user_rating['short'] }})</p>
        </div>

        <!-- Exam Token Status Card -->
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Exam Token Status</h5>
            @if($active_token)
                <p class="font-normal text-gray-700 dark:text-gray-400">Active Exam: {{ $active_token->exam->name }}</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">Expires on: {{ $active_token->expires_at->format('d-m-Y') }}</p>
            @else
                <p class="font-normal text-gray-700 dark:text-gray-400">No active exam tokens.</p>
            @endif
        </div>

        <!-- Subdivision Card --->
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Subdivision</h5>
            <p class="font-normal text-gray-700 dark:text-gray-400">{{ $subdivisionName }} ({{ $subdivisionID }})</p>
        </div>
    </div>

    <!-- Exam Attempts Table -->
    <div class="relative overflow-x-auto shadow-md">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Exam</th>
                    <th scope="col" class="px-6 py-3">Date</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Score</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($exam_attempts as $attempt)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $attempt->exam->name }}</td>
                        <td class="px-6 py-4">{{ $attempt->attempted_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            @if($attempt->status === 'passed')
                                <span class="text-green-500 font-bold">Passed</span>
                            @elseif($attempt->status === 'failed')
                                <span class="text-red-500 font-bold">Failed</span>
                            @else
                                <span class="text-yellow-500 font-bold">Waiting for evaluation</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $attempt->score ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('exam.review', $attempt->id) }}" class="text-blue-400">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white dark:bg-gray-800">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">No exam attempts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection