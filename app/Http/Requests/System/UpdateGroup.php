<?php

namespace Servidor\Http\Requests\System;

class UpdateGroup extends SaveGroup
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'users' => 'array|nullable',
        ]);
    }
}
