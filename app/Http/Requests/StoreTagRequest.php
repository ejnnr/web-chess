<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreTagRequest extends Request
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
            'data.name' => 'required|string|max:255',
			'data.owner_id' => 'required|integer|min:1|exists:users,id',
			'data.public' => 'required|integer|min:0|max:255'
        ];
    }
}
