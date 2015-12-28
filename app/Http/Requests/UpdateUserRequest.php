<?php

namespace App\Http\Requests;

class UpdateUserRequest extends Request
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
    public function rules()
    {
        return [
            'data.name'     => 'alpha_dash|max:255|unique:users,name,'.$this->route('users'),
            'data.email'    => 'email|max:255|unique:users,email,'.$this->route('users'),
            'data.password' => 'string|max:60',
        ];
    }
}
