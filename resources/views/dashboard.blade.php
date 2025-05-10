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
        <div class="flex justify-between items-center p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Current Rating</h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">{{ $user_rating['long'] }} ({{ $user_rating['short'] }})</p>
            </div>
            <div>
                <!-- Placeholder for Icon -->
                <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Exam Token Status Card -->
        <div class="flex justify-between items-center p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Exam Token Status</h5>
                @if($active_token)
                    <p class="font-normal text-gray-700 dark:text-gray-400">Active Exam: {{ $active_token->exam->name }}</p>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Expires on: {{ $active_token->expires_at->format('d-m-Y') }}</p>
                @else
                    <p class="font-normal text-gray-700 dark:text-gray-400">No active exam tokens.</p>
                @endif
            </div>
            <div>
                <!-- Updated SVG -->
                <svg class="w-[35px] h-[35px] text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" stroke="currentColor">
                    <g>
                        <g>
                            <path d="M409.223,102.777c-20.476-20.474-47.744-31.749-76.781-31.749c-29.038,0-56.306,11.276-76.781,31.749L102.777,255.661
                                c-42.337,42.338-42.337,111.225,0,153.562c20.475,20.474,47.743,31.749,76.781,31.749c29.038,0,56.306-11.276,76.781-31.749
                                l152.884-152.884C451.56,214.001,451.56,145.115,409.223,102.777z M114.282,295.845l160.054-160.054l21.508,21.508L135.79,317.353
                                L114.282,295.845z M159.482,374.025l-21.508-21.508l167.223-167.223l21.508,21.508L159.482,374.025z M216.155,397.718
                                l-21.508-21.508l160.053-160.054l21.508,21.508L216.155,397.718z M379.358,171.755c-10.801,10.801-28.313,10.801-39.114,0
                                c-10.801-10.801-10.801-28.313,0-39.114c10.801-10.801,28.313-10.801,39.114,0C390.159,143.443,390.159,160.955,379.358,171.755z"
                                fill="currentColor" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path d="M459.407,52.591C425.493,18.677,380.402,0,332.441,0s-93.052,18.677-126.966,52.591L52.592,205.476
                                c-70.009,70.009-70.009,183.922,0,253.932c33.914,33.914,79.004,52.591,126.966,52.591c47.962,0,93.052-18.677,126.966-52.591
                                l152.883-152.884C529.416,236.514,529.416,122.601,459.407,52.591z M430.73,277.846L277.846,430.731
                                c-26.221,26.22-61.127,40.66-98.289,40.66c-37.163,0-72.068-14.439-98.288-40.66c-54.196-54.196-54.196-142.38,0-196.578
                                L234.153,81.269c26.22-26.22,61.126-40.66,98.289-40.66c37.162,0,72.068,14.439,98.288,40.66
                                C484.926,135.466,484.926,223.65,430.73,277.846z"
                                fill="currentColor" />
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Subdivision Card -->
        <div class="flex justify-between items-center p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 dark:shadow-md">
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Subdivision</h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">{{ $subdivisionName }} ({{ $subdivisionID }})</p>
            </div>
            <div>
                <!-- Placeholder for Icon -->
                <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0 0v6M9.5 9A2.5 2.5 0 0 1 12 6.5"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Exam Attempts Table -->
    <div class="relative overflow-x-auto shadow-md">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <!-- Table Header -->
            <thead class="text-xs text-white uppercase bg-cyan-900 dark:bg-cyan-900 dark:text-white">
                <tr>
                    <th colspan="5" class="px-6 py-3 text-lg">Exam Attempts</th>
                </tr>
            </thead>
            <!-- Column Headers -->
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
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
                        <td class="px-6 py-4">{{ $attempt->score && $attempt->status!=='waiting_for_manual_evaluation' ? $attempt->score : '-' }}</td>
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
    
    <div class="mt-4">
        {{ $exam_attempts->links('vendor.pagination.flowbite') }}
    </div>
</div>

@endsection