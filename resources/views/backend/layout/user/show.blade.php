@extends('backend.app')
@section('title', 'General Setting')
@section('content')
    <div class="app-content content ">
        <div class="container mt-5">
            <!-- User Information Section -->
            <div class="card card-body mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Avatar -->
                        <img src="{{ asset($user->avatar) ?? '' }}" alt="User Avatar" class="img-fluid rounded-circle"
                            width="150">
                    </div>
                    <div class="col-md-8">
                        <!-- User Details -->
                        <h3>{{ $user->name ?? 'No Name' }}</h3>
                        <p><strong>Username:</strong> {{ $user->username ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                        <p><strong>Status:</strong>
                            @if ($user->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
