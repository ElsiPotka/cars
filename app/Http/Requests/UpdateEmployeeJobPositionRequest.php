<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeJobPositionRequest extends FormRequest
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
            'role_id' => [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    // Try to get the user from the route parameter 'employee'
                    // The route is companies/{company}/employees/{employee}
                    // where {employee} is bound to a User model.
                    $user = $this->route('employee'); 
                    
                    if ($user) {
                        $role = \App\Models\Role::find($value);
                        if ($role && !$user->hasRole($role->name)) {
                           $fail('The employee does not have this role.');
                        }
                    }
                },
            ],
        ];
    }
}
