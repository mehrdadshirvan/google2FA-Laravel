<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $auth = User::where('email', $request->only('email'))->first();
        if($auth->google2fa_enabled){

            $google2fa = new Google2FA();
            if ($google2fa->verifyKey(decrypt($auth->google2fa_secret), $request->get('code'))) {
                session(['google2fa_passed' => true]);
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard', absolute: false));
            }
            return $this->destroy($request);
        }
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
