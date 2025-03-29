@extends('layouts.app')

@section('content')

<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mb-6">
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
                <p class="font-normal text-gray-700 dark:text-gray-400">Expires on: {{ $active_token->expires_at->format('Y-m-d H:i') }}</p>
            @else
                <p class="font-normal text-gray-700 dark:text-gray-400">No active exam tokens.</p>
            @endif
        </div>
    </div>

    <!-- Exam Attempts Table -->
    <div class="relative overflow-x-auto">
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
                            @if($attempt->status === 'pass')
                                <span class="text-green-500 font-bold">Passed</span>
                            @elseif($attempt->status === 'fail')
                                <span class="text-red-500 font-bold">Failed</span>
                            @else
                                <span class="text-yellow-500 font-bold">In Progress</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $attempt->score ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($attempt->status === 'in_progress')
                                <a href="{{ route('exam.resume', $attempt->id) }}" class="text-blue-400">Resume</a>
                            @else
                                <a href="{{ route('exam.review', $attempt->id) }}" class="text-blue-400">Review</a>
                            @endif
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