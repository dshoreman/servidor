<?php

namespace Servidor\Http\Requests\System;

class CreateUser extends SaveUser
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'gid' => 'integer|required_unless:user_group,1',
            'create_home' => 'boolean',
            'user_group' => 'boolean',
        ]);
    }
}
