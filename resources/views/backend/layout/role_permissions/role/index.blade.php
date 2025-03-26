@extends('backend.app')

@section('title', 'Role')

@section('content')
    <main class="app-content content">
        <h2 class="section-title">All Roles</h2>

        <div class="card p-3 border rounded shadow-sm">
            <div class="card-body">
                <div class="table-responsive p-4">
                    <!-- Button Positioned at Top Right -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.role.create') }}" class="btn btn-primary" type="button">
                            <span>Add Role</span>
                        </a>
                    </div>
                    <br>
                    <table id="basic_tables" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $index => $role)
                                <tr id="role-{{ $role->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.role.edit', $role->id) }}" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <button onclick="deleteRole({{ $role->id }})" class="btn btn-danger text-white" title="Delete">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
<script>
    function deleteRole(id) {
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
                    url: '/role/destroy/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#role-' + id).remove();
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Something went wrong. Please try again later.',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endpush
