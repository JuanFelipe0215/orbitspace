<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Tenant;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'blog_slug' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:tenants,id'],
        ]);

        // Crear tenant y subdominio primero (en BD central)
        $tenant = Tenant::create(['id' => $request->blog_slug]);
        $tenant->domains()->create([
            'domain' => $request->blog_slug . '.' . config('app.central_domain'),
        ]);

        // Cambiar al contexto del tenant para crear el usuario en su BD
        tenancy()->initialize($tenant);
        Auth::forgetGuards();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Redirigir al subdominio del tenant
        $domain = $tenant->domains->first()->domain;
        $port = $request->getPort();
        $scheme = $request->getScheme();
        $suffix = ($port == 80 || $port == 443) ? '' : ":$port";

        return redirect()->away("{$scheme}://{$domain}{$suffix}");
    }
}
