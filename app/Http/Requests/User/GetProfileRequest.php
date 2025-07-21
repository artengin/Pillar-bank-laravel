<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class GetProfileRequest extends Request
{
    public function rules(): array
    {
        $availableRelations = implode(',', $this->getAvailableRelations());

        return [
            'with' => 'array',
            'with.*' => "required|string|in:{$availableRelations}",
        ];
    }

    public function messages(): array
    {
        return [
            'with.*.in' => __('validation.exceptions.invalid_relation', ['input' => ':input']),
        ];
    }

    protected function getAvailableRelations(): array
    {
        return [
            'cards',
        ];
    }
}
