<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use LdapRecord\LdapRecordException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', ['ldapEnabled' => (bool) config('ldap.enabled')]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // LDAP_ENABLED=true authenticates against the directory; otherwise
        // the same form checks the local users table (the bypass).
        $authenticated = config('ldap.enabled')
            ? $this->attemptLdap($credentials)
            : $this->attemptLocal($credentials);

        if (! $authenticated) {
            activity('auth')
                ->withProperties(['username' => $credentials['username'], 'ip' => $request->ip()])
                ->log('failed login');

            return back()
                ->withErrors(['username' => 'The credentials provided are not correct'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        activity('auth')
            ->causedBy(Auth::user())
            ->withProperties(['via' => config('ldap.enabled') ? 'ldap' : 'local', 'ip' => $request->ip()])
            ->log('logged in');

        if (Auth::user()->roles()->doesntExist()) {
            return redirect()->route('access.pending');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        activity('auth')
            ->causedBy($request->user())
            ->withProperties(['ip' => $request->ip()])
            ->log('logged out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function accessPending()
    {
        if (Auth::user()->roles()->exists()) {
            return redirect()->route('dashboard');
        }

        return view('auth.access-pending');
    }

    private function attemptLdap(array $credentials): bool
    {
        try {
            $bound = Auth::guard('ldap')->attempt([
                'samaccountname' => $credentials['username'],
                'password'       => $credentials['password'],
            ]);
        } catch (LdapRecordException $e) {
            report($e);

            throw ValidationException::withMessages([
                'username' => 'The directory server could not be reached. Please try again later.',
            ]);
        }

        if (! $bound) {
            return false;
        }

        // Re-login the synced user on the web guard, which is the guard
        // the rest of the app and the role/permission checks run on.
        Auth::guard('web')->login(Auth::guard('ldap')->user());

        return true;
    }

    private function attemptLocal(array $credentials): bool
    {
        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return Auth::guard('web')->attempt([
            $field     => $credentials['username'],
            'password' => $credentials['password'],
        ]);
    }
}
