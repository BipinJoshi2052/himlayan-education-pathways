@php
    $hasPassword = \App\Models\Setting::get('mail_password') !== null;
@endphp

<div class="card">
    <div class="card-header"><h5 class="mb-0">SMTP / Mail</h5></div>
    <div class="card-body">
        <p class="text-muted small">For Gmail: host <code>smtp.gmail.com</code>, port <code>587</code>, encryption <code>tls</code>, username your full Gmail address, and an <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a> (not your normal Google password — Google requires 2FA + an app-specific password for SMTP).</p>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="smtp">

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">Host</label>
                        <input type="text" class="form-control" name="mail_host" placeholder="smtp.gmail.com"
                               value="{{ old('mail_host', \App\Models\Setting::get('mail_host')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Port</label>
                        <input type="number" class="form-control" name="mail_port" placeholder="587"
                               value="{{ old('mail_port', \App\Models\Setting::get('mail_port')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="mail_username"
                               value="{{ old('mail_username', \App\Models\Setting::get('mail_username')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Password {{ $hasPassword ? '(set)' : '' }}</label>
                        <input type="password" class="form-control" name="mail_password"
                               placeholder="{{ $hasPassword ? 'Leave blank to keep current password' : '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Encryption</label>
                        <select class="form-select" name="mail_encryption">
                            @foreach (['tls' => 'TLS', 'ssl' => 'SSL', '' => 'None'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('mail_encryption', \App\Models\Setting::get('mail_encryption', 'tls')) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">From Address</label>
                        <input type="email" class="form-control" name="mail_from_address"
                               value="{{ old('mail_from_address', \App\Models\Setting::get('mail_from_address')) }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group mb-0">
                        <label class="form-label">From Name</label>
                        <input type="text" class="form-control" name="mail_from_name"
                               value="{{ old('mail_from_name', \App\Models\Setting::get('mail_from_name')) }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save SMTP Settings</button>
        </form>
    </div>
</div>
