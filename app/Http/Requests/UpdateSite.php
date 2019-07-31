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
            'primary_domain' => [new Domain],
            'type' => 'required|in:basic,php,laravel,redirect',
            'source_repo' => 'required_unless:type,redirect|nullable|url',
            'source_branch' => 'nullable|string',
            'document_root' => 'required_unless:type,redirect|nullable|string',
            'redirect_type' => 'required_if:type,redirect|nullable|integer',
            'redirect_to' => 'required_if:type,redirect|nullable|string',
            'is_enabled' => 'boolean',
        ];
    }
}
