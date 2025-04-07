@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rating Upgrade Requests</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Manage and process rating upgrade requests submitted by users.</p>
    </div>

    <!-- Table Section -->
    @if ($requests->count() > 0)
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">User CID</th>
                        <th scope="col" class="px-6 py-3">Requested Rating</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $request->user_cid }}</td>
                            <td class="px-6 py-4">{{ $request->rating }}</td>
                            <td class="px-6 py-4">
                                @if ($request->completed)
                                    <span class="text-green-500 font-bold">Completed</span>
                                @else
                                    <span class="text-yellow-500 font-bold">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if (!$request->completed)
                                    <form method="POST" action="{{ route('admin.markRatingUpgradeCompleted', $request->id) }}">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                            Mark as Completed
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center mt-10">
            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <p class="mt-4 text-gray-500 dark:text-gray-400">No rating upgrade requests found.</p>
        </div>
    @endif
</div>
@endsection