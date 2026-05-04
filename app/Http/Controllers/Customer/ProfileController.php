<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile page with addresses.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load('addresses');

        return view('customer.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the customer's profile.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update the customer's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // If email changed, reset email verification
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $user->fill($validated)->save();

        return redirect()
            ->route('customer.profile.index')
            ->with('toast_success', 'Profil berhasil diperbarui.');
    }
}
