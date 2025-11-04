<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
   <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
         <div class="flex items-center justify-start rtl:justify-end">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
               type="button"
               class="cursor-pointer inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
               <span class="sr-only">Open sidebar</span>
               <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg">
                  <path clip-rule="evenodd" fill-rule="evenodd"
                     d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                  </path>
               </svg>
            </button>
            <a href="https://exam.vat-sea.com/dashboard" class="hidden sm:flex ms-2 md:me-24">
               <img src="{{ asset('images/SEA_TEST_DARK.png') }}" class="h-8 me-3 dark:hidden" alt="Logo" />
               <img src="{{ asset('images/SEA_TEST_LIGHT.png') }}" class="h-8 me-3 hidden dark:block" alt="Logo" />
               <span
                  class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">SEA-TEST</span>
            </a>

            @if(!auth()->user()->isStudent())
               <div class="flex sm:hidden flex-1 mx-2">
                  <div class="relative w-full">
                     <input type="text" id="user-search-mobile" autocomplete="off"
                        class="w-full p-2 pl-9 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Search users...">
                     <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none"
                           viewBox="0 0 20 20">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                     </div>
                     <div id="user-search-results-mobile"
                        class="absolute left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow z-50 hidden">
                     </div>
                  </div>
               </div>
            @endif
         </div>
         <div class="hidden sm:flex flex-1 justify-end mr-3">
            @if(!auth()->user()->isStudent())
               <div class="relative w-60">
                  <input type="text" id="user-search" autocomplete="off"
                     class="w-full p-2 pl-9 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                     placeholder="Search users...">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                     <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                     </svg>
                  </div>
                  <div id="user-search-results"
                     class="absolute left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow z-50 hidden">
                  </div>
               </div>
            @endif
         </div>
         <div class="flex items-center space-x-2">
            @can('subdivisionStaff')
               <button id="notification-settings-btn" data-modal-target="notification-settings-modal"
                  data-modal-toggle="notification-settings-modal" data-tooltip-target="tooltip-bottom"
                  data-tooltip-placement="bottom" type="button"
                  class="cursor-pointer text-gray-500 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path
                        d="M11.209 3.816a1 1 0 0 0-1.966.368l.325 1.74a5.338 5.338 0 0 0-2.8 5.762l.276 1.473.055.296c.258 1.374-.228 2.262-.63 2.998-.285.52-.527.964-.437 1.449.11.586.22 1.173.75 1.074l12.7-2.377c.528-.1.418-.685.308-1.27-.103-.564-.636-1.123-1.195-1.711-.606-.636-1.243-1.306-1.404-2.051-.233-1.085-.275-1.387-.303-1.587-.009-.063-.016-.117-.028-.182a5.338 5.338 0 0 0-5.353-4.39l-.298-1.592Z" />
                     <path fill-rule="evenodd"
                        d="M6.539 4.278a1 1 0 0 1 .07 1.412c-1.115 1.23-1.705 2.605-1.83 4.26a1 1 0 0 1-1.995-.15c.16-2.099.929-3.893 2.342-5.453a1 1 0 0 1 1.413-.069Z"
                        clip-rule="evenodd" />
                     <path d="M8.95 19.7c.7.8 1.7 1.3 2.8 1.3 1.6 0 2.9-1.1 3.3-2.5l-6.1 1.2Z" />
                  </svg>
               </button>
               <div id="tooltip-bottom" role="tooltip"
                  class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg shadow-xs opacity-0 tooltip">
                  Update Mail Settings
                  <div class="tooltip-arrow" data-popper-arrow></div>
               </div>
            @endcan

            <button id="theme-toggle" type="button"
               class="cursor-pointer text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
               <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg">
                  <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
               </svg>
               <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                     d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                     fill-rule="evenodd" clip-rule="evenodd"></path>
               </svg>
            </button>

            <div class="flex items-center">
               <div class="flex items-center">
                  <div>
                     <button type="button"
                        class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 cursor-pointer"
                        aria-expanded="false" data-dropdown-toggle="dropdown-user">
                        <div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                           <svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20"
                              xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                 clip-rule="evenodd"></path>
                           </svg>
                        </div>
                     </button>
                  </div>
                  <div
                     class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600"
                     id="dropdown-user">
                     <div class="px-4 py-3" role="none">
                        <p class="text-sm text-gray-900 dark:text-white" role="none">
                           {{ auth()->user()->name }}
                        </p>
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                           {{ auth()->user()->email }}
                        </p>
                     </div>
                     <ul class="py-1" role="none">
                        <li>
                           <a href="{{ route('dashboard') }}"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                              role="menuitem">Dashboard</a>
                        </li>
                        @if(auth()->user()->isAdmin())
                           <li>
                              <a href="{{ route('admin.settings') }}"
                                 class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                 role="menuitem">Admin Settings</a>
                           </li>
                        @endif
                        <li>
                           <a href="{{ route('logout') }}"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                              role="menuitem">Sign out</a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</nav>

<aside id="logo-sidebar"
   class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
   aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <li>
            <a href="{{ route('dashboard') }}"
               class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path
                     d="M13.5 2c-.178 0-.356.013-.492.022l-.074.005a1 1 0 0 0-.934.998V11a1 1 0 0 0 1 1h7.975a1 1 0 0 0 .998-.934l.005-.074A7.04 7.04 0 0 0 22 10.5 8.5 8.5 0 0 0 13.5 2Z" />
                  <path d="M11 6.025a1 1 0 0 0-1.065-.998 8.5 8.5 0 1 0 9.038 9.039A1 1 0 0 0 17.975 13H11V6.025Z" />
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="{{ route('my-exams') }}"
               class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd"
                     d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                     clip-rule="evenodd" />
                  <path fill-rule="evenodd"
                     d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                     clip-rule="evenodd" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">My Exams</span>
            </a>
         </li>
         @if(auth()->user()->isExaminer() || auth()->user()->isAdmin())
            <li>
               <a href="{{ route('student.manualEvaluation') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                        d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-3 8a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-2-1a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H9Zm2 5a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-2-1a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H9Z"
                        clip-rule="evenodd" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">Manual Evaluation</span>
               </a>
            </li>
         @endif
         @can('subdivisionStaff')
            <li>
               <a href="{{ route('subdivision.questions.index') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">vACC QB</span>
               </a>
            </li>
         @endcan
         @if(auth()->user()->isAdmin())
            <li>
               <a href="{{ route('admin.questions.index') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                        d="M11 4.717c-2.286-.58-4.16-.756-7.045-.71A1.99 1.99 0 0 0 2 6v11c0 1.133.934 2.022 2.044 2.007 2.759-.038 4.5.16 6.956.791V4.717Zm2 15.081c2.456-.631 4.198-.829 6.956-.791A2.013 2.013 0 0 0 22 16.999V6a1.99 1.99 0 0 0-1.955-1.993c-2.885-.046-4.76.13-7.045.71v15.081Z"
                        clip-rule="evenodd" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">Question Bank</span>
               </a>
            </li>
         @endif
         @if(auth()->user()->isAdmin())
            <li>
               <a href="{{ route('admin.activity-log') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                        d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm2-2a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm0 3a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm-6 4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-6Zm8 1v1h-2v-1h2Zm0 3h-2v1h2v-1Zm-4-3v1H9v-1h2Zm0 3H9v1h2v-1Z"
                        clip-rule="evenodd" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">Audit Logs</span>
               </a>
            </li>
         @endif
         @if(auth()->user()->isAdmin())
            <li>
               <a href="{{ route('admin.roles.index') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path fill-rule="evenodd"
                        d="M17 10v1.126c.367.095.714.24 1.032.428l.796-.797 1.415 1.415-.797.796c.188.318.333.665.428 1.032H21v2h-1.126c-.095.367-.24.714-.428 1.032l.797.796-1.415 1.415-.796-.797a3.979 3.979 0 0 1-1.032.428V20h-2v-1.126a3.977 3.977 0 0 1-1.032-.428l-.796.797-1.415-1.415.797-.796A3.975 3.975 0 0 1 12.126 16H11v-2h1.126c.095-.367.24-.714.428-1.032l-.797-.796 1.415-1.415.796.797A3.977 3.977 0 0 1 15 11.126V10h2Zm.406 3.578.016.016c.354.358.574.85.578 1.392v.028a2 2 0 0 1-3.409 1.406l-.01-.012a2 2 0 0 1 2.826-2.83ZM5 8a4 4 0 1 1 7.938.703 7.029 7.029 0 0 0-3.235 3.235A4 4 0 0 1 5 8Zm4.29 5H7a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h6.101A6.979 6.979 0 0 1 9 15c0-.695.101-1.366.29-2Z"
                        clip-rule="evenodd" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">Role Assignment</span>
               </a>
            </li>
         @endif
         @if(auth()->user()->isAdmin())
            <li>
               <a href="{{ route('admin.notifications.edit') }}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                     <path
                        d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM6 6a1 1 0 0 1-.707-.293l-1-1a1 1 0 0 1 1.414-1.414l1 1A1 1 0 0 1 6 6Zm-2 4H3a1 1 0 0 1 0-2h1a1 1 0 1 1 0 2Zm14-4a1 1 0 0 1-.707-1.707l1-1a1 1 0 1 1 1.414 1.414l-1 1A1 1 0 0 1 18 6Zm3 4h-1a1 1 0 0 1 0-2h1a1 1 0 0 1 0 2ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z" />
                  </svg>
                  <span class="flex-1 ms-3 whitespace-nowrap">Notifications</span>
               </a>
            </li>
         @endif
         <li>
            <a href="{{ route('faq') }}"
               class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-[25px] h-[25px] text-gray-800 dark:text-white" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd"
                     d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z"
                     clip-rule="evenodd" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">FAQ</span>
            </a>
         </li>
      </ul>
      <!-- Footer Section -->
      <div class="absolute bottom-0 left-0 w-full p-4 bg-white dark:bg-gray-800">
         <p class="text-sm text-black dark:text-gray-400 text-center italic">
            Developed by <b class="text-sky-400">Dinesh Babu</b>
         </p>
         <p class="text-sm text-black dark:text-gray-400 text-center mt-1">
            &copy; {{ date('Y') }} SEA-TEST. All rights reserved.
         </p>
      </div>
   </div>
</aside>
<!-- Notification Email Modal -->
<div id="notification-settings-modal" tabindex="-1" aria-hidden="true"
   class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
   <div class="relative p-4 w-full max-w-md max-h-full">
      <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
         <div
            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
               Notification Email Settings
            </h3>
            <button type="button"
               class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
               data-modal-hide="notification-settings-modal">
               <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
               </svg>
               <span class="sr-only">Close modal</span>
            </button>
         </div>
         <div class="p-4 md:p-5">
            <form class="space-y-4" method="POST" action="{{ route('mail.preferences.set') }}">
               @csrf
               <div>
                  <label for="notification_email"
                     class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notification Email</label>
                  <input type="email" id="notification_email" name="notification_email"
                     value="{{ old('notification_email', auth()->user()->notification_email) }}"
                     class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                     placeholder="Enter email to receive notifications">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                     Leave blank to use your login email ({{ auth()->user()->email }}).
                  </p>
               </div>
               <div class="flex justify-end gap-2">
                  <button type="button" data-modal-hide="notification-settings-modal" id="cancel-notification-modal"
                     class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 cursor-pointer">Cancel</button>
                  <button type="submit"
                     class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 cursor-pointer">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<script>
   let searchTimeout = null;

   // Desktop search functionality
   const searchInput = document.getElementById('user-search');
   const resultsBox = document.getElementById('user-search-results');

   // Mobile search functionality
   const searchInputMobile = document.getElementById('user-search-mobile');
   const resultsBoxMobile = document.getElementById('user-search-results-mobile');

   function handleSearch(input, results) {
      if (!input || !results) return;

      input.addEventListener('input', function () {
         clearTimeout(searchTimeout);
         const query = this.value.trim();

         // Clear results if query is empty
         if (query.length === 0) {
            results.innerHTML = '';
            results.classList.add('hidden');
            return;
         }

         searchTimeout = setTimeout(() => {
            fetch(`/user-search?q=${encodeURIComponent(query)}`)
               .then(res => res.json())
               .then(users => {
                  if (users.length === 0) {
                     results.innerHTML = '<div class="p-2 text-gray-500 text-black dark:text-white">No users found.</div>';
                  } else {
                     results.innerHTML = users.map(u =>
                        `<div class="block p-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b last:border-b-0 text-black dark:text-white" onclick="window.location.href='/student/${u.cid}'">
                                ${u.name} <span>(${u.cid})</span>
                            </div>`
                     ).join('');
                  }
                  results.classList.remove('hidden');
               })
               .catch(error => {
                  console.error('Search error:', error);
                  results.innerHTML = '<div class="p-2 text-red-500">Search failed. Please try again.</div>';
                  results.classList.remove('hidden');
               });
         }, 300);
      });
   }

   // Initialize both search boxes
   handleSearch(searchInput, resultsBox);
   handleSearch(searchInputMobile, resultsBoxMobile);

   // Hide results when clicking outside
   document.addEventListener('click', function (e) {
      // Desktop search
      if (searchInput && resultsBox && !searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
         resultsBox.classList.add('hidden');
      }

      // Mobile search
      if (searchInputMobile && resultsBoxMobile && !searchInputMobile.contains(e.target) && !resultsBoxMobile.contains(e.target)) {
         resultsBoxMobile.classList.add('hidden');
      }
   });
</script>