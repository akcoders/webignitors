<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:180'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:190'],
            'excerpt' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'content' => [$required, 'string'],
            'category' => ['sometimes', 'nullable', 'string', 'max:80'],
            'tags' => ['sometimes', 'nullable'],
            'featured_image_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'featured_image_alt' => ['sometimes', 'nullable', 'string', 'max:180'],
            'status' => ['sometimes', Rule::in(['draft', 'published'])],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:70'],
            'meta_description' => ['sometimes', 'nullable', 'string', 'max:180'],
            'focus_keyword' => ['sometimes', 'nullable', 'string', 'max:120'],
            'secondary_keywords' => ['sometimes', 'nullable'],
            'canonical_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'og_title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'og_description' => ['sometimes', 'nullable', 'string', 'max:220'],
            'og_image_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'robots_index' => ['sometimes', 'boolean'],
            'robots_follow' => ['sometimes', 'boolean'],
            'schema_type' => ['sometimes', Rule::in(['BlogPosting', 'Article', 'TechArticle'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->is('admin/*')) {
            $this->merge([
                'robots_index' => $this->boolean('robots_index'),
                'robots_follow' => $this->boolean('robots_follow'),
            ]);
        }
    }
}
