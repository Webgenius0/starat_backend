@extends('backend.app')

@section('title', 'FAQ Page')

@section('content')
    <main class="app-content content">
        <h2 class="section-title">All Permissions</h2>

        <div class="card p-3 border rounded shadow-sm">
            <div class="card-body">
                <div class="table-responsive p-4">
                    <!-- Button Positioned at Top Right -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary" type="button">
                            <span>Add Permissions</span>
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
                            @foreach ($permissions as $index => $permission)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <a href="" class="btn btn-warning">Edit</a>
                                        <a href="" class="btn btn-danger">Delete</a>
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
