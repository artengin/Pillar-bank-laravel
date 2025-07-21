<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;

class IssueRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'filled|string',
        ];
    }
}
