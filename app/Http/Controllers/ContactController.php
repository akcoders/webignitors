<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Mail\NewInquiryNotification;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $inquiry = Inquiry::create([
            ...$request->safe()->except('website'),
            'source_ip' => $request->ip(),
        ]);

        try {
            Mail::to(config('mail.to.address'))->send(new NewInquiryNotification($inquiry));
        } catch (\Throwable $exception) {
            Log::warning('Inquiry email delivery failed.', [
                'inquiry_id' => $inquiry->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return to_route('contact')->with(
            'success',
            "Thanks, {$inquiry->name}! Your brief is in. We'll reply within one business day."
        );
    }
}
