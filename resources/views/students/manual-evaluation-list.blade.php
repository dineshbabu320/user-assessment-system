@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Manual Evaluations</h1>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">CID</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Subdivision</th>
                    <th scope="col" class="px-6 py-3">Pending Evaluations</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $student->cid }}</td>
                    <td class="px-6 py-4">{{ $student->name }}</td>
                    <td class="px-6 py-4">{{ $student->subdivision_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $student->pending_evaluations }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('student.manualEvaluation.show', $student->cid) }}" class="text-blue-500 hover:underline">View Evaluations</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection