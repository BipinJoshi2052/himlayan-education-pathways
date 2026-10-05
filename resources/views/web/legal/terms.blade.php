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
                    <h1>Terms of Use</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li> / Terms of Use</li>
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

                    <p>These terms apply to your use of the {{ $siteName }} website. By using the site, you agree to them. If you do not agree, please do not use the site.</p>

                    <h3>1. Use of the site</h3>
                    <p>You may browse the site, read its content and contact us through its forms. Please do not misuse the site, attempt to disrupt it, or use it for anything unlawful.</p>

                    <h3>2. Information on courses and fees</h3>
                    <p>Course descriptions, schedules, durations and fees are provided for information. They may change, and the fees and terms agreed when you enrol are the ones that apply. Please contact us to confirm current details before you pay.</p>

                    <h3>3. Enquiries and enrolment</h3>
                    <p>Submitting an enquiry does not by itself enrol you in a course or create any payment obligation. Enrolment is confirmed only when we confirm it to you directly.</p>

                    <h3>4. Content and intellectual property</h3>
                    <p>The text, images, logos and other content on this site belong to {{ $siteName }} or are used with permission. You may view and share pages for personal use. Please do not copy or republish our content for commercial purposes without written permission.</p>

                    <h3>5. Links to other sites</h3>
                    <p>The site links to third-party websites, including Google Maps and Facebook. We are not responsible for their content or practices.</p>

                    <h3>6. Accuracy and availability</h3>
                    <p>We work to keep the information on the site accurate and up to date, but we do not guarantee that it is complete or error-free. The site may be unavailable from time to time for maintenance or other reasons.</p>

                    <h3>7. Limitation of liability</h3>
                    <p>To the extent permitted by law, {{ $siteName }} is not liable for any loss or damage arising from your use of the site or reliance on its content.</p>

                    <h3>8. Privacy</h3>
                    <p>How we handle personal information is described in our <a href="{{ route('web.privacy') }}">Privacy Policy</a>.</p>

                    <h3>9. Changes to these terms</h3>
                    <p>We may update these terms. The "last updated" date at the top shows when they last changed. Continued use of the site after a change means you accept the updated terms.</p>

                    <h3>10. Governing law</h3>
                    <p>These terms are governed by the laws of Nepal.</p>

                    <h3>11. Contact us</h3>
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
