<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
</head>
<body class="p-4">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Roles Management</h3>
        <button id="showAddEditRolesModal" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Role
        </button>
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
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name">
                        <div class="text-danger mt-1" id="error-name"></div>
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
                        <div class="text-danger mt-1" id="error-permissions"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Role</button>
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
                <p class="mb-4">Do you really want to delete this role?</p>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger me-2">Yes, delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
$(document).ready(function() {
    const roleForm = $('#roleForm');
    const methodInput = $('#method');
    const modalTitle = $('#modalTitle');
    const addEditModal = new bootstrap.Modal($('#addEditRolesModal')[0], { backdrop: 'static', keyboard: false });
    const confirmationModal = new bootstrap.Modal($('#confirmationModal')[0]);
    let currentDeleteId = null;

    $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('beft/roles/index') }}",
        columns: [
            { data: "name", name: "name" },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
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

    // Show add role modal
    $('#showAddEditRolesModal').on('click', function() {
        roleForm[0].reset();
        methodInput.val('POST');
        modalTitle.text('Create New Role');
        $('#role_id').val('');
        $('#error-permissions').text('');
        roleForm.find('div.text-danger[id^="error-"]').text('');
        addEditModal.show();
    });

    // Edit role
    $('#roles-table').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get(`/beft/roles/${id}/edit`, function(data) {
            roleForm[0].reset();
            $('#role_id').val(data.id);
            $('#name').val(data.name);
            methodInput.val('PUT');
            modalTitle.text('Edit Role');
            roleForm.find('input[name="permissions[]"]').prop('checked', false);
            data.permissions.forEach(p => $(`input[name="permissions[]"][value="${p.id}"]`).prop('checked', true));
            addEditModal.show();
        });
    });

    // Delete role
    $('#roles-table').on('click', '.delete-btn', function() {
        currentDeleteId = $(this).data('id');
        confirmationModal.show();
    });

    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: `/beft/roles/${currentDeleteId}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data) {
                table.ajax.reload();
                confirmationModal.hide();
                alert(data.message || 'Role deleted successfully.');
            },
            error: function() { alert('Error deleting role.'); }
        });
    });

    // Submit form
    roleForm.on('submit', function(e) {
        e.preventDefault();
        const id = $('#role_id').val();
        const url = id ? `/beft/roles/${id}` : "/beft/roles";
        const method = methodInput.val();

        $.ajax({
            url: url,
            method: method,
            data: roleForm.serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    table.ajax.reload();
                    addEditModal.hide();
                    roleForm[0].reset();
                    alert(data.message || 'Role saved successfully.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        if (field === 'permissions') $('#error-permissions').text(errors[field][0]);
                        else $(`#error-${field}`).text(errors[field][0]);
                    });
                } else {
                    alert('An error occurred.');
                }
            }
        });
    });

});
</script>

</body>
</html>
