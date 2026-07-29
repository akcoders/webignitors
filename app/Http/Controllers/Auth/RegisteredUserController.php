<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WebsiteAudit\WebsiteReportCreator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, WebsiteReportCreator $creator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        if ($url = $request->session()->pull('pending_audit_url')) {
            $report = $creator->create($user, $url);

            return to_route('reports.show', $report)
                ->with('success', 'Your account is ready and the website audit has started.');
        }

        return to_route('dashboard')->with('success', 'Your WebIgnitors account is ready.');
    }
}
