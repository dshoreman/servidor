<?php

namespace Servidor\Http\Requests\Databases;

use Illuminate\Foundation\Http\FormRequest;

class NewDatabase extends FormRequest
{
    public function rules(): array
    {
        return [
            'database' => 'required|string',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @suppress PhanUnusedPublicMethodParameter
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param array|int|string|null $key
     * @param mixed                 $default
     *
     * @return array{database: string}
     */
    public function validated($key = null, $default = null): array
    {
        /** @var array{database: string} $data */
        $data = parent::validated();

        return array_merge($data, ['database' => $data['database']]);
    }
}
