<?php

namespace Servidor\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\NoColon;
use Servidor\Rules\NoComma;
use Servidor\Rules\NoWhitespace;

class SaveGroup extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'max:32', 'bail', new NoComma(),
                new NoColon(), new NoWhitespace(),
                'regex:/^[a-z_][a-z0-9_-]*[\$]?$/',
            ],
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
            'users' => 'array|nullable',
        ];
    }
}
