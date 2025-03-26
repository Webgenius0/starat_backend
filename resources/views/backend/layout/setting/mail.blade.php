@extends('backend.app')

@section('title', 'Mail Setting')

@push('style')
    <style>
        .nav-tabs.nav-justified {
            width: 400px;
        }
    </style>
@endpush

@section('content')
    <!--app-content open-->
    <div class="app-content content">
        <div class="side-app">
            <div class="main-container container-fluid">
                <div class="row" id="user-profile">
                    <div class="col-lg-12">

                        <div class="tab-content mt-4">
                            <div class="tab-pane fade {{ session('type') ? (session('type') == 'profile' ? 'show active' : '') : 'show active' }}"
                                id="editProfile">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Mail Setting</h5>
                                        <p class="card-description">Setup your system mail, please <code>provide your valid
                                                data</code>.</p>
                                        <form class="forms-sample" action="{{ route('admin.setting.mailstore') }}"
                                            method="POST">
                                            @csrf
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <label class="form-lable">MAIL MAILER</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_mailer') is-invalid @enderror"
                                                        placeholder="MAIL MAILER" name="mail_mailer"
                                                        value="{{ env('MAIL_MAILER') }}" required>
                                                    @error('mail_mailer')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label class="form-lable">MAIL HOST</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_host') is-invalid @enderror"
                                                        placeholder="MAIL HOST" name="mail_host"
                                                        value="{{ env('MAIL_HOST') }}" required>
                                                    @error('mail_host')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <label class="form-lable">MAIL PORT</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_port') is-invalid @enderror"
                                                        placeholder="MAIL PORT" name="mail_port"
                                                        value="{{ env('MAIL_PORT') }}" required>
                                                    @error('mail_port')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label class="form-lable">MAIL USERNAME</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_username') is-invalid @enderror"
                                                        placeholder="MAIL USERNAME" name="mail_username"
                                                        value="{{ env('MAIL_USERNAME') }}" required>
                                                    @error('mail_username')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <label class="form-lable">MAIL PASSWORD</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_password') is-invalid @enderror"
                                                        placeholder="MAIL PASSWORD" name="mail_password"
                                                        value="{{ env('MAIL_PASSWORD') }}" required>
                                                    @error('mail_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label class="form-lable">MAIL ENCRYPTION</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_encryption') is-invalid @enderror"
                                                        placeholder="MAIL ENCRYPTION" name="mail_encryption"
                                                        value="{{ env('MAIL_ENCRYPTION') }}" required>
                                                    @error('mail_encryption')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <div class="col-6">
                                                    <label class="form-lable">MAIL FROM ADDRESS</label>
                                                    <input type="text"
                                                        class="form-control form-control-md border-left-0 @error('mail_from_address') is-invalid @enderror"
                                                        placeholder="MAIL FROM ADDRESS" name="mail_from_address"
                                                        value="{{ env('MAIL_FROM_ADDRESS') }}" required>
                                                    @error('mail_from_address')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
