<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
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
            'car_model_id' => ['sometimes', 'required', 'exists:car_models,id'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'mileage' => ['sometimes', 'required', 'integer', 'min:0'],
            'transmission' => ['sometimes', 'required', \Illuminate\Validation\Rule::enum(\App\Enums\CarTransmission::class)],
            'fuel_type' => ['sometimes', 'required', \Illuminate\Validation\Rule::enum(\App\Enums\CarFuelType::class)],
            'engine_size' => ['nullable', 'string', 'max:255'],
            'color' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', \Illuminate\Validation\Rule::enum(\App\Enums\CarStatus::class)],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:car_features,id'],

            'deleted_photos' => ['nullable', 'array'],
            'deleted_photos.*' => ['exists:car_photos,id'],

            'photos' => ['nullable', 'array', 'max:16'],
            'photos.*' => [
                'file',
                'mimes:jpeg,png,jpg,gif,svg,webp,heic,heif',
                'max:10240',
            ],
        ];
    }
}
