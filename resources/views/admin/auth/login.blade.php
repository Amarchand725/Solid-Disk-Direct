@extends('admin.auth.layouts.app')
@section('title', Str::upper($title) .' | '.Str::upper(appName()))
@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
            <img
                src="{{ asset('admin/assets/img/illustrations/auth-login-illustration-light.png')}}"
                alt="auth-login-cover"
                class="img-fluid my-5 auth-illustration"
                data-app-light-img="illustrations/auth-login-illustration-light.png')}}"
                data-app-dark-img="illustrations/auth-login-illustration-dark.png')}}"
            />

            <img
                src="{{ asset('admin/assets/img/illustrations/bg-shape-image-light.png')}}"
                alt="auth-login-cover"
                class="platform-bg"
                data-app-light-img="illustrations/bg-shape-image-light.png')}}"
                data-app-dark-img="illustrations/bg-shape-image-dark.png')}}"
            />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
            <!-- Logo -->
            <x-company-logo />
            <!-- /Logo -->
            <h3 class="mb-1 fw-bold">Welcome to {{ appName() }}! 👋</h3>
            <p class="mb-4">Please sign-in to your account and start the adventure</p>

            <div id="errorMessage"></div>

            <form id="loginForm" action="{{ route('admin.login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        autofocus
                    />
                    <span id="email_error" class="text-danger error">{{ $errors->first('email') }}</span>
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>

                    </div>
                    <div class="input-group input-group-merge">
                        <input
                        type="password"
                        id="password"
                        class="form-control"
                        name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        aria-describedby="password"
                        />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    <span id="password_error" class="text-danger error">{{ $errors->first('password') }}</span>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" />
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('password.request') }}">
                                <small>Forgot Password?</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3 action-btn">
                    <div class="demo-inline-spacing sub-btn">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                    </div>
                    <div class="demo-inline-spacing loading-btn" style="display: none;">
                        <button class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                          <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                          Loading...
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <!-- /Login -->
        </div>
    </div>
@endsection
@push('js')
@endpush
