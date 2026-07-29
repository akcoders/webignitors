<?php

namespace App\Notifications;

use App\Models\WebsiteReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebsiteReportReady extends Notification
{
    use Queueable;

    public function __construct(public WebsiteReport $report) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your {$this->report->domain} website report is ready")
            ->greeting("Your Website Intelligence Report is ready, {$notifiable->name}.")
            ->line("We completed the design, performance, code, accessibility, SEO, marketing and security review for {$this->report->domain}.")
            ->line('Overall score: '.data_get($this->report->scores, 'overall', '—').'/100')
            ->action('View your report', route('reports.show', $this->report))
            ->line('Sign in to your private WebIgnitors dashboard to review the findings and download the branded PDF.');
    }
}
