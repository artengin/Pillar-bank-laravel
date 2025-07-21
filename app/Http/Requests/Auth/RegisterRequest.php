<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'ssn' => 'required|string|regex:/^\d{3}-\d{2}-\d{4}$/',
            'password' => 'required|string',
        ];
    }
}
