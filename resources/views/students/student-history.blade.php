@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Student History</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Detailed history of the student, including tokens and exam attempts.</p>
    </div>

    <!-- Student Info Card -->
    <div class="mb-6 p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $student->name }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">CID: {{ $student->cid }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Email: {{ $student->email }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Subdivision: {{ $student->subdivision_name ?? 'N/A' }}</p>
    </div>
        
    <!-- Tokens Section -->
    @if (!auth()->user()->isMentor())
        <div id="tokens-section" class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tokens</h3>
            @if ($tokens->count() > 0)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Token ID</th>
                                <th scope="col" class="px-6 py-3">Issued Date</th>
                                <th scope="col" class="px-6 py-3">Expiry Date</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tokens as $token)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $token->id }}</td>
                                    <td class="px-6 py-4">{{ $token->issued_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">{{ $token->expires_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($token->status == 'active')
                                            <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded dark:bg-green-900 dark:text-green-300">Active</span>
                                        @elseif ($token->status == 'used')
                                            <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded dark:bg-red-900 dark:text-red-300">Used</span>
                                        @elseif ($token->status == 'revoked')
                                            <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded dark:bg-red-900 dark:text-red-300">Revoked</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded dark:bg-red-900 dark:text-red-300">Expired</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($token->status == 'active')
                                            <form action="{{ route('tokens.revoke', $token->token_instance_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this token?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="cursor-pointer px-4 py-2 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Revoke</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $tokens->appends(['attempts_page' => request('attempts_page')])->links('vendor.pagination.flowbite') }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 mt-4">No tokens found for this student.</p>
            @endif
        </div>
    @endif

    <!-- Exam Attempts Section -->
    <div id="attempts-section" class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Exam Attempts</h3>
        @if ($attempts->count() > 0)
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Exam Name</th>
                            <th scope="col" class="px-6 py-3">Percentage</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Attempt Date</th>
                            @if (auth()->user()->isSubdivisionStaff() || auth()->user()->isAdmin() || auth()->user()->isMentor())
                                <th scope="col" class="px-6 py-3">Review</th>
                            @endif
                            @if (auth()->user()->isAdmin())
                                <th scope="col" class="px-6 py-3">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attempts as $attempt)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->exam->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->percentage.'%' ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($attempt->status === 'passed')
                                        <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded dark:bg-green-900 dark:text-green-300">Passed</span>
                                    @elseif ($attempt->status === 'failed')
                                        <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded dark:bg-red-900 dark:text-red-300">Failed</span>
                                    @elseif($attempt->status === 'waiting_for_manual_evaluation')
                                        <span class="px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded dark:bg-yellow-900 dark:text-yellow-300">Waiting for Evaluation</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded dark:bg-yellow-900 dark:text-yellow-300">In Progress</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->attempted_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('exam.review', $attempt->id) }}" class="text-blue-400">Review</a>
                                </td>
                                @if (auth()->user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('manual-evaluation-admin.show', $attempt->id) }}" 
                                           class="w-full sm:w-auto inline-block text-center px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:px-4 sm:py-2 sm:text-sm cursor-pointer">
                                            Manually Evaluate
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $attempts->appends(['tokens_page' => request('tokens_page')])->links('vendor.pagination.flowbite') }}
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 mt-4">No exam attempts found for this student.</p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tokens_page')) {
            document.getElementById('tokens-section').scrollIntoView({ behavior: 'smooth' });
        } else if (urlParams.has('attempts_page')) {
            document.getElementById('attempts-section').scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
@endsection