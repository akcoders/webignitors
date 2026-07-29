<x-mail::message>
# New project inquiry

**{{ $inquiry->name }}**{{ $inquiry->company ? " from {$inquiry->company}" : '' }} just sent a project brief.

<x-mail::panel>
Service: {{ str($inquiry->service)->replace('-', ' ')->title() }}<br>
Budget: {{ str($inquiry->budget)->replace('-', ' ')->title() }}<br>
Email: {{ $inquiry->email }}<br>
Phone: {{ $inquiry->phone ?: 'Not provided' }}
</x-mail::panel>

{{ $inquiry->message }}

Reply directly to this email to continue the conversation.

— {{ config('app.name') }} website
</x-mail::message>
