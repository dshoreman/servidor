<?php

namespace Servidor\Http\Requests\System;

class UpdateUser extends SaveUser
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'dir' => 'string|nullable',
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
            'move_home' => 'boolean|nullable',
        ]);
    }
}
