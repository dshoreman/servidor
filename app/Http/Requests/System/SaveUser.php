<?php

namespace Servidor\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Servidor\Rules\ValidLinuxUser;

class SaveUser extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'max:32', 'bail', new ValidLinuxUser(),
                'regex:/^[a-z_][a-z0-9_-]*[\$]?$/',
            ],
            'uid' => 'integer|nullable',
            'gid' => 'integer|required',
            'groups' => 'array|nullable',
        ];
    }
}
