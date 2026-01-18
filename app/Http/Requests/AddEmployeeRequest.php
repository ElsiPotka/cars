<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddEmployeeRequest extends FormRequest
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
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'role_id' => [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $userId = $this->input('user_id');
                    if ($userId) {
                        $user = \App\Models\User::find($userId);
                        $role = \App\Models\Role::find($value);
                        if ($user && $role && !$user->hasRole($role->name)) {
                           $fail('The selected user does not have this role.');
                        }
                    }
                },
            ],
        ];
    }
}
