<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">

    <!-- Custom DataTables Styling -->
    <style>
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: .25rem;
            padding: .25rem .5rem;
        }
        .dataTables_paginate .paginate_button {
            padding: .25rem .5rem;
            margin-left: .25rem;
            border-radius: .25rem;
            border: 1px solid #ced4da;
            background-color: #fff;
            color: #495057;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
        }
        .dataTables_paginate .paginate_button.disabled {
            opacity: .5;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-light p-4">

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <h3 class="fw-bold text-primary" ><a class="text-black" href="{{url('/beft/roles')}}">Roles Management</a></h3>
            <h3 class="fw-bold "> <a  href="{{url('/beft/permissions')}}"> Permission Management</a></h3>

        </div>
        <button id="showAddEditRolesModal" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add New Permission
        </button>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="showToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div id="toastMessage" class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="hideToast()"></button>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="roles-table" class="table table-bordered table-striped w-100">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Group Name</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addEditRolesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form id="roleForm">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" name="id" id="role_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter permission name">
                        <div class="text-danger mt-1" id="error-name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Permission Group Name <span class="text-danger">*</span></label>
                        <input type="text" name="group_name" id="group_name" class="form-control" placeholder="Enter group name">
                        <div class="text-danger mt-1" id="error-group_name"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="submit-text">Save Permission</span>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                </div>
                <h5>Are you sure?</h5>
                <p>Do you really want to delete this permission? This action cannot be undone.</p>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger me-2">
                    Yes, delete
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    const roleForm = $('#roleForm');
    const methodInput = $('#method');
    const modalTitle = $('#modalTitle');
    const submitBtn = $('#submitBtn');
    let currentDeleteId = null;

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $('#roles-table').DataTable({
        ajax: "/beft/permissions",
        columns: [
            {  data: "name", name: "name",
                render: function(data) {
                    let parts = data.split('.');
                    return parts.map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                }
            },
            { data: "group_name" },
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

    function showToast(message, type='success'){
        const toastEl = $('#showToast');
        toastEl.removeClass('bg-success bg-danger').addClass(type==='success'?'bg-success':'bg-danger');
        $('#toastMessage').text(message);
        const toast = new bootstrap.Toast(toastEl.parent()[0]);
        toast.show();
    }

    function resetForm(){
        roleForm[0].reset();
        methodInput.val('POST');
        modalTitle.text('Create New Permission');
        roleForm.find('.text-danger').text('');
    }

    $('#showAddEditRolesModal').on('click', () => { resetForm(); new bootstrap.Modal($('#addEditRolesModal')[0]).show(); });

    $('#roles-table').on('click', '.edit-btn', function(){
        const id = $(this).data('id');
        resetForm();
        modalTitle.text('Edit Permission');
        $.get(`/beft/permissions/${id}/edit`, function(data){
            $('#role_id').val(data.id);
            $('#name').val(data.name.split('.')[1] ?? data.name);
            $('#group_name').val(data.group_name);
            methodInput.val('PUT');
            new bootstrap.Modal($('#addEditRolesModal')[0]).show();
        }).fail(() => showToast('Error fetching permission data', 'danger'));
    });

    $('#roles-table').on('click', '.delete-btn', function(){ currentDeleteId = $(this).data('id'); new bootstrap.Modal($('#confirmationModal')[0]).show(); });

    $('#confirmDeleteBtn').on('click', function(){
        const btn = $(this);
        btn.prop('disabled', true).find('span.spinner-border').removeClass('d-none');
        $.ajax({
            url: `/beft/permissions/${currentDeleteId}`,
            method: 'DELETE',
            success: data => { table.ajax.reload(); showToast(data.message, 'success'); $('#confirmationModal').modal('hide'); },
            error: xhr => showToast(xhr.responseJSON?.message || 'Error', 'danger'),
            complete: () => btn.prop('disabled', false).find('span.spinner-border').addClass('d-none')
        });
    });

    roleForm.on('submit', function(e){
        e.preventDefault();
        const btn = submitBtn;
        btn.prop('disabled', true).find('span.spinner-border').removeClass('d-none');
        const id = $('#role_id').val();
        const url = id ? `/beft/permissions/${id}` : `/beft/permissions`;
        $.ajax({
            url: url,
            method: methodInput.val(),
            data: roleForm.serialize(),
            success: data => { table.ajax.reload(); $('#addEditRolesModal').modal('hide'); showToast(data.message, 'success'); },
            error: xhr => {
                if(xhr.status === 422){
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(f => $(`#error-${f}`).text(errors[f][0]));
                } else showToast(xhr.responseJSON?.message || 'Error', 'danger');
            },
            complete: () => btn.prop('disabled', false).find('span.spinner-border').addClass('d-none')
        });
    });
});
</script>
</body>
</html>
