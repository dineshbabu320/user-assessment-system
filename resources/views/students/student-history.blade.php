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
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tokens</h3>
                
                @if (auth()->user()->isSubdivisionStaff() || auth()->user()->isAdmin())
                    <!-- Issue Token Button -->
                    <button data-modal-target="issue-token-modal" data-modal-toggle="issue-token-modal" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Issue Token
                    </button>
                @endif
            </div>

            @if ($tokens->count() > 0)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Token ID</th>
                                <th scope="col" class="px-6 py-3">Issued Date</th>
                                <th scope="col" class="px-6 py-3">Expiry Date</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                @if (auth()->user()->isSubdivisionStaff() || auth()->user()->isAdmin())
                                    <th scope="col" class="px-6 py-3">Action</th>
                                @endif
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
                                    @if (auth()->user()->isSubdivisionStaff() || auth()->user()->isAdmin())
                                        <td class="px-6 py-4">
                                            @if ($token->status == 'active')
                                                <form action="{{ route('tokens.revoke', $token->token_instance_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this token?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="cursor-pointer px-4 py-2 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Revoke</button>
                                                </form>
                                            @endif
                                        </td>
                                    @endif
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

<!-- Issue Token Modal -->
<div id="issue-token-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Issue Token for {{ $student->name }}
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="issue-token-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
                <form action="{{ route('students.issueToken') }}" method="POST" id="issueTokenForm">
                    @csrf
                    <input type="hidden" name="user_cid" value="{{ $student->cid }}">
                    <div class="mb-4">
                        <label for="token_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Token</label>
                        <select name="token_id" id="token_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                            <option value="READY-S1">READY-S1</option>
                            <option value="READY-S2">READY-S2</option>
                            <option value="READY-S3">READY-S3</option>
                            <option value="READY-C1">READY-C1</option>
                            @if(auth()->user()->isAdmin())
                                <option value="READY-C3">READY-C3</option>
                                <option value="READY-I1">READY-I1</option>
                                <option value="REC-I1">READY-I3</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Issue Token
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>