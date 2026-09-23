<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Mail\InquiryReceivedConfirmation;
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
            Mail::to(config('mail.inquiries.address'))->send(new NewInquiryNotification($inquiry));
            Log::info('Inquiry notification submitted to the company mailbox.', [
                'inquiry_id' => $inquiry->id,
                'recipient' => config('mail.inquiries.address'),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Inquiry notification to the company mailbox failed.', [
                'inquiry_id' => $inquiry->id,
                'recipient' => config('mail.inquiries.address'),
                'error' => $exception->getMessage(),
            ]);
        }

        try {
            Mail::to($inquiry->email)->send(new InquiryReceivedConfirmation($inquiry));
            Log::info('Inquiry confirmation submitted to the customer.', [
                'inquiry_id' => $inquiry->id,
                'recipient' => $inquiry->email,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Inquiry confirmation to the customer failed.', [
                'inquiry_id' => $inquiry->id,
                'recipient' => $inquiry->email,
                'error' => $exception->getMessage(),
            ]);
        }

        return to_route('contact')->with(
            'success',
            "Thanks, {$inquiry->name}! Your brief is in. We'll reply within one business day."
        );
    }
}
