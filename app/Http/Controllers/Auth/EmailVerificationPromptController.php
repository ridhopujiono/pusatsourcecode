<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended($this->redirectPath($request))
                    : view('public.auth.verify-email');
    }

    private function redirectPath(Request $request): string
    {
        return $request->user()?->is_admin
            ? route('dashboard', absolute: false)
            : route('home', absolute: false);
    }
}
