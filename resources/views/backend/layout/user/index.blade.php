@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('backend/assets/datatable/css/datatables.min.css') }}">
@endpush
@section('title', 'Users')
@section('content')
    <div class="app-content content ">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users List</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-4 p-4 card-datatable table-responsive pt-0">
                    <table class="table table-hover" id="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-checkbox">
                                        <input type="checkbox" class="form-check-input" id="select_all"
                                            onclick="select_all()">
                                        <label class="form-check-label" for="select_all"></label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                var searchable = [];
                var selectable = [];
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    }
                });
                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    let dTable = $('#data-table').DataTable({
                        order: [],
                        lengthMenu: [
                            [25, 50, 100, 200, 500, -1],
                            [25, 50, 100, 200, 500, "All"]
                        ],
                        processing: true,
                        responsive: true,
                        serverSide: true,

                        language: {
                            processing: `<div class="text-center">
                                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>`
                        },

                        scroller: {
                            loadingIndicator: false
                        },
                        pagingType: "full_numbers",
                        dom: "<'row justify-content-between table-topbar'<'col-md-2 col-sm-4 px-0'l><'col-md-2 col-sm-4 px-0'f>>tipr",
                        ajax: {
                            url: "{{ route('user.list') }}",
                            type: "get",
                        },

                        columns: [{
                                data: 'bulk_check',
                                name: 'bulk_check',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'name',
                                name: 'name',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'email',
                                name: 'email',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        ],
                    });

                    new DataTable('#example', {
                        responsive: true
                    });
                }
            });

            function showDeleteConfirm(id, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteArticle(id, deleteUrl);
                    }
                });
            }

            function deleteArticle(id, deleteUrl) {
                fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Your article has been deleted.',
                                'success'
                            );

                            var table = $('#data-table').DataTable();

                            table.row('#article-' + id).remove().draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the article.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An unexpected error occurred.',
                            'error'
                        );
                    });
            }


            // Status Change Confirm Alert
            function showStatusChangeAlert(id) {
                event.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to update the status?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        statusChange(id);
                    }
                });
            }

            // Status Change
            function statusChange(id) {
                let url = '{{ route('user.status', ':id') }}';
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', id),
                    success: function(resp) {
                        if (resp.success) {
                            Swal.fire({
                                icon: "success",
                                title: resp.message,
                                showConfirmButton: false,
                                timer: 800
                            });
                        } else if (resp.errors) {
                            Swal.fire({
                                icon: "error",
                                title: resp.errors[0],
                                showConfirmButton: false,
                                timer: 800
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: resp.message,
                                showConfirmButton: false,
                                timer: 800
                            });
                        }
                        $('#data-table').DataTable().ajax.reload();
                    },
                    error: function(error) {
                        // location.reload();
                    }
                })
            }
        </script>

<script>
    async function deleteUser(id) {
        const {
            value: password
        } = await Swal.fire({
            icon: 'info',
            title: "Are you sure you want to delete this account?",
            input: "password",
            inputLabel: "Enter your password",
            inputPlaceholder: "Enter your password",
            inputAttributes: {
                maxlength: "100",
                autocapitalize: "off",
                autocorrect: "off"
            },
            confirmButtonText: "Yes",
            showCancelButton: true,
            cancelButtonText: "No"
        });

        if (password) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('password', password);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('user.user.destroy') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) { // Handle incorrect password case
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Incorrect Password',
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something Went Wrong',
                        });
                    }
                }
            });
        }
    }
</script>
    @endpush
@endsection
