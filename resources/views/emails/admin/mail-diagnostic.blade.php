<x-mail::message>
# Email delivery is connected

Hello {{ $administrator->name }},

This test message confirms that the WebIgnitors production application connected to the configured SMTP server and submitted an email successfully.

**Application:** {{ config('app.name') }}  
**Website:** {{ config('app.url') }}  
**Tested:** {{ now()->format('d M Y, h:i A T') }}

If this message arrived in spam, mark it as safe and verify the SPF, DKIM and DMARC records for the sending domain.

Thanks,  
WebIgnitors System Monitor
</x-mail::message>
