<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final readonly class AttemptLoginAction
{
    public function handle(Request $request, LoginData $data): bool
    {
        $throttleKey = mb_strtolower($data->email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        $credentials = ['email' => $data->email, 'password' => $data->password];

        if (! Auth::attempt($credentials, $data->remember)) {
            RateLimiter::hit($throttleKey);

            return false;
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $this->canLogIn($user)) {
            Auth::logout();
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('Your account does not currently have login access.'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return true;
    }

    /**
     * Roles > Login Enabled — a role with the toggle off blocks its users
     * from logging in. A user with no roles at all (or only roles that
     * were deleted out from under them) isn't affected by this — there's
     * nothing to disable — so only a user whose EVERY assigned role has
     * the toggle off is blocked.
     */
    private function canLogIn(User $user): bool
    {
        $roles = $user->roles;

        if ($roles->isEmpty()) {
            return true;
        }

        return $roles->contains(fn ($role) => (bool) $role->login_enabled);
    }
}
