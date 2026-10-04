<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <header class="site-header">
            <a href="{{ url('/') }}" class="brand">{{ config('app.name', 'Laravel') }}</a>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-signin">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-signin">Sign in</a>
            @endauth
        </header>

        <main>
            <div class="description">
                <h1>Welcome to {{ config('app.name', 'Laravel') }}</h1>
                <p>
                    This is a simple starting point for your site. Built with plain HTML, CSS and
                    jQuery, it's a clean foundation you can shape into whatever you're building next.
                </p>
            </div>
        </main>

        <footer class="site-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </footer>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
