<?php

namespace Servidor\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class EditFile extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => 'required',
            'contents' => 'present',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File path must be specified.',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @suppress PhanUnextractableAnnotationSuffix
     * @suppress PhanUnextractableAnnotationElementName
     * @suppress PhanUnusedPublicMethodParameter
     *
     * @param array<array-key, mixed>|int|string|null $key
     * @param mixed                                   $default
     *
     * @return array{file: string, contents: string}
     */
    public function validated($key = null, $default = null): array
    {
        $data = (array) parent::validated();

        return [
            'file' => (string) $data['file'],
            'contents' => (string) $data['contents'],
        ];
    }
}
