@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('backend/assets/datatable/css/datatables.min.css') }}">
@endpush
@section('title', 'Dynamic Page List')
@section('content')
    <div class="app-content content ">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Post Page List</h3>
                <div>
                    <button type='button' style='min-width: 115px;' class='btn btn-danger delete_btn d-none'
                        onclick='multi_delete()'>Bulk Delete</button>
                    <a href="{{ route('dynamicpages.create') }}" class="btn btn-primary" type="button">
                        <span>Add Dynamic Page</span>
                    </a>
                </div>
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
                                <th>Image</th>
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
                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('post.index') }}",
                        order: [],
                        lengthMenu: [
                            [25, 50, 100, -1],
                            [25, 50, 100, "All"]
                        ],
                        pagingType: "full_numbers",
                        language: {
                            processing: `<div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                             </div>`
                        },
                        columns: [{
                                data: 'bulk_check',
                                orderable: false,
                                searchable: false
                            },

                            {
                                data: 'image'
                            },
                            
                            {
                                data: 'status'
                            },
                            {
                                data: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                }
            });

            function showDeleteAlert(id) {
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
                        deleteFinal(id);
                    }
                });
            }

            function deleteFinal(id) {
                let deleteUrl = '{{ route('dynamicpages.destroy', ':id') }}'.replace(':id', id);
                fetch(deleteUrl,
                    {
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
                                'Your item has been deleted.',
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


            // Use the status change alert
            function changeStatus(event, id) {
                event.preventDefault();
                let statusUrl = '{{ route('dynamicpages.status', ':id') }}'.replace(':id', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to change the status of this dynamic page.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: statusUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 800
                                    });
                                } else if (response.errors) {
                                    Swal.fire({
                                        icon: "error",
                                        title: response.errors[0],
                                        showConfirmButton: false,
                                        timer: 800
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 800
                                    });
                                }
                                $('#data-table').DataTable().ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    response.responseJSON.error || 'An error occurred.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }


            // Use the delete confirm alert
            function deleteRecord(event, id) {
                event.preventDefault();
                let deleteUrl = '{{ route('dynamicpages.destroy', ':id') }}'.replace(':id', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                );
                                $('#basic_tables').DataTable().ajax.reload(); // Reload DataTable
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    response.responseJSON.error || 'An error occurred.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }


            function multi_delete() {
                let ids = [];
                let rows;
                // Use the DataTable instance
                let dTable = $('#data-table').DataTable();

                $('.select_data:checked').each(function() {
                    ids.push($(this).val());
                    rows = dTable.rows($(this).parents('tr')); // Use DataTable rows() method
                });

                if (ids.length == 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Please check at least one row of the table!',
                    });
                } else {
                    let url = "{{ route('dynamicpages.bulk-delete') }}";
                    bulk_delete(ids, url, rows, dTable);
                }
            }
        </script>
    @endpush
@endsection
