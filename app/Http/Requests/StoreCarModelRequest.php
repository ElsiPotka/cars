<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarModelRequest extends FormRequest
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
            'brand_id' => ['required', 'uuid', 'exists:brands,id'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The model name is required.',
            'name.max' => 'The model name cannot exceed 255 characters.',
            'brand_id.required' => 'Please select a brand.',
            'brand_id.uuid' => 'The selected brand is invalid.',
            'brand_id.exists' => 'The selected brand does not exist.',
            'year.required' => 'The model year is required.',
            'year.integer' => 'The year must be a valid number.',
            'year.min' => 'The year must be 1900 or later.',
            'year.max' => 'The year cannot be more than next year.',
        ];
    }
}
