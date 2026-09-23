<x-mail::message>
# Your project brief is safely with us

Hello {{ $inquiry->name }},

Thanks for contacting WebIgnitors. We received your brief for **{{ str($inquiry->service)->replace('-', ' ')->title() }}** and will review the details before replying.

<x-mail::panel>
Reference: WI-{{ str_pad((string) $inquiry->id, 6, '0', STR_PAD_LEFT) }}  
Expected response: Within one business day
</x-mail::panel>

You can reply directly to this email if there is anything else we should know.

<x-mail::button :url="route('services')">
Explore our capabilities
</x-mail::button>

Thanks,  
Anuj Shukla, Nilesh Dubey and the WebIgnitors crew
</x-mail::message>
