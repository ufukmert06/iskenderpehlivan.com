<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'service' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('contact.form_name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('contact.form_name'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('contact.form_email')]),
            'email.email' => __('validation.email', ['attribute' => __('contact.form_email')]),
            'email.max' => __('validation.max.string', ['attribute' => __('contact.form_email'), 'max' => 255]),
            'message.required' => __('validation.required', ['attribute' => __('contact.form_message')]),
            'message.max' => __('validation.max.string', ['attribute' => __('contact.form_message'), 'max' => 5000]),
        ];
    }
}
