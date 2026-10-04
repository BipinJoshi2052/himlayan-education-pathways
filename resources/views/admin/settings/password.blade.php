<style>
    .password-eye-btn,
    .password-eye-btn:focus {
        color: #6c757d;
        background: #ffffff;
        border-color: #ced4da;
    }
    .password-eye-btn:hover,
    .password-eye-btn:active {
        color: #212529;
        background: #e9ecef;
        border-color: #ced4da;
    }
</style>
<div class="card">
    <div class="card-header"><h5 class="mb-0">Change Password</h5></div>
    <div class="card-body">
        <p class="text-muted small">Changes the password for your own account ({{ auth()->user()->email }}).</p>

        <form method="POST" action="{{ route('admin.settings.password.update') }}" style="max-width: 420px;">
            @csrf

            @foreach ([
                'current_password' => ['label' => 'Old password', 'autocomplete' => 'current-password'],
                'password' => ['label' => 'New password', 'autocomplete' => 'new-password'],
                'password_confirmation' => ['label' => 'Confirm new password', 'autocomplete' => 'new-password'],
            ] as $name => $field)
                <div class="form-group">
                    <label class="form-label" for="{{ $name }}">{{ $field['label'] }}</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="{{ $name }}" name="{{ $name }}"
                               autocomplete="{{ $field['autocomplete'] }}" required>
                        <button type="button" class="btn btn-outline-secondary password-eye-btn" data-toggle-password="{{ $name }}"
                                aria-label="Show password" title="Show password">
                            <svg class="eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-7-11-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach

            @error('current_password') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
            @error('password') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

            <small class="text-muted d-block mb-3">At least 8 characters. Must differ from your old password.</small>

            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.togglePassword);
            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.querySelector('.eye-show').style.display = showing ? '' : 'none';
            btn.querySelector('.eye-hide').style.display = showing ? 'none' : '';
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            btn.title = btn.getAttribute('aria-label');
        });
    });
</script>
