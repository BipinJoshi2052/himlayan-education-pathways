<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" style="--bs-primary: #3a57e8;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Sign in - {{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('admin-assets/css/hope-ui.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/css/admin.css') }}">
    </head>
    <body>
        <div class="wrapper">
            <section class="login-content">
                <div class="row m-0 align-items-center bg-white vh-100">
                    <div class="col-md-6">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                    <div class="card-body z-3 px-md-0 px-lg-4">
                                        <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center mb-3">
                                            <div class="logo-main">
                                                <div class="logo-normal">
                                                    <svg class="text-primary icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                                                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                                                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                                                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <h4 class="logo-title ms-3">{{ config('app.name', 'Laravel') }}</h4>
                                        </a>

                                        <h2 class="mb-2 text-center">Sign In</h2>
                                        <p class="text-center">Login to stay connected.</p>

                                        @if ($errors->any())
                                            <div class="alert alert-danger" role="alert">
                                                {{ $errors->first() }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder=" ">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="password" class="form-label">Password</label>
                                                        <div class="position-relative">
                                                            <input type="password" class="form-control pe-5" id="password" name="password" required placeholder=" ">
                                                            <span class="password-toggle-icon" id="password-toggle" role="button" tabindex="0" aria-label="Show password">
                                                                <svg class="icon-20 eye-open" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                                                </svg>
                                                                <svg class="icon-20 eye-closed d-none" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M3 3l18 18M10.584 10.587a2 2 0 0 0 2.828 2.83M9.363 5.365A9.466 9.466 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-3.223 4.19M6.52 6.519C3.624 8.2 2 12 2 12a13.2 13.2 0 0 0 4.08 4.775" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 d-flex justify-content-between">
                                                    <div class="form-check mb-3">
                                                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                                        <label class="form-check-label" for="remember">Remember Me</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary">Sign In</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sign-bg">
                            <svg width="280" height="230" viewBox="0 0 431 398" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.05">
                                    <rect x="-157.085" y="193.773" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 -157.085 193.773)" fill="#3B8AFF"/>
                                    <rect x="7.46875" y="358.327" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 7.46875 358.327)" fill="#3B8AFF"/>
                                    <rect x="61.9355" y="138.545" width="310.286" height="77.5714" rx="38.7857" transform="rotate(45 61.9355 138.545)" fill="#3B8AFF"/>
                                    <rect x="62.3154" y="-190.173" width="543" height="77.5714" rx="38.7857" transform="rotate(45 62.3154 -190.173)" fill="#3B8AFF"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                        <img src="{{ asset('admin-assets/images/auth/01.png') }}" class="img-fluid gradient-main animated-scaleX" alt="Sign in illustration">
                    </div>
                </div>
            </section>
        </div>

        <script src="{{ asset('admin-assets/js/libs.min.js') }}"></script>
        <script src="{{ asset('admin-assets/js/hope-ui.js') }}"></script>
        <script>
            document.getElementById('password-toggle').addEventListener('click', function () {
                var input = document.getElementById('password');
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                this.querySelector('.eye-open').classList.toggle('d-none', isHidden);
                this.querySelector('.eye-closed').classList.toggle('d-none', !isHidden);
                this.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        </script>
    </body>
</html>
