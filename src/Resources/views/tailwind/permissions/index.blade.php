<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />

    <!-- Custom Styles for DataTables & Components -->
    <style>
        /* Custom styling for DataTables to match Tailwind */
        .dataTables_wrapper .dataTables_filter input {
            @apply border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
        .dataTables_wrapper .dataTables_length select {
            @apply border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
        .dataTables_paginate .paginate_button {
            @apply px-3 py-1 ml-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100;
        }
        .dataTables_paginate .paginate_button.current {
            @apply bg-blue-500 text-white hover:bg-blue-600 border-blue-500;
        }
        .dataTables_paginate .paginate_button.disabled {
            @apply opacity-50 cursor-not-allowed;
        }
    </style>
</head>

<body class="bg-white p-4 md:p-6">

    <div class="max-w-7xl mx-auto p-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-2">
                <h3 class="text-2xl font-bold text-gray-800 underline"> <a href="{{url('/beft/roles')}}"> Roles Management</a></h3>
                <h3 class="text-2xl font-bold text-blue-800 underline">Permission Management</h3>
            </div>
            <button id="showAddEditRolesModal" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 ease-in-out flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Permission
            </button>
        </div>

        <!-- Toast Notification -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
            <div id="showToast" class="bg-green-500 text-white font-medium py-3 px-5 rounded-lg shadow-lg flex items-center justify-between">
                <span id="toastMessage"></span>
                <button onclick="hideToast()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-gray-100 shadow-md rounded-lg">
            <div class="p-4 md:p-6">
                <table id="roles-table" class="display w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="p-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="p-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                            <th class="p-3 text-xs font-medium text-gray-500 uppercase tracking-wider"><span class="flex justify-center">Action</span></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div id="addEditRolesModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <form id="roleForm">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" name="id" id="role_id">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-xl font-bold text-gray-800" id="modalTitle">Create New Permission</h5>
                    <button type="button" onclick="hideModal('addEditRolesModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="mb-4">
                    <div id="editLoading" class="text-center hidden">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="formContent">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Permission Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter permission name">
                            <p class="text-red-500 text-xs italic mt-1" id="error-name"></p>
                        </div>
                        <div class="mb-4">
                            <label for="group_name" class="block text-gray-700 text-sm font-bold mb-2">Permission Group Name <span class="text-red-500">*</span></label>
                            <input type="text" name="group_name" id="group_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter permission name">
                            <p class="text-red-500 text-xs italic mt-1" id="error-group_name"></p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-4 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300" id="submitBtn">
                        <span class="submit-text">Save Permission</span>
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <button type="button" onclick="hideModal('addEditRolesModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Are you sure?</h3>
                <p class="text-sm text-gray-500 mb-4">Do you really want to delete this Permission? This action cannot be undone.</p>
                <div class="flex justify-center gap-2">
                    <button type="button" id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        <span class="delete-text">Yes, delete</span>
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <button type="button" onclick="hideModal('confirmationModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

 <!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

<script>
    // --- Global Utility Functions ---
    // These functions are moved outside the $(document).ready() block
    // so they can be called from inline onclick attributes in the HTML.

    function showToast(message, type = 'success') {
        const toastContainer = $('#toast-container');
        const toastElement = $('#showToast');
        const toastMessage = $('#toastMessage');

        toastMessage.text(message);
        toastElement.removeClass('bg-green-500 bg-red-500').addClass(type === 'success' ? 'bg-green-500' : 'bg-red-500');

        toastContainer.removeClass('opacity-0 pointer-events-none').addClass('opacity-100');

        setTimeout(hideToast, 3000);
    }

    function hideToast() {
        const toastContainer = $('#toast-container');
        toastContainer.removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => {
            toastContainer.addClass('pointer-events-none');
        }, 300);
    }

    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function hideModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }


 $(document).ready(function() {
    // --- Cache DOM Elements ---
    const roleForm = $('#roleForm');
    const methodInput = $('#method');
    const modalTitle = $('#modalTitle');
    const formContent = $('#formContent');
    const editLoading = $('#editLoading');
    const submitBtn = $('#submitBtn');
    const confirmDeleteBtn = $('#confirmDeleteBtn');
    let currentDeleteId = null;

    // --- Setup AJAX ---
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- Initialize DataTables ---
    const table = $('#roles-table').DataTable({
        ajax: "/beft/permissions",
        columns: [
            { data: "name", name: "name",
                render: function(data) {
                    let parts = data.split('.');
                    return parts.map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                }
            },
            { data: "group_name", name: "group_name" },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="flex justify-center gap-2">
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-sm edit-btn" data-id="${data}">Edit</button>
                            <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm delete-btn" data-id="${data}">Delete</button>
                        </div>
                    `;
                }
            }
        ]
    });

    // --- Local Utility Functions ---

    function setLoadingState(button, loading = true) {
        const textSpan = button.find('span').first();
        const spinner = button.find('svg');
        if (loading) {
            button.prop('disabled', true);
            textSpan.addClass('hidden');
            spinner.removeClass('hidden');
        } else {
            button.prop('disabled', false);
            textSpan.removeClass('hidden');
            spinner.addClass('hidden');
        }
    }

    function resetForm() {
        roleForm[0].reset();
        methodInput.val('POST');
        modalTitle.text('Create New Permission');
        $('#role_id').val('');
        roleForm.find('p[id^="error-"]').text('');
        roleForm.find('input, select').removeClass('border-red-500');
    }

    // --- Event Handlers ---

    // Show add role modal
    $('#showAddEditRolesModal').on('click', function() {
        resetForm();
        showModal('addEditRolesModal');
    });

    // Edit role
    $('#roles-table').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        resetForm();
        modalTitle.text('Edit Permission');

        formContent.addClass('hidden');
        editLoading.removeClass('hidden');
        showModal('addEditRolesModal');

        $.get(`/beft/permissions/${id}/edit`, function(data) {
            $('#role_id').val(data.id);
            let parts = data.name.split('.');
            $('#name').val(parts[1] ?? data.name);

            //  $('#name').val(data.name);
            $('#group_name').val(data.group_name);
            methodInput.val('PUT');
        }).fail(function() {
            showToast('Error fetching permission data.', 'error');
            hideModal('addEditRolesModal');
        }).always(function() {
            formContent.removeClass('hidden');
            editLoading.addClass('hidden');
        });
    });

    // Delete role
    $('#roles-table').on('click', '.delete-btn', function() {
        currentDeleteId = $(this).data('id');
        showModal('confirmationModal');
    });

    $('#confirmDeleteBtn').on('click', function() {
        setLoadingState(confirmDeleteBtn, true);
        $.ajax({
            url: `/beft/permissions/${currentDeleteId}`,
            method: 'DELETE',
            success: function(data) {
                table.ajax.reload();
                hideModal('confirmationModal');
                showToast(data.message, 'success');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'An error occurred while deleting the role.';
                showToast(message, 'error');
            },
            complete: function() {
                setLoadingState(confirmDeleteBtn, false);
            }
        });
    });

    // Clear errors on user input
    $('#name').on('input', function() {
        $(this).removeClass('border-red-500');
        $('#error-name').text('');
        $('#error-group_name').text('');
    });


    // Submit form
    roleForm.on('submit', function(e) {
        e.preventDefault();
        setLoadingState(submitBtn, true);

        const id = $('#role_id').val();
        const url = id ? `/beft/permissions/${id}` : "/beft/permissions";
        const method = methodInput.val();

        $.ajax({
            url: url,
            method: method,
            data: roleForm.serialize(),
            success: function(data) {
                table.ajax.reload();
                hideModal('addEditRolesModal');
                showToast(data.message, 'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    roleForm.find('input, select').removeClass('border-red-500');
                    roleForm.find('p[id^="error-"]').text('');

                    Object.keys(errors).forEach(function(field) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('border-red-500');
                        $(`#error-${field}`).text(errors[field][0]);
                    });
                } else {
                    const message = xhr.responseJSON?.message || 'An error occurred.';
                    showToast(message, 'error');
                }
            },
            complete: function() {
                setLoadingState(submitBtn, false);
            }
        });
    });

});
</script>

</body>
</html>