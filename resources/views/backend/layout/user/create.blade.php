@extends('backend.app')
@section('title', 'Create User')

@push('style')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
@endpush
@section('content')
    <div class="app-content content">
        <div class="row">
            <div class="col-lg-6 m-auto">
                <form action="{{ route('user.store') }}" method="POST">@csrf
                    <div class="card card-body">
                        <h4 class="mb-4">User <span id="Categorytitle">Create</span></h4>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label">User Role</label>
                            <div class="col-9">
                                <select name="role" class="form-control" id="">
                                    <option value="">-- Select User Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Name</i></label>
                            <div class="col-9">
                                <input type="text" name="name" class="form-control" placeholder="Name..."
                                    value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Username</i></label>
                            <div class="col-9">
                                <input type="text" name="username" class="form-control" placeholder="username..."
                                    value="{{ old('username') }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Phone Number</i></label>
                            <div class="col-9">
                                <input type="number" name="phone" class="form-control" placeholder="phone number..."
                                    value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Birth Day</i></label>
                            <div class="col-9">
                                <input type="text" name="birth_day" id="datepicker" class="form-control"
                                    placeholder="04/24/2025" value="">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Email</i></label>
                            <div class="col-9">
                                <input type="text" name="email" class="form-control" placeholder="email..."
                                    value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Password</i></label>
                            <div class="col-9">
                                <input type="password" name="password" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Confirm Password</i></label>
                            <div class="col-9">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="ri-save-line"></i> Create
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker();
        });
    </script>
@endpush
