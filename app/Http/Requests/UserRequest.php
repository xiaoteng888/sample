<?php

namespace App\Http\Requests;


class UserRequest extends Request
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
                  'name' => 'required|max:50',
                  'email' => 'required|email|unique:users|max:255',
                  'password' => 'required|confirmed|min:6'
        ];
    }
}
