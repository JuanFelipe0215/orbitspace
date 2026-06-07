<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $this->ensureTenancyInitialized();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function ensureTenancyInitialized(): void
    {
        if (tenancy()->initialized) {
            return;
        }

        $host = $this->getHost();

        if (in_array($host, config('tenancy.central_domains', []))) {
            return;
        }

        try {
            // InitializeTenancyByDomain should have already run via middleware.
            // This is a safety fallback in case it didn't (e.g. guard cached before middleware ran).
            tenancy()->initialize(
                app(DomainTenantResolver::class)->resolve($host)
            );
            // AuthTenancyBootstrapper calls forgetGuards() automatically via the
            // TenancyInitialized event, so Auth::attempt() below will get a fresh guard
            // pointed at the tenant DB.
        } catch (TenantCouldNotBeIdentifiedException) {
            // Unknown domain — Auth::attempt() will simply fail with auth.failed.
        }
    }

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

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
