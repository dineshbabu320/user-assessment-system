@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- About Section -->
    <div class="mb-12">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">FAQ</h1>
        <p class="text-gray-700 dark:text-gray-300">
            This section is currently under development. If you have any questions or need assistance, please use the contact options below.
        </p>
        <ul class="mt-4 list-disc list-inside text-gray-700 dark:text-gray-300">
            <li class="flex items-center gap-2">
                Discord: 
                <span class="text-gray-900 dark:text-white">
                    <div class="w-full max-w-[16rem]">
                        <div class="relative">
                            <label for="npm-install-copy-text" class="sr-only">Label</label>
                            <input id="npm-install-copy-text" type="text" class="col-span-6 bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2.5 py-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" value="captain_h.db" disabled readonly>
                            <button data-copy-to-clipboard-target="npm-install-copy-text" class="cursor-pointer absolute end-2.5 top-1/2 -translate-y-1/2 text-gray-900 dark:text-gray-400 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 rounded-lg py-2 px-2.5 inline-flex items-center justify-center bg-white border-gray-200 border h-8">
                                <span id="default-message">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                                        </svg>
                                        <span class="text-xs font-semibold">Copy</span>
                                    </span>
                                </span>
                                <span id="success-message" class="hidden">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 text-blue-700 dark:text-blue-500 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-500">Copied</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </span>
            </li>
            <li>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=vatseatech@gmail.com" target="_blank" class="text-blue-400 hover:underline">Send me an Email</a>
            </li>
        </ul>
    </div>
</div>

@endsection

@section('scripts')

<script>
    window.addEventListener('load', function () {
        const copyBtn = document.querySelector('[data-copy-to-clipboard-target="npm-install-copy-text"]');
        const input = document.getElementById('npm-install-copy-text');
        const $defaultMessage = document.getElementById('default-message');
        const $successMessage = document.getElementById('success-message');

        if (copyBtn && input) {
            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(input.value).then(function () {
                    showSuccess();
                    setTimeout(resetToDefault, 2000);
                });
            });
        }

        function showSuccess() {
            $defaultMessage.classList.add('hidden');
            $successMessage.classList.remove('hidden');
        }

        function resetToDefault() {
            $defaultMessage.classList.remove('hidden');
            $successMessage.classList.add('hidden');
        }
    });
</script>

@endsection