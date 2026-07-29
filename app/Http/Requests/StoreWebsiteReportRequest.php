<?php

namespace App\Http\Requests;

use App\Services\WebsiteAudit\SafeWebsiteUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreWebsiteReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $url = trim((string) $this->input('url'));
        if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        $this->merge(['url' => $url]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:2048', 'url:http,https'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('url')) {
                    return;
                }

                try {
                    $safeUrl = app(SafeWebsiteUrl::class);
                    $url = $safeUrl->normalize((string) $this->input('url'));
                    $safeUrl->assertPublic($url);
                } catch (\Throwable $exception) {
                    $validator->errors()->add('url', $exception->getMessage());
                }
            },
        ];
    }
}
