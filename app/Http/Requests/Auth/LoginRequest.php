<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Implements Requirement 1.4 (valid login → session 7 days) and
     * Requirement 1.5 (5 failed attempts → lock account 15 minutes).
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if the account is locked in the database (Requirement 1.5)
        $user = User::where('email', $this->string('email')->lower()->value())->first();

        if ($user && $user->isLocked()) {
            $minutesLeft = (int) ceil(now()->diffInSeconds($user->locked_until) / 60);

            throw ValidationException::withMessages([
                'email' => "Akun Anda dikunci karena terlalu banyak percobaan login yang gagal. "
                    . "Silakan coba lagi dalam {$minutesLeft} menit.",
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Increment failed_login_attempts and lock if threshold reached (Requirement 1.5)
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;

                if ($attempts >= 5) {
                    $user->update([
                        'failed_login_attempts' => $attempts,
                        'locked_until'          => now()->addMinutes(15),
                    ]);

                    throw ValidationException::withMessages([
                        'email' => 'Akun Anda telah dikunci selama 15 menit karena terlalu banyak '
                            . 'percobaan login yang gagal. Silakan coba lagi nanti.',
                    ]);
                }

                $user->update(['failed_login_attempts' => $attempts]);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Successful login — reset failed attempts and clear rate limiter
        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until'          => null,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited by the RateLimiter facade.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
