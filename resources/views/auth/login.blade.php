@extends('layouts.app')

@section('title', 'Login')
@section('scripts')
    @parent
@endsection

@section('styles')
    @parent

@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->

    <!-- /Logo -->
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img
                    src="{{asset('assets/img/illustrations/auth-login-illustration-light.png')}}"
                    alt="auth-login-cover"
                    class="my-5 auth-illustration" />
                <img
                    src="{{asset('assets/img/illustrations/bg-shape-image-light.png')}}"
                    alt="auth-login-cover"
                    class="platform-bg" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Welcome to {{ $appbrand->business_brand_app_name??'ERP' }}! 👋</h4>
                <p class="mb-6">Please login to your account and start the adventure</p>

                @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    <div>{{$error}}</div>
                    @endforeach
                </div>
                @endif

                <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-6 form-control-validation">
                        <label for="email" class="form-label">Email or Username</label>
                        <input
                            type="text"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Enter your email or username"
                            required
                            autofocus />
                    </div>
                    <div class="mb-6 form-password-toggle form-control-validation">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                id="password"
                                class="form-control"
                                name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"
                                required autocomplete="current-password" />
                            <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                        </div>
                    </div>
                    <div class="my-8">
                        <div class="d-flex justify-content-between">
                            <div class="form-check mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} />
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                <p class="mb-0">Forgot Password?</p>
                            </a>
                            @endif
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100">Login</button>
                </form>

            </div>
        </div>
        <!-- /Login -->
    </div>
</div>

@endsection