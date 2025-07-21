<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
