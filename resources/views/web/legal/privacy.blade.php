@extends('layouts.web')

@section('content')
    @php
        $siteName = \App\Models\Setting::get('site_name', config('app.name'));
        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactPhone = \App\Models\Setting::get('contact_phone');
        $siteAddress = \App\Models\Setting::get('site_address');
    @endphp

    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>Privacy Policy</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li> / Privacy Policy</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 legal-content">
                    <p><em>Last updated: October 2026</em></p>

                    <p>{{ $siteName }} ("we", "us") runs this website. This page explains what personal information we collect when you use the site, why we collect it, and what choices you have.</p>

                    <h2 class="h3">1. Information you give us</h2>
                    <p>When you use the contact form, we collect your name, email address, phone number (optional), subject and message. We use it only to reply to your enquiry. Enquiries are stored so our team can follow them up.</p>

                    <h2 class="h3">2. Information collected automatically</h2>
                    <p>When you visit a public page, we record the page address, the time of the visit, your IP address, your browser's user agent (the name and version of your browser and device) and the page that referred you. We use this to understand which pages are useful. Visits by signed-in staff and by search engine or link preview bots are not recorded.</p>
                    <p>To show approximate visitor locations to our staff, we look up the city and country for an IP address using the ipapi.co service. Only the IP address is sent to that service.</p>

                    <h2 class="h3">3. Cookies and local storage</h2>
                    <p>We use cookies that keep the site working, such as your session and your chosen language. We also use Google Analytics to measure site traffic, which sets its own cookies. Pop-up notices may be remembered in your browser's local storage so they are not shown again.</p>

                    <h2 class="h3">4. Third-party services</h2>
                    <p>The site uses Google Maps to show our location, Google Analytics for measurement, and a Facebook page feed where enabled. These services have their own privacy policies, and their use of data is governed by those policies.</p>

                    <h2 class="h3">5. How we use your information</h2>
                    <p>We use your information to answer enquiries, improve the site, and keep it secure. We do not sell your personal information.</p>

                    <h2 class="h3">6. How long we keep it</h2>
                    <p>Enquiries are kept for as long as they are needed to follow them up, and then deleted. Visit records are kept for analysis and are reviewed periodically.</p>

                    <h2 class="h3">7. Your choices</h2>
                    <p>You can ask us to see, correct or delete any personal information we hold about you. You can also clear cookies and local storage in your browser at any time.</p>

                    <h2 class="h3">8. Children</h2>
                    <p>Our courses are for learners of all ages, but this site is not designed to collect personal information from children under 13 without a parent or guardian.</p>

                    <h2 class="h3">9. Changes to this policy</h2>
                    <p>We may update this policy from time to time. The "last updated" date at the top shows when it last changed.</p>

                    <h2 class="h3">10. Contact us</h2>
                    <p>
                        {{ $siteName }}
                        @if ($siteAddress)<br>{{ $siteAddress }}@endif
                        @if ($contactEmail)<br>Email: <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif
                        @if ($contactPhone)<br>Phone: {{ $contactPhone }}@endif
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
