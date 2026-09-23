<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DailyReportLimitExceeded;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WebsiteAudit\WebsiteReportCreator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

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

        // Authentication and report creation must not depend on SMTP being
        // available. A mail transport failure previously left the user in the
        // database but returned a 500 before the pending audit was created.
        Auth::login($user);
        $request->session()->regenerate();

        $report = null;
        $reportLimitMessage = null;
        if ($url = $request->session()->pull('pending_audit_url')) {
            try {
                $report = $creator->create($user, $url);
            } catch (DailyReportLimitExceeded $exception) {
                $reportLimitMessage = $exception->getMessage();
            }
        }

        $verificationSent = $this->sendRegistrationNotification($user);

        if ($report) {
            $response = to_route('reports.show', $report)
                ->with('success', 'Your account is ready and the website audit has started.');

            return $verificationSent
                ? $response
                : $response->with('status', 'The audit is running, but the verification email could not be sent. Check the production mail settings, then resend it from your account.');
        }

        $response = to_route('dashboard')->with('success', 'Your WebIgnitors account is ready.');

        if ($reportLimitMessage) {
            $response->withErrors(['url' => $reportLimitMessage]);
        }

        return $verificationSent
            ? $response
            : $response->with('status', 'Your account is ready, but the verification email could not be sent. Check the production mail settings, then resend it from your account.');
    }

    private function sendRegistrationNotification(User $user): bool
    {
        try {
            event(new Registered($user));
            Log::info('Registration verification email submitted to the user.', [
                'user_id' => $user->id,
                'recipient' => $user->email,
            ]);

            return true;
        } catch (Throwable $exception) {
            Log::error('Registration succeeded but the verification notification failed.', [
                'user_id' => $user->id,
                'recipient' => $user->email,
                'exception' => $exception,
            ]);

            return false;
        }
    }
}
