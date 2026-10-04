@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>Contact Us</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li> / Contact</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="address_area section-padding">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp">
                    <div class="single_address sa_one">
                        <i class="ti-map"></i>
                        <h4>Our Location</h4>
                        <p>
                            <a href="{{ \App\Common\Services\MapLink::pointUrl() ?? ('https://www.google.com/maps/search/?api=1&query=' . urlencode(\App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal'))) }}" target="_blank" rel="noopener">
                                {{ \App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal') }}
                            </a>
                        </p>
                    </div>
                </div>
                @if (\App\Models\Setting::get('contact_phone'))
                    <div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp">
                        <div class="single_address sa_two">
                            <i class="ti-mobile"></i>
                            <h4>Telephone</h4>
                            <p><a href="tel:{{ preg_replace('/[^\d+]/', '', \App\Models\Setting::get('contact_phone')) }}">{{ \App\Models\Setting::get('contact_phone') }}</a></p>
                        </div>
                    </div>
                @endif
                @if (\App\Models\Setting::get('contact_email'))
                    <div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp">
                        <div class="single_address sa_three">
                            <i class="ti-email"></i>
                            <h4>Send Email</h4>
                            <p><a href="mailto:{{ \App\Models\Setting::get('contact_email') }}">{{ \App\Models\Setting::get('contact_email') }}</a></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div id="contact" class="contact_area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-sm-12 col-xs-12 wow fadeInUp">
                    <div class="contact">
                        <div id="contact-alert"></div>
                        <form class="form" id="contact-form">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Your Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Your Subject</label>
                                    <input type="text" name="subject" class="form-control">
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Your Message</label>
                                    <textarea rows="6" name="message" class="form-control" required></textarea>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn_one">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 col-sm-12 col-xs-12 wow fadeInUp">
                    <div class="map map-with-directions">
                        <iframe src="{{ \App\Common\Services\MapLink::embedUrl() ?? ('https://www.google.com/maps?q=' . urlencode(\App\Models\Setting::get('site_address', 'Chabahil, Kathmandu, Nepal')) . '&output=embed') }}" style="border:0; width:100%; height:400px;" allowfullscreen loading="lazy"></iframe>
                        <a href="{{ \App\Common\Services\MapLink::directionsUrl(\App\Models\Setting::get('site_address', 'Chabahil, Kathmandu, Nepal')) }}"
                           class="btn_one map-directions-btn" target="_blank" rel="noopener">
                            Get Directions <i class="ti-arrow-top-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('contact-form').addEventListener('submit', function (event) {
            event.preventDefault();

            var form = event.target;
            var alertBox = document.getElementById('contact-alert');
            var formData = new FormData(form);

            fetch('{{ route('contact.send') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(function (response) { return response.json().then(function (data) { return {status: response.status, data: data}; }); })
                .then(function (result) {
                    if (result.status === 200 && result.data.success) {
                        alertBox.innerHTML = '<div class="alert alert-success">' + result.data.message + '</div>';
                        form.reset();
                    } else if (result.status === 422) {
                        var messages = Object.values(result.data.error.details || {}).flat().join(' ');
                        alertBox.innerHTML = '<div class="alert alert-danger">' + (messages || 'Please check the form and try again.') + '</div>';
                    } else {
                        alertBox.innerHTML = '<div class="alert alert-danger">' + (result.data.message || 'Something went wrong.') + '</div>';
                    }
                })
                .catch(function () {
                    alertBox.innerHTML = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
                });
        });
    </script>
@endpush
