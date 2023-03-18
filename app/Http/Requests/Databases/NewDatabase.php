<?php

namespace Servidor\Http\Requests\Databases;

use Illuminate\Foundation\Http\FormRequest;

class NewDatabase extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'database' => 'required|string',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @suppress PhanUnextractableAnnotationElementName
     * @suppress PhanUnextractableAnnotationSuffix
     * @suppress PhanUnusedPublicMethodParameter
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param array<array-key, mixed>|int|string|null $key
     * @param mixed                                   $default
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
