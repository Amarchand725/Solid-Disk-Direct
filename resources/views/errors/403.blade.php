@extends('admin.auth.layouts.app')
@section('title',  ($exception->getStatusCode() ?? 'Unknown'). '-'. ' Authorized')
@section('content')
  <div class="container-xxl container-p-y d-flex flex-column align-items-center justify-content-center min-vh-100 text-center">
    <div class="misc-wrapper">
      <h2 class="mb-1 mt-4">You are not authorized!</h2>
      <p class="mb-4 mx-2">{{ $exception->getMessage() ?: 'Oops! 😖 Something went wrong.' }}</p>
      <a href="{{ route('admin.login') }}" class="btn btn-primary mb-4">Back to home</a>
      <div class="mt-4">
        <img src="{{ asset('admin') }}/assets/img/illustrations/page-misc-error.png"
          alt="page-misc-error"
          width="225"
          class="img-fluid"
        />
      </div>
    </div>
  </div>
  <div class="container-fluid misc-bg-wrapper position-fixed w-100 h-100 d-flex align-items-center justify-content-center">
    <img
      src="{{ asset('admin') }}/assets/img/illustrations/bg-shape-image-light.png"
      alt="page-misc-error"
      data-app-light-img="illustrations/bg-shape-image-light.png"
      data-app-dark-img="illustrations/bg-shape-image-dark.png"
    />
  </div>
@endsection
