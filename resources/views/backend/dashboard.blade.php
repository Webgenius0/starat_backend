@extends('backend.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    <div class="row match-height">
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="info">
                                        <h5>{{ $greetings['message'] }}, {{ auth()->user()->name }}</h5>
                                        <p class="card-text font-small-3">What's your plan today ?</p>
                                    </div>
                                    <div class="img">
                                        @if($greetings['type'] == 'morning')
                                        <img src="{{ asset('backend/assets/greetings/004-sunrise.png') }}"
                                        alt="Medal Pic" />
                                        @elseif ($greetings['type'] == 'afternoon')
                                        <img src="{{ asset('backend/assets/greetings/002-sunsets.png') }}">
                                        @else
                                        <img src="{{ asset('backend/assets/greetings/003-cloudy-night.png') }}">
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
@endsection
