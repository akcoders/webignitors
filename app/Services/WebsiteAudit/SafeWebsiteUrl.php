<?php

namespace App\Services\WebsiteAudit;

use InvalidArgumentException;

class SafeWebsiteUrl
{
    public function normalize(string $url): string
    {
        $url = trim($url);

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['host'])) {
            throw new InvalidArgumentException('Enter a complete website URL.');
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Only HTTP and HTTPS websites can be audited.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('Website URLs containing credentials are not allowed.');
        }

        $port = $parts['port'] ?? null;
        if ($port !== null && ! in_array((int) $port, [80, 443], true)) {
            throw new InvalidArgumentException('Only standard web ports can be audited.');
        }

        $host = strtolower(rtrim($parts['host'], '.'));
        $path = $parts['path'] ?? '/';
        $path = $path === '' ? '/' : $path;
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $portPart = $port !== null ? ':'.$port : '';

        return "{$scheme}://{$host}{$portPart}{$path}{$query}";
    }

    public function assertPublic(string $url): void
    {
        $url = $this->normalize($url);
        $host = (string) parse_url($url, PHP_URL_HOST);

        if ($host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) {
            throw new InvalidArgumentException('Local and private websites cannot be audited.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )) {
                throw new InvalidArgumentException('Private or reserved network addresses cannot be audited.');
            }

            return;
        }

        if (! config('audit.resolve_dns', true)) {
            return;
        }

        $addresses = $this->resolveAddresses($host);

        if ($addresses === []) {
            throw new InvalidArgumentException('The website domain could not be resolved.');
        }

        foreach ($addresses as $address) {
            if (! filter_var(
                $address,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )) {
                throw new InvalidArgumentException('Private or reserved network addresses cannot be audited.');
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function resolveAddresses(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (! is_array($records)) {
            return [];
        }

        $addresses = [];
        foreach ($records as $record) {
            if (isset($record['ip'])) {
                $addresses[] = $record['ip'];
            }
            if (isset($record['ipv6'])) {
                $addresses[] = $record['ipv6'];
            }
        }

        return array_values(array_unique($addresses));
    }
}
