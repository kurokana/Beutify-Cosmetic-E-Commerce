<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

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
     * Creates a new customer account and dispatches the email verification job
     * asynchronously (Requirement 1.1).
     * Duplicate email returns "Email sudah terdaftar" (Requirement 1.3).
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'role'      => 'customer',
        ]);

        // Dispatch the Registered event for any other listeners
        event(new Registered($user));

        // Dispatch email verification job asynchronously (Requirement 1.1)
        SendEmailVerificationJob::dispatch($user);

        Auth::login($user);

        // Redirect to email verification notice so the user knows to check
        // their inbox before accessing the dashboard (Requirement 1.2).
        return redirect()->route('verification.notice');
    }
}
