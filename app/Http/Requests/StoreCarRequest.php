<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'car_model_id' => ['required', 'exists:car_models,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'mileage' => ['required', 'integer', 'min:0'],
            'transmission' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\CarTransmission::class)],
            'fuel_type' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\CarFuelType::class)],
            'engine_size' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\CarStatus::class)],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:car_features,id'],
            'photos' => ['nullable', 'array', 'max:16'],
            'photos.*' => [
                'file',
                'mimes:jpeg,png,jpg,gif,svg,webp,heic,heif',
                'max:10240',
            ],
        ];
    }
}
