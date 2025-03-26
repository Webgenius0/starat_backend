@extends('backend.app')
@section('title', 'Users_Edit')


@section('content')
    <div class="app-content content ">
        <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data">@csrf
            <input type="hidden" name="id" value="{{ $user->id }}">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="card card-body">
                        <h4 class="mb-4">{{ $role }} <span id="Categorytitle">Images</span></h4>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label">Avatar</label>
                            <div class="col-9">
                                <img id="A" class="mb-2" width="80" height="80"
                                    src="{{ asset($user->avatar) }}" alt=""><br>
                                <input oninput="A.src=window.URL.createObjectURL(this.files[0])" class="form-control-sm"
                                    type="file" name="avatar">
                            </div>
                        </div>
                    </div>
                    <div class="card card-body">
                        <h4 class="mb-4">{{ $role }} <span id="Categorytitle">Info</span></h4>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Name</i></label>
                            <div class="col-9">
                                <input type="text" name="name" class="form-control" placeholder="Name..."
                                    value="{{ old('name', $user->name) }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Username</i></label>
                            <div class="col-9">
                                <input type="text" name="username" class="form-control" placeholder="username..."
                                    value="{{ old('username', $user->username) }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>Email</i></label>
                            <div class="col-9">
                                <input type="text" name="email" class="form-control" placeholder="email..."
                                    value="{{ old('email', $user->email) }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="" class="col-3 col-form-label"><i>New Password</i></label>
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
                                        <i class="ri-save-line"></i> Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-6">
                    <div class="card card-body">
                        <h4 class="mb-4">More <span id="Categorytitle">Information</span></h4>

                    </div>
                </div> --}}

            </div>

        </form>
    </div>
@endsection


@push('script')
@endpush
