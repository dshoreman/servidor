<?php

namespace Servidor\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\NoColon;
use Servidor\Rules\NoComma;
use Servidor\Rules\NoWhitespace;

class SaveUser extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'max:32', 'bail', new NoComma(),
                new NoColon(), new NoWhitespace(),
                'regex:/^[a-z_][a-z0-9_-]*[\$]?$/',
            ],
            'dir' => 'string|nullable',
            'shell' => 'string|nullable',
            'uid' => 'integer|nullable',
        ];
    }
}
