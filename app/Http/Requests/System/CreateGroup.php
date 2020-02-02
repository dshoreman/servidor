<?php

namespace Servidor\Http\Requests\System;

class CreateGroup extends SaveGroup
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'system' => 'boolean|nullable',
        ]);
    }
}
