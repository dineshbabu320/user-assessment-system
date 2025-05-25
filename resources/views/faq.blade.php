@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <!-- About Section -->
    <div class="mb-12">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">FAQ</h1>
        <p class="text-gray-700 dark:text-gray-300">
            This section is currently under development. If you have any questions or need assistance, please <span><a href="https://mail.google.com/mail/?view=cm&fs=1&to=vatseatech@gmail.com" target="_blank" class="text-blue-400 hover:underline">send me an e-mail</a></span>
        </p>
    </div>
</div>

@endsection

@section('scripts')

<script>
    window.addEventListener('load', function () {
        const copyBtn = document.getElementById('copy-discord-btn');
        const username = document.getElementById('discord-username');
        const toast = document.getElementById('copy-toast');

        function showToast() {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 1500);
        }

        if (copyBtn && username) {
            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(username.textContent.trim()).then(showToast);
            });
            copyBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    navigator.clipboard.writeText(username.textContent.trim()).then(showToast);
                }
            });
        }
    });
</script>

@endsection