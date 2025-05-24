@extends('layouts.app')

@section('content')
<div id="main-content" class="ml-64 pt-20 pb-6 pl-4 pr-4 bg-gray-50 dark:bg-gray-700 min-h-screen">
    <h1 class="text-2xl font-bold mb-4 text-black dark:text-white">Role Management</h1>

    @if (session('success'))
        <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 1 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Role Assignment Form -->
    <div class="mb-6 p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h2 class="text-lg font-bold mb-4 text-black dark:text-white">Assign Roles</h2>
        <form action="{{ route('admin.roles.assign') }}" method="POST" id="roleForm">
            @csrf
            <div class="mb-4">
                <label for="cid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CID</label>
                <input type="text" name="cid" id="cid" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Enter CID" required>
            </div>
            <div class="mb-4" id="rolesDiv">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Assign Roles</label>
                <div id="roles_checkboxes" class="flex flex-col gap-2 text-gray-900 dark:text-white">
                    @php $supIds = [3]; @endphp
                    @foreach ($roles as $role)
                        @if ($role->id != 1)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="role_ids[]" class="role-checkbox cursor-pointer" value="{{ $role->id }}" data-type="{{ in_array($role->id, $supIds) ? 'SUP' : 'STD' }}">
                                <span class="ml-2">{{ $role->name }} [{{ in_array($role->id, $supIds) ? 'SUP' : 'STD' }}]</span>
                            </label>
                        @endif
                    @endforeach
                </div>
                <div class="text-xs text-red-600 mt-1">Can only select one SUP. If SUP selected, cannot select any STD.</div>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer">Assign Roles</button>
        </form>
    </div>

    <!-- Display Users with Roles -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-600 dark:text-white">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">CID</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Roles</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php
                        $nonStudentRoles = $user->roles->filter(fn($role) => $role->id != 1);
                    @endphp
                    @if ($nonStudentRoles->count())
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->cid }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                {{ $nonStudentRoles->pluck('name')->join(', ') }}
                            </td>
                            <td class="px-6 py-4">
                                <!-- Edit button in table -->
                                <button
                                    data-modal-target="roleModal"
                                    data-modal-toggle="roleModal"
                                    type="button"
                                    class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 cursor-pointer"
                                    data-cid="{{ $user->cid }}"
                                    data-roles="{{ $user->roles->pluck('id')->implode(',') }}"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="roleModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Edit Roles
                </h3>
                <button type="button" data-modal-hide="roleModal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="roleEditForm" method="POST" action="{{ route('admin.roles.adjust') }}" class="p-4 md:p-5">
                @csrf
                <input type="hidden" name="cid" id="modal_cid">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Assign Roles</label>
                    <div id="modal_roles_checkboxes" class="flex flex-col gap-2 text-gray-900 dark:text-white">
                        @foreach ($roles as $role)
                            @if ($role->id != 1)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="role_ids[]" class="role-checkbox cursor-pointer" value="{{ $role->id }}" data-type="{{ in_array($role->id, $supIds) ? 'SUP' : 'STD' }}">
                                    <span class="ml-2">{{ $role->name }} [{{ in_array($role->id, $supIds) ? 'SUP' : 'STD' }}]</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    <div class="text-xs text-red-600 mt-1">Can only select one SUP. If SUP selected, cannot select any STD.</div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" data-modal-hide="roleModal" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 cursor-pointer">Cancel</button>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 cursor-pointer">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Map users by cid for modal editing
    const users = {
        @foreach ($users as $user)
            "{{ $user->cid }}": {
                cid: "{{ $user->cid }}",
                role_ids: [
                    @foreach ($user->roles as $role)
                        {{ $role->id }},
                    @endforeach
                ]
            },
        @endforeach
    };

    // Only one SUP, cannot mix SUP and STD
    function updateRoleCheckboxes(containerId) {
        const checkboxes = document.querySelectorAll(`#${containerId} .role-checkbox`);
        const supCheckboxes = Array.from(checkboxes).filter(cb => cb.dataset.type === 'SUP');
        const stdCheckboxes = Array.from(checkboxes).filter(cb => cb.dataset.type === 'STD');
        const checkedSUP = supCheckboxes.find(cb => cb.checked);

        if (checkedSUP) {
            supCheckboxes.forEach(cb => {
                if (cb !== checkedSUP) {
                    cb.checked = false;
                    cb.disabled = true;
                    cb.parentElement.classList.add('opacity-50', 'pointer-events-none');
                } else {
                    cb.disabled = false;
                    cb.parentElement.classList.remove('opacity-50', 'pointer-events-none');
                }
            });
            stdCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.disabled = true;
                cb.parentElement.classList.add('opacity-50', 'pointer-events-none');
            });
        } else {
            checkboxes.forEach(cb => {
                cb.disabled = false;
                cb.parentElement.classList.remove('opacity-50', 'pointer-events-none');
            });
        }
    }

    // Assign roles form (main)
    document.querySelectorAll('#roles_checkboxes .role-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateRoleCheckboxes('roles_checkboxes');
        });
    });

    // Fill modal with user data on open (Flowbite handles modal open/close)
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-toggle="roleModal"]')) {
            const btn = e.target;
            const cid = btn.getAttribute('data-cid');
            const roles = (btn.getAttribute('data-roles') || '').split(',').map(Number);

            // Set hidden input
            document.getElementById('modal_cid').value = cid;

            // Set checkboxes
            document.querySelectorAll('#modal_roles_checkboxes .role-checkbox').forEach(cb => {
                cb.checked = roles.includes(Number(cb.value));
            });
            
            updateRoleCheckboxes('modal_roles_checkboxes');
        }
    });

    // Modal checkboxes logic
    document.querySelectorAll('#modal_roles_checkboxes .role-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateRoleCheckboxes('modal_roles_checkboxes');
        });
    });

    // On modal submit, send all checked roles as role_ids[]
    document.getElementById('roleEditForm').addEventListener('submit', function(e) {
        Array.from(this.querySelectorAll('input[type="hidden"][name="role_ids[]"]')).forEach(el => el.remove());
        const checked = Array.from(document.querySelectorAll('#modal_roles_checkboxes .role-checkbox:checked'));
        checked.forEach(cb => {
            this.insertAdjacentHTML('beforeend', `<input type="hidden" name="role_ids[]" value="${cb.value}">`);
        });
    });
</script>
@endsection
