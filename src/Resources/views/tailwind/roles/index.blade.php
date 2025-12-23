@extends('layout.layout')

@php
    $title = 'Roles Management';
    $subTitle = 'Roles';
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
                <div
                    class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">

                    <button type="button" id="showAddEditRolesModal" class="btn btn-primary ms-auto text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Role
                    </button>
                </div>
                <div class="card-body p-6">
                    <div class="table-responsive scroll-sm">
                        <table id="roles-table"
                            class="w-full border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-4 py-3">Name</th>
                                    <th scope="col" class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Role -->
    <div id="addEditRolesModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalTitle">
                        Create New Role
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        id="closeAddEditRolesModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <form action="" method="POST" id="roleForm">
                        @csrf
                        <input type="hidden" name="_method" id="method" value="POST">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                            <div class="col-span-12">
                                <label class="form-label" for="name">Role Name<span>*</span></label>
                                <div class="relative">
                                    {{-- <input class="form-control rounded-lg bg-white dark:bg-neutral-700" name="name" id="name" type="text" placeholder="Enter Role Name"  > --}}
                                    <input class="form-control rounded-lg bg-white dark:bg-neutral-700" name="name" id="name" type="text" placeholder="Enter role name"  >
                                    <span class="text-red-500 text-sm mt-1 d-block" id="error-name"></span>
                                </div>
                            </div>

                            <div class="col-span-12">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Permissions</h4>
                                @foreach ($permissions as $groupName => $permissionList)
                                    <div class="mt-4">
                                        <h5 class="text-md font-semibold text-gray-800 dark:text-gray-200">{{ $groupName }}</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                                            @foreach ($permissionList as $permission)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-checkbox rounded text-primary-600">
                                                    <p class="ms-2 ml-2"> {{ ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }} </p>

                                                    {{-- <p class="ms-2 ml-2">{{ $permission->name }}</p> --}}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="error-permissions" class="text-red-500 text-sm mt-1"></div>

                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 ">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="confirmationModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this role?</h3>
                    <button type="button" id="confirmDeleteBtn"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, I'm sure
                    </button>
                    <button type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        data-modal-hide="confirmationModal">No, cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            const form = $('#roleForm');
            const methodInput = form.find("input[name='_method']");
            const modalTitle = $('#modalTitle');
            const addEditModal = new Modal($('#addEditRolesModal')[0], {
                backdrop: 'static', // prevents outside click from closing
                keyboard: false     // optional: prevents ESC key from closing
            });
            const confirmationModal = new Modal($('#confirmationModal')[0]);


            let clearModal = 0;

            const table = $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.roles.index') }}",
                columns: [
                    { data: "name", name: "name" },
                    {
                        data: "id",
                        name: "action",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="flex items-center gap-3 justify-center">
                                <button type="button" data-id="${data}" class="edit-btn bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 bg-hover-success-200 font-medium w-10 h-10 flex justify-center items-center rounded-full" title="Edit">
                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                </button>
                                <button type="button" data-id="${data}" class="delete-btn bg-danger-100 dark:bg-danger-600/25 hover:bg-danger-200 text-danger-600 dark:text-danger-500 font-medium w-10 h-10 flex justify-center items-center rounded-full" title="Delete">
                                    <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                </button>
                            </div>`;
                        }
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).find('.edit-btn').on('click', () => editRole(data.id));
                    $(row).find('.delete-btn').on('click', () => showDeleteConfirmation(data.id));
                }
            });

            $('#showAddEditRolesModal').on('click', function() {
                if(clearModal == 1){
                    form[0].reset();
                    clearModal = 0;
                    // Clear previous errors
                    form.find('span[id^="error-"]').text('');
                    $('#error-permissions').text('');
                }
                form.attr('action', "{{ route('admin.roles.store') }}");
                methodInput.val("POST");
                modalTitle.text("Create New Role");
                addEditModal.show();
            });

            $('#closeAddEditRolesModal').on('click', function() {
                addEditModal.hide();
            });

            $('[data-modal-hide="addEditRolesModal"]').on('click', () => addEditModal.hide());
            $('[data-modal-hide="confirmationModal"]').on('click', () => confirmationModal.hide());

            function editRole(id) {
                $.ajax({
                    url: `/admin/roles/${id}/edit`,
                    method: 'GET',
                    success: function(data) {

                        // Clear previous errors
                        form.find('span[id^="error-"]').text('');
                        $('#error-permissions').text('');

                        form.attr('action', `/admin/roles/${id}`);
                        methodInput.val("POST");
                        form.find("#method").val('PUT');
                        form.find("#id").val(data.id);
                        form.find("#name").val(data.name);

                        form.find(`input[name="permissions[]"]`).prop('checked', false);
                        data.permissions.forEach(function(permission) {
                            form.find(`input[name="permissions[]"][value="${permission.id}"]`).prop('checked', true);
                        });

                        modalTitle.text("Edit Role");
                        addEditModal.show();
                        clearModal =1;
                    },
                    error: function() {
                        showToast('error', "Failed to load role data.");
                    }
                });
            }

            function showDeleteConfirmation(id) {
                $('#confirmDeleteBtn').data('id', id);
                confirmationModal.show();
            }

            $('#confirmDeleteBtn').on('click', function() {
                const id = $(this).data('id');
                confirmationModal.hide();

                $.ajax({
                    url: `/admin/roles/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(data) {
                        if(data.success) {
                            table.ajax.reload();
                            showToast('success', data.message);
                        } else {
                            showToast('error', data.message);
                        }
                    },
                    error: function() {
                        showToast('error', "Error deleting role.");
                    }
                });
            });

            form.on('submit', function(e) {
                e.preventDefault();
                const url = form.attr('action');
                const method = methodInput.val();

                // Clear previous errors
                form.find('span[id^="error-"]').text('');
                $('#error-permissions').text('');

                $.ajax({
                    url: url,
                    method: method,
                    data: form.serialize(),
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(data) {
                        if (data.success) {
                            addEditModal.hide();
                            table.ajax.reload();
                            form[0].reset();
                            clearModal = 0;
                            showToast('success', data.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Validation error
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                if(field === 'permissions') {
                                    $('#error-permissions').text(errors[field][0]);
                                } else {
                                    $(`#error-${field}`).text(errors[field][0]);
                                }
                            });
                            showToast('error', "Please fix the highlighted errors.");
                        } else {
                            showToast('error', xhr.responseJSON?.message || "An error occurred while submitting the form.");
                        }
                    }
                });
            });

            form.find("input, select").on("input change", function() {
                const field = $(this).attr("name");
                if(field === 'permissions[]') {
                    $('#error-permissions').text('');
                } else {
                    $(`#error-${field}`).text('');
                }
            });

        });
    </script>
@endpush