<?php

namespace Servidor\Http\Requests\System;

class UpdateUser extends SaveUser
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
            'move_home' => 'boolean|nullable',
        ]);
    }
}
