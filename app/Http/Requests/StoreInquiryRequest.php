<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:120'],
            'service' => ['required', 'in:web-development,ecommerce,erp,crm,hrm,complex-web-application,business-automation,mobile-apps,digital-marketing,ai-integration,product-strategy,other'],
            'budget' => ['required', 'in:under-5k,5k-15k,15k-40k,40k-plus,not-sure'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'service.required' => 'Choose the service you are interested in.',
            'budget.required' => 'Choose an approximate project budget.',
            'message.min' => 'Give us a little more detail—at least 20 characters.',
            'website.max' => 'We could not verify this submission. Please try again.',
        ];
    }
}
