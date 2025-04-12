@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- About Section -->
    <div class="mb-12">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">About This Application</h1>
        <p class="text-gray-700 dark:text-gray-300">
            This application is designed to streamline the management of student evaluations, exam attempts, and token issuance for mentors and admins. It provides an intuitive interface to manage questions, evaluate students, and track progress efficiently.
        </p>
        <p class="mt-4 text-gray-700 dark:text-gray-300">
            If you encounter any issues or have feedback, feel free to reach out via the contact options below.
        </p>
        <ul class="mt-4 list-disc list-inside text-gray-700 dark:text-gray-300">
            <li>
                <a href="https://github.com/your-github-profile" target="_blank" class="text-blue-600 hover:underline">GitHub</a>
            </li>
            <li>
                Discord: <span class="text-gray-900 dark:text-white">captain_h.db</span>
            </li>
            <li>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dineshbabu7358@gmail.com" target="_blank" class="text-blue-600 hover:underline">Email</a>
            </li>
        </ul>
    </div>

    <!-- FAQ Section -->
    <div>
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Frequently Asked Questions</h1>
        <div id="accordion-collapse" data-accordion="collapse" class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="border border-gray-200 rounded-lg shadow-lg dark:border-gray-700 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                <h2 id="accordion-collapse-heading-1">
                    <button type="button" class="flex items-center justify-between w-full p-5 font-medium text-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-800 transition-all cursor-pointer" data-accordion-target="#accordion-collapse-body-1" aria-expanded="false" aria-controls="accordion-collapse-body-1">
                        <span><i class="fas fa-key text-blue-500"></i> How do I issue a token?</span>
                        <svg data-accordion-icon class="w-4 h-4 transition-transform transform rotate-0 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                        </svg>
                    </button>
                </h2>
                <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300">
                            Navigate to the "Student Search" page, search for a student, and click the "Issue Token" button. Select the appropriate token and confirm the issuance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 rounded-lg shadow-lg dark:border-gray-700 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                <h2 id="accordion-collapse-heading-2">
                    <button type="button" class="flex items-center justify-between w-full p-5 font-medium text-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-800 transition-all cursor-pointer" data-accordion-target="#accordion-collapse-body-2" aria-expanded="false" aria-controls="accordion-collapse-body-2">
                        <span><i class="fas fa-user-check text-green-500"></i> How do I evaluate a student's manual evaluation?</span>
                        <svg data-accordion-icon class="w-4 h-4 transition-transform transform rotate-0 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                        </svg>
                    </button>
                </h2>
                <div id="accordion-collapse-body-2" class="hidden" aria-labelledby="accordion-collapse-heading-2">
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300">
                            Go to the "Manual Evaluation" section, select a student, and evaluate all pending responses on a single page. Submit all evaluations at once.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 rounded-lg shadow-lg dark:border-gray-700 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                <h2 id="accordion-collapse-heading-3">
                    <button type="button" class="flex items-center justify-between w-full p-5 font-medium text-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-800 transition-all cursor-pointer" data-accordion-target="#accordion-collapse-body-3" aria-expanded="false" aria-controls="accordion-collapse-body-3">
                        <span><i class="fas fa-question-circle text-purple-500"></i> How do I manage questions for an exam?</span>
                        <svg data-accordion-icon class="w-4 h-4 transition-transform transform rotate-0 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                        </svg>
                    </button>
                </h2>
                <div id="accordion-collapse-body-3" class="hidden" aria-labelledby="accordion-collapse-heading-3">
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300">
                            Navigate to the "Manage Questions" page, select an exam, and view or edit its questions. You can also add new questions using the "Add Question" button.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Accordion toggle logic
    document.querySelectorAll('[data-accordion-target]').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.getAttribute('data-accordion-target'));
            const icon = button.querySelector('[data-accordion-icon]');

            if (target.classList.contains('hidden')) {
                target.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                target.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        });
    });
</script>
@endsection