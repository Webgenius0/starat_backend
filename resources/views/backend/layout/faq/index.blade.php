@extends('backend.app')

@section('title', 'FAQ Page')

@push('style')
    <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />
    <style>
        .dropify-wrapper {
            width: 160px;
        }

        #data-table th,
        #data-table td {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
@endpush
@section('content')
    <main class="app-content content">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">FAQ <span id="faqtitle">Create</span></h4>
                    </div>
                    <div class="card-body">
                        <form id="createfaq" action="{{ route('faq.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Question ?</label>
                                        <div class="col-9">
                                            <textarea name="que" class="form-control" id="" placeholder="question..?" cols="5" rows="2">{{ old('que') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Answer</label>
                                        <div class="col-9">
                                            <textarea name="ans" class="ck-editor form-control @error('ans') is-invalid @enderror">{{ old('ans') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form style="display: none;" id="editfaq" action="{{ route('faq.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Question ?</label>
                                        <div class="col-9">
                                            <textarea name="que" class="form-control" id="" placeholder="question..?" cols="5" rows="2">{{ old('que') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Answer</label>
                                        <div class="col-9">
                                            <textarea id="ans" name="ans" class="ck-editor form-control @error('ans') is-invalid @enderror">{{ old('ans') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">FAQ List</h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="accordionExample">
                            @forelse ($faqs as $key => $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" style="display: flex;">
                                        <button style="flex: 25; border-radius: 0;"
                                            class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}"
                                            aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $item->id }}">
                                            <span>{{ $key+1 . "." }}</span>{{ $item->que }}
                                        </button>
                                        <button style="flex: 1; border-radius:0;padding: .8rem 1.25rem;"
                                            onclick="editfaq({{ $item->id }})" type="button" class="btn btn-info"><i
                                                class="mdi mdi-pencil"></i>
                                        </button>
                                        <button style="flex: 1; border-radius:0;padding: .8rem 1.25rem;" type="button"
                                            name="{{ route('faq.destroy',$item->id) }}" class="btn btn-danger del"><i class="mdi mdi-delete"></i>
                                        </button>
                                        <button style="flex: 3; border-radius:0;padding: .8rem 1.25rem;" type="button"
                                            onclick="statusfaq({{ $item->id }})"
                                            class="btn btn-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $item->status == 'active' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </h2>

                                    <div id="collapse{{ $item->id }}"
                                        class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            {!! $item->ans !!}
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="alert alert-info">
                                    No Data Found
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
    <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

    <script>
        let editors = [];

        document.querySelectorAll('.ck-editor').forEach(editorElement => {
            ClassicEditor
                .create(editorElement, {
                    removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption',
                        'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'
                    ],
                    height: '500px'
                })
                .then(editor => {
                    editors.push(editor); // Store the editor instance for later use
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>


    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
        });
    </script>

    <script>
        function editfaq(id) {
            $.ajax({
                url: "{{ route('faq.get') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response) {
                        $('#faqtitle').text('Update');
                        $('#editfaq').show();
                        $('#createfaq').hide();
                        $('#editfaq input[name="id"]').val(response[0].id);
                        $('#editfaq textarea[name="que"]').val(response[0].que);

                        const editor = editors[1];
                        if (editor) {
                            editor.setData(response[0].ans); // Set content in CKEditor
                        }

                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
    <script>
        function statusfaq(id) {
            $.ajax({
                url: "{{ route('faq.status') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something Went Wrong'
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endpush
