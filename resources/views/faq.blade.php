@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- About Section -->
    <div class="mb-12">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">About This Application</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Purpose Card -->
            <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Purpose</h2>
                <p class="mt-4 text-gray-700 dark:text-gray-300">
                    This application is designed to streamline the management of student evaluations, exam attempts, and token issuance for mentors and admins. It provides an intuitive interface to manage questions, evaluate students, and track progress efficiently.
                </p>
            </div>

            <!-- Features Card -->
            <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Features</h2>
                <ul class="mt-4 list-disc list-inside text-gray-700 dark:text-gray-300">
                    <li>Issue tokens to students for specific exams.</li>
                    <li>Conduct and review exams with automated and manual evaluation options.</li>
                    <li>Manage questions for exams with multiple question types.</li>
                    <li>Track student history and performance.</li>
                    <li>Administer rating upgrade requests.</li>
                </ul>
            </div>

            <!-- Technologies Card -->
            <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Technologies Used</h2>
                <p class="mt-4 text-gray-700 dark:text-gray-300">
                    This application is built using:
                </p>
                <ul class="mt-2 list-disc list-inside text-gray-700 dark:text-gray-300">
                    <li>Laravel (PHP Framework)</li>
                    <li>TailwindCSS for styling</li>
                    <li>JavaScript for interactivity</li>
                    <li>MySQL for database management</li>
                </ul>
            </div>

            <!-- Contact Card -->
            <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Contact</h2>
                <p class="mt-4 text-gray-700 dark:text-gray-300">
                    If you encounter any issues or have feedback, feel free to reach out:
                </p>
                <ul class="mt-2 list-disc list-inside text-gray-700 dark:text-gray-300">
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
        </div>
    </div>

    <!-- FAQ Section -->
    <div>
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Frequently Asked Questions</h1>
        <div id="faq-accordion" class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <button type="button" class="w-full text-left text-gray-900 dark:text-white font-medium" data-toggle="collapse" data-target="#faq1">
                    How do I issue a token?
                </button>
                <div id="faq1" class="mt-2 hidden">
                    <p class="text-gray-700 dark:text-gray-300">
                        Navigate to the "Student Search" page, search for a student, and click the "Issue Token" button. Select the appropriate token and confirm the issuance.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <button type="button" class="w-full text-left text-gray-900 dark:text-white font-medium" data-toggle="collapse" data-target="#faq2">
                    How do I evaluate a student's manual evaluation?
                </button>
                <div id="faq2" class="mt-2 hidden">
                    <p class="text-gray-700 dark:text-gray-300">
                        Go to the "Manual Evaluation" section, select a student, and evaluate all pending responses on a single page. Submit all evaluations at once.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <button type="button" class="w-full text-left text-gray-900 dark:text-white font-medium" data-toggle="collapse" data-target="#faq3">
                    How do I manage questions for an exam?
                </button>
                <div id="faq3" class="mt-2 hidden">
                    <p class="text-gray-700 dark:text-gray-300">
                        Navigate to the "Manage Questions" page, select an exam, and view or edit its questions. You can also add new questions using the "Add Question" button.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <button type="button" class="w-full text-left text-gray-900 dark:text-white font-medium" data-toggle="collapse" data-target="#faq4">
                    How do I review an exam attempt?
                </button>
                <div id="faq4" class="mt-2 hidden">
                    <p class="text-gray-700 dark:text-gray-300">
                        After a student submits an exam, navigate to the "Exam Review" page to view their responses, scores, and evaluation status.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <button type="button" class="w-full text-left text-gray-900 dark:text-white font-medium" data-toggle="collapse" data-target="#faq5">
                    What should I do if I encounter an error?
                </button>
                <div id="faq5" class="mt-2 hidden">
                    <p class="text-gray-700 dark:text-gray-300">
                        Please report any errors or issues via GitHub, Discord, or email. Include as much detail as possible to help us resolve the issue quickly.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.getAttribute('data-target'));
            target.classList.toggle('hidden');
        });
    });
</script>
@endsection