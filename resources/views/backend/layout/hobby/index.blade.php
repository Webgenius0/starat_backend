@extends('backend.app')

@section('title', 'Hobby Page')

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
            <div class="col-lg-12 mb-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">Hobby <span id="Brandtitle">Create</span></h4>
                    </div>
                    <div class="card-body">
                        <form id="createBrand" action="{{ route('hobby.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Name</label>
                                        <div class="col-9">
                                            <input type="text" name="name" class="form-control" placeholder="name..."
                                                value="{{ old('name') }}">
                                            <div style="display: none" class="text-danger nameExists">
                                                <i>Brand Name </i>Already Exists
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Priority</label>
                                        <div class="col-9">
                                            <input type="number" name="priority" class="form-control"
                                                placeholder="ranking..." value="{{ old('priority') }}">
                                            <div style="display: none" class="text-danger priorityExists">
                                                This Rank Already Taken
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-4"> --}}

                                    {{-- <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Image <span
                                                class="text-danger">(55 x 65)</span></label>
                                        <div class="col-9">
                                            <img id="I" class="mb-2" width="80" height="80"
                                                src="{{ asset('default.jpg') }}" alt=""><br>
                                            <input oninput="I.src=window.URL.createObjectURL(this.files[0])"
                                                class="form-control-sm" type="file" name="image">

                                        </div>
                                    </div> --}}
                                {{-- </div> --}}
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form style="display: none;" id="editBrand" action="{{ route('hobby.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Name</label>
                                        <div class="col-9">
                                            <input type="text" name="name" class="form-control" placeholder="name...">
                                            <div style="display: none" class="text-danger nameExists">
                                                <i>Brand Name</i> Already Exists
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Priority</label>
                                        <div class="col-9">
                                            <input type="number" name="priority" class="form-control"
                                                placeholder="ranking...">
                                            <div style="display: none" class="text-danger priorityExists">
                                                This Rank Already Taken
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <div class="row mb-2">
                                        <label for="inputEmail3" class="col-3 col-form-label">Image <span
                                                class="text-danger">(55 x 65)</span></label>
                                        <div class="col-9">
                                            <img id="IE" class="mb-2" width="80" height="80"
                                                src="{{ asset('default.jpg') }}" alt=""><br>
                                            <input oninput="IE.src=window.URL.createObjectURL(this.files[0])"
                                                class="form-control-sm" type="file" name="image">
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
            <div class="col-lg-12 mb-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="m-0">Hobby List</h4>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
    <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>
    <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    {{-- Update Rank --}}
    <script>
        $(function() {
            $("tbody").sortable({
                update: function(e, ui) {
                    $("tbody tr").each(function(index) {
                        $(this).find("td:nth-child(1)").text(index +
                            1);
                    });

                    updateRanks();
                }
            });

            function updateRanks() {
                var ranks = [];
                $("tbody tr").each(function(index) {
                    var BrandId = $(this).data("id");
                    var rank = index + 1;
                    ranks.push({
                        id: BrandId,
                        rank: rank
                    });
                });

                $.ajax({
                    url: "{{ route('hobby.priority') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ranks: ranks
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({
                                icon: 'success',
                                title: 'Categories priorities updated successfully.'
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Failed to update priorities.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating ranks:', error);
                    }
                });
            }
        });
    </script>
    {{-- Datatable --}}
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            $(document).ready(function() {
                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('hobby.index') }}",
                        columns: [{
                                data: 'priority',
                                name: 'priority'
                            },
                            {
                                data: 'name',
                                name: 'name'
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
                            }
                        ]
                    });
                }
            });

        });
    </script>
    {{-- Exists Check --}}
    <script>
        $('input[name="name"]').on('keyup', function() {
            let input = $(this).val();
            let cateId = $('input[name="id"]').val();
            checkBrandName(cateId, input);
        });

        function checkBrandName(id, name) {
            $.ajax({
                url: "{{ route('hobby.get') }}",
                type: "GET",
                data: {
                    id: id,
                    name: name,
                },
                success: function(response) {
                    if (response.length > 0) {
                        $('.nameExists').show();
                    } else {
                        $('.nameExists').hide();
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('input[name="priority"]').on('keyup', function() {
            let input = $(this).val();
            let cateId = $('input[name="id"]').val();
            checkBrandPriority(cateId, input);
        });

        function checkBrandPriority(id, priority) {
            $.ajax({
                url: "{{ route('hobby.get') }}",
                type: "GET",
                data: {
                    id: id,
                    priority: priority,
                },
                success: function(response) {
                    if (response.length > 0) {
                        $('.priorityExists').show();
                    } else {
                        $('.priorityExists').hide();
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
    {{-- Edit --}}
    <script>
        function editBrand(id) {
            $.ajax({
                url: "{{ route('hobby.get') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response) {
                        let imagePath = "{{ asset('') }}" + response[0].image;

                        $('#Brandtitle').text('Update');
                        $('#editBrand').show();
                        $('#createBrand').hide();
                        $('#editBrand input[name="id"]').val(response[0].id);
                        $('#editBrand input[name="name"]').val(response[0].name);
                        $('#editBrand input[name="priority"]').val(response[0].priority);
                        $('#editBrand #IE').attr('src', imagePath);

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
    {{-- Status --}}
    <script>
        function statusBrand(id) {
            $.ajax({
                url: "{{ route('hobby.status') }}",
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
