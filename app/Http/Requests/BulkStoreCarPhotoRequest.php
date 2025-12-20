<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreCarPhotoRequest extends FormRequest
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
            'car_id' => ['required', 'exists:cars,id'],
            'photos' => ['required', 'array'],
            'photos.*' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif,svg,webp,heic,heif',
                'max:10240',
            ],
        ];
    }
}
