<?php

return [
    'user_agent' => env('AUDIT_USER_AGENT', 'WebIgnitors Website Intelligence/1.0 (+https://webignitors.in)'),
    'page_limit' => (int) env('AUDIT_PAGE_LIMIT', 1),
    'max_html_bytes' => (int) env('AUDIT_MAX_HTML_BYTES', 3_000_000),
    'resolve_dns' => env('AUDIT_RESOLVE_DNS', true),

    'pagespeed' => [
        'enabled' => env('PAGESPEED_ENABLED', true),
        'api_key' => env('GOOGLE_PAGESPEED_API_KEY'),
        'endpoint' => env('PAGESPEED_ENDPOINT', 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'),
        'timeout' => (int) env('PAGESPEED_TIMEOUT', 90),
    ],

    'crux' => [
        'enabled' => env('CRUX_ENABLED', true),
        'api_key' => env('GOOGLE_CRUX_API_KEY', env('GOOGLE_PAGESPEED_API_KEY')),
        'endpoint' => env('CRUX_ENDPOINT', 'https://chromeuxreport.googleapis.com/v1/records:queryRecord'),
    ],

    'w3c' => [
        'enabled' => env('W3C_VALIDATOR_ENABLED', true),
        'endpoint' => env('W3C_VALIDATOR_ENDPOINT', 'https://validator.w3.org/nu/'),
    ],

    'observatory' => [
        'enabled' => env('MDN_OBSERVATORY_ENABLED', true),
        'endpoint' => env('MDN_OBSERVATORY_ENDPOINT', 'https://observatory-api.mdn.mozilla.net/api/v2/scan'),
    ],

    'browserless' => [
        'enabled' => env('BROWSERLESS_ENABLED', false),
        'token' => env('BROWSERLESS_API_TOKEN'),
        'base_url' => rtrim(env('BROWSERLESS_BASE_URL', 'https://production-sfo.browserless.io'), '/'),
    ],
];
