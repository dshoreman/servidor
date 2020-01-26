<?php

namespace Servidor\Http\Requests\System;

class UpdateUser extends SaveUser
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
        ]);
    }
}
