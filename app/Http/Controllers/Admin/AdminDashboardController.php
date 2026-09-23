<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminMailDiagnostic;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\WebsiteReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'reports' => WebsiteReport::query()->count(),
                'completed' => WebsiteReport::query()->where('status', 'completed')->count(),
                'inquiries' => Inquiry::query()->count(),
            ],
            'queue' => [
                'waiting' => DB::table('jobs')->count(),
                'failed' => DB::table('failed_jobs')->count(),
                'stalled' => WebsiteReport::query()->where('status', 'queued')->count(),
            ],
            'reports' => WebsiteReport::query()->with('user')->latest()->limit(12)->get(),
            'inquiries' => Inquiry::query()->latest()->limit(8)->get(),
            'users' => User::query()->withCount('websiteReports')->latest()->limit(8)->get(),
            'mailConfig' => [
                'mailer' => config('mail.default'),
                'scheme' => config('mail.mailers.smtp.scheme') ?: 'automatic',
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'password_set' => filled(config('mail.mailers.smtp.password')),
                'from' => config('mail.from.address'),
            ],
        ]);
    }

    public function mailTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
        ]);
        $startedAt = microtime(true);

        try {
            Mail::to($validated['email'])->send(new AdminMailDiagnostic($request->user()));
            $duration = (int) ((microtime(true) - $startedAt) * 1000);

            Log::info('Administrator SMTP diagnostic succeeded.', [
                'user_id' => $request->user()->id,
                'recipient' => $validated['email'],
                'duration_ms' => $duration,
            ]);

            return back()->with('mail_diagnostic', [
                'success' => true,
                'message' => "SMTP accepted the test email in {$duration} ms. Check the inbox and spam folder.",
            ]);
        } catch (Throwable $exception) {
            Log::error('Administrator SMTP diagnostic failed.', [
                'user_id' => $request->user()->id,
                'recipient' => $validated['email'],
                'exception' => $exception,
            ]);

            return back()->with('mail_diagnostic', [
                'success' => false,
                'message' => $this->mailFailureHint($exception->getMessage()),
                'detail' => Str::limit($exception->getMessage(), 400),
            ]);
        }
    }

    private function mailFailureHint(string $message): string
    {
        $message = Str::lower($message);

        return match (true) {
            str_contains($message, 'scheme') && str_contains($message, 'support') => 'The SMTP scheme is invalid. Use smtp with port 587, or smtps with port 465.',
            str_contains($message, 'authenticate'), str_contains($message, '535') => 'The SMTP server rejected the login. Confirm the complete mailbox address and mailbox password.',
            str_contains($message, 'timed out'), str_contains($message, 'connection refused'), str_contains($message, 'getaddrinfo') => 'The application could not reach the SMTP server. Confirm the host, port and hosting outbound-mail access.',
            str_contains($message, 'certificate'), str_contains($message, 'crypto'), str_contains($message, 'tls') => 'The secure SMTP connection failed. Use smtp on port 587 or smtps on port 465.',
            str_contains($message, 'sender'), str_contains($message, '550'), str_contains($message, '553') => 'The SMTP server rejected the sender. MAIL_FROM_ADDRESS should normally match the authenticated mailbox.',
            default => 'The SMTP test failed. Review the technical detail below and the Laravel production log.',
        };
    }
}
