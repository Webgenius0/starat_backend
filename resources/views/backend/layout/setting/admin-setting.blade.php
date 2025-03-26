@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
    <style>
        .dropify-wrapper{
            height: inherit !important;
        }
    </style>
@endpush
@section('title', 'Admin Setting')
@section('content')

    <div class="app-content content ">
        <!-- General setting Form section start -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Admin Panel Setting</h3>
            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('admin.settingupdate') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="country">Logo</label>
                                <input class="form-control dropify" type="file" name="admin_logo"
                                    @isset($setting->admin_logo)
                                                   data-default-file="{{ asset($setting->admin_logo) }}"
                                    @endisset>
                                @error('admin_logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- <div class="form-group">
                                <label for="country">Mini Logo</label>
                                <input class="form-control dropify" type="file" name="admin_mini_logo"
                                    @isset($setting->admin_mini_logo)
                                                   data-default-file="{{ asset($setting->admin_mini_logo) }}"
                                    @endisset>
                                @error('admin_mini_logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div> --}}
                            <div class="form-group">
                                <label for="country">Favicon</label>
                                <input class="form-control dropify" type="file" name="admin_favicon"
                                    @isset($setting->admin_favicon)
                                                data-default-file="{{ asset($setting->admin_favicon) }}"
                                    @endisset>
                                @error('admin_favicon')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6 col-6">
                                    <div class="form-group">
                                        <label for="first-name-column">Title</label>
                                        <input type="text" id="admin_title" class="form-control"
                                            value="{{ $setting->admin_title ?? '' }}" placeholder="Admin Title"
                                            name="admin_title" />
                                        @error('admin_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-5">
                                    <div class="form-group">
                                        <label for="first-name-column">Short Title</label>
                                        <input type="text" id="admin_short_title" class="form-control"
                                            value="{{ $setting->admin_short_title ?? '' }}" placeholder="Admin Short Title"
                                            name="admin_short_title" />
                                        @error('admin_short_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 col-12">
                                    <div class="form-group">
                                        <label for="country">Copyright text</label>
                                        <input type="text" class="form-control" name="admin_copyright_text"
                                            id="admin_copyright_text" value="{{ $setting->admin_copyright_text ?? '' }}"
                                            placeholder="Copyright Text">
                                        @error('admin_copyright_text')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 col-12 mt-3 text-end">
                                    <button type="submit" class="btn btn-primary mr-1">Update</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

    <script>
        $('.dropify').dropify();
    </script>
@endpush
