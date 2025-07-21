<?php

namespace App\Http\Requests;

use RonasIT\Support\Http\BaseRequest;

class Request extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
