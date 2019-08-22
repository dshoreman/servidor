<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Servidor\Rules\Domain;

class UpdateSite extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('sites', 'name')->ignore($this->route('site')),
            ],
            'primary_domain' => ['required', new Domain],
            'type' => 'required|in:basic,php,laravel,redirect',
            'source_repo' => 'required_unless:type,redirect|nullable|url',
            'source_branch' => 'nullable|string',
            'document_root' => 'required_unless:type,redirect|nullable|string',
            'redirect_type' => 'required_if:type,redirect|nullable|integer',
            'redirect_to' => 'required_if:type,redirect|nullable|string',
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            if (!isset($data['source_branch'], $data['source_repo'])) {
                return;
            }

            exec('git ls-remote --heads --exit-code "'.$data['source_repo'].'" "'.$data['source_branch'].'"', $o, $status);

            if (128 === $status) {
                $validator->errors()->add('source_repo', "This repo couldn't be found. Does it require auth?");
            } elseif (2 === $status) {
                $validator->errors()->add('source_branch', "This branch doesn't exist.");
            } elseif (0 !== $status) {
                $validator->errors()->add('source_repo', 'Branch listing failed. Is this repo valid?');
            }
        });
    }
}
