<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>

<body class="p-4">

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Roles Management</h3>
            <button id="showAddEditRolesModal" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Role
            </button>
        </div>

        <!-- Toast Notification -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div id="showToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="roles-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div class="modal fade" id="addEditRolesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="roleForm">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" name="id" id="role_id">
                    <div class="modal-body">
                        <div id="editLoading" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="formContent">
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name" aria-describedby="error-name">
                                <div class="invalid-feedback mt-1" id="error-name"></div>
                            </div>

                            <h6>Permissions</h6>
                            <div class="mb-3" id="permissions-list">
                                @foreach ($permissions as $groupName => $permissionList)
                                    <div class="mb-2">
                                        <strong>{{ $groupName }}</strong>
                                        <div class="row mt-1">
                                            @foreach ($permissionList as $permission)
                                                <div class="col-6 col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}">
                                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                            {{ ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                                <div class="invalid-feedback mt-1" id="error-permissions"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="submit-text">Save Role</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="mb-4">Do you really want to delete this role? This action cannot be undone.</p>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger me-2">
                        <span class="delete-text">Yes, delete</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
 $(document).ready(function() {
    // --- Cache DOM Elements ---
    const roleForm = $('#roleForm');
    const methodInput = $('#method');
    const modalTitle = $('#modalTitle');
    const addEditModal = new bootstrap.Modal($('#addEditRolesModal')[0]);
    const confirmationModal = new bootstrap.Modal($('#confirmationModal')[0]);
    const toastEl = document.getElementById('showToast');
    const toast = new bootstrap.Toast(toastEl);
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
        ajax: "/beft/roles",
        columns: [
            { data: "name", name: "name" },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-success btn-sm edit-btn" data-id="${data}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">Delete</button>
                        </div>
                    `;
                }
            }
        ]
    });

    // --- Utility Functions ---
    function showToast(message, type = 'success') {
        const toastBody = $('#toastMessage');
        const toastElement = $('#showToast');
        toastBody.text(message);
        toastElement.removeClass('bg-success bg-danger').addClass(`bg-${type === 'success' ? 'success' : 'danger'} text-white`);
        toast.show();
    }

    function setLoadingState(button, loading = true) {
        const textSpan = button.find('span').first();
        const spinner = button.find('.spinner-border');
        if (loading) {
            button.prop('disabled', true);
            textSpan.addClass('d-none');
            spinner.removeClass('d-none');
        } else {
            button.prop('disabled', false);
            textSpan.removeClass('d-none');
            spinner.addClass('d-none');
        }
    }

    function resetForm() {
        roleForm[0].reset();
        methodInput.val('POST');
        modalTitle.text('Create New Role');
        $('#role_id').val('');
        roleForm.find('.invalid-feedback').text('');
        roleForm.find('.form-control, .form-check-input').removeClass('is-invalid');
    }

    // --- Event Handlers ---

    // Show add role modal
    $('#showAddEditRolesModal').on('click', function() {
        resetForm();
        addEditModal.show();
    });

    // Edit role
    $('#roles-table').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        resetForm();
        modalTitle.text('Edit Role');

        // Show loading state in modal
        formContent.addClass('d-none');
        editLoading.removeClass('d-none');
        addEditModal.show();

        $.get(`/beft/roles/${id}/edit`, function(data) {
            $('#role_id').val(data.id);
            $('#name').val(data.name);
            methodInput.val('PUT');
            data.permissions.forEach(p => $(`input[name="permissions[]"][value="${p.id}"]`).prop('checked', true));
        }).fail(function() {
            showToast('Error fetching role data.', 'error');
            addEditModal.hide();
        }).always(function() {
            formContent.removeClass('d-none');
            editLoading.addClass('d-none');
        });
    });

    // Delete role
    $('#roles-table').on('click', '.delete-btn', function() {
        currentDeleteId = $(this).data('id');
        confirmationModal.show();
    });

    $('#confirmDeleteBtn').on('click', function() {
        setLoadingState(confirmDeleteBtn, true);
        $.ajax({
            url: `/beft/roles/${currentDeleteId}`,
            method: 'DELETE',
            success: function(data) {
                table.ajax.reload();
                confirmationModal.hide();
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
        $(this).removeClass('is-invalid');
        $('#error-name').text('');
    });

    $('input[name="permissions[]"]').on('change', function() {
        $('#error-permissions').text('');
    });

    // Submit form
    roleForm.on('submit', function(e) {
        e.preventDefault();
        setLoadingState(submitBtn, true);

        const id = $('#role_id').val();
        const url = id ? `/beft/roles/${id}` : "/beft/roles";
        const method = methodInput.val();

        $.ajax({
            url: url,
            method: method,
            data: roleForm.serialize(),
            success: function(data) {
                table.ajax.reload();
                addEditModal.hide();
                showToast(data.message, 'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    roleForm.find('.form-control, .form-check-input').removeClass('is-invalid');
                    roleForm.find('.invalid-feedback').text('');

                    Object.keys(errors).forEach(function(field) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
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

    // Reset form state when modal is fully hidden
    $('#addEditRolesModal').on('hidden.bs.modal', function () {
        resetForm();
    });

});
</script>

</body>
</html>