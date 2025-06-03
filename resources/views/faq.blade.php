@extends('layouts.app')

@section('content')

<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4">
    <h1 class="text-3xl mb-5 font-extrabold dark:text-white">Exam System Related FAQ</h1>
    <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-gray-100 dark:bg-[#25304a] text-gray-900 dark:text-white" data-inactive-classes="text-gray-700 dark:text-gray-100">
        <h2 id="faq-heading-1">
            <button type="button"
                class="cursor-pointer flex items-center justify-between w-full p-5 font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#1a2332] hover:bg-gray-100 dark:hover:bg-[#283147] transition-colors gap-3 focus:outline-none"
                data-accordion-target="#faq-body-1"
                aria-expanded="true"
                aria-controls="faq-body-1">
                <span>How do I attempt an exam?</span>
                <svg data-accordion-icon class="w-3 h-3 transition-transform duration-300 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>
        <div id="faq-body-1" class="hidden" aria-labelledby="faq-heading-1">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-[#232c3b] rounded-b-xl shadow-lg">
                <p class="mb-2 text-gray-700 dark:text-gray-200">SEA-TEST is a token based exam system where you would need to be issued a token by your vACC Staff Members. You will receive an email regarding the token assigned and it's validity. Once a token has been issued for a specific exam, you can start the attempt by visiting the <b>"My Exams"</b> section.</p>
            </div>
        </div>
        <h2 id="faq-heading-2">
            <button type="button"
                class="cursor-pointer flex items-center justify-between w-full p-5 font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#1a2332] hover:bg-gray-100 dark:hover:bg-[#283147] transition-colors gap-3 focus:outline-none"
                data-accordion-target="#faq-body-2"
                aria-expanded="false"
                aria-controls="faq-body-2">
                <span>I failed an attempt, what do I do?</span>
                <svg data-accordion-icon class="w-3 h-3 transition-transform duration-300 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>
        <div id="faq-body-2" class="hidden" aria-labelledby="faq-heading-2">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-[#232c3b] rounded-b-xl shadow-lg">
                <p class="mb-2 text-gray-700 dark:text-gray-200">According to GCAP, following a failure on a theory test, a cooldown period of 72 hours apply. You may then request a token for another attempt after the period, from your vACC staff.</p>
            </div>
        </div>
        <h2 id="faq-heading-3">
            <button type="button"
                class="cursor-pointer flex items-center justify-between w-full p-5 font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#1a2332] hover:bg-gray-100 dark:hover:bg-[#283147] transition-colors gap-3 focus:outline-none"
                data-accordion-target="#faq-body-3"
                aria-expanded="false"
                aria-controls="faq-body-3">
                <span>I see "Waiting for manual evaluation" in my review form, what does that mean?</span>
                <svg data-accordion-icon class="w-3 h-3 transition-transform duration-300 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>
        <div id="faq-body-3" class="hidden" aria-labelledby="faq-heading-3">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-[#232c3b] rounded-b-xl shadow-lg">
                <p class="mb-2 text-gray-700 dark:text-gray-200">This means that you were presented with open-ended questions in your exam which requires manual evaluation from our examiners. It may take some time for our staff to evaluate your answers and you will be notified with your final score through an email.</p>
            </div>
        </div>
    </div>
</div>

@endsection