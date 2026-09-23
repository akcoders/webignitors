@props(['action', 'theme' => 'light'])

@if (config('services.turnstile.enabled'))
    <div {{ $attributes->merge(['class' => 'turnstile-field']) }}>
        @if (config('services.turnstile.site_key'))
            <div
                class="cf-turnstile"
                data-sitekey="{{ config('services.turnstile.site_key') }}"
                data-action="{{ $action }}"
                data-theme="{{ $theme }}"
                data-size="flexible"
                data-retry="auto"
                data-refresh-expired="auto"
            ></div>
        @else
            <div class="turnstile-unavailable">Security verification is not configured.</div>
        @endif
        @error('turnstile')<div class="turnstile-error">{{ $message }}</div>@enderror
    </div>

    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
@endif
