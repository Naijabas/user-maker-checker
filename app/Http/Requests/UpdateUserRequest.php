<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Let's get the route param by name to get the User object value
        $user = request()->route('user');

        return [
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
            'email' => 'sometimes|email|unique:users,email,'.$user,
//            'user_id' => 'required',
        ];
    }
}
