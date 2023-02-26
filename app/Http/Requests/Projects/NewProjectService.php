<?php

namespace Servidor\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Servidor\Projects\ProjectService;
use Servidor\Rules\Domain;

class NewProjectService extends FormRequest
{
    private const BRANCH_CMD = 'git ls-remote --heads --exit-code "%s" %s';

    private const ERR_NO_REFS = "This branch doesn't exist.";
    private const ERR_NON_ZERO = 'Branch listing failed. Is this repo valid?';
    private const ERR_NOT_FOUND = "This repo couldn't be found. Does it require auth?";

    private const GIT_NO_REFS = 2;
    private const GIT_NOT_FOUND = 128;

    public function rules(): array
    {
        $rules = [
            'template' => 'required|in:html,php,laravel,redirect',
            'domain' => [new Domain()],
            'includeWww' => 'boolean',
            'config' => 'sometimes|required|array:'
                . implode(',', array_keys($this->configRules())),
        ];

        foreach ($this->configRules() as $field => $ruleset) {
            $rules['config.' . $field] = $ruleset;
        }

        return $rules;
    }

    /**
     * @return array<string,array|string>
     */
    private function configRules(): array
    {
        return [
            'phpVersion' => 'sometimes|required|regex:/^[7-8]\.[0-4]$/',
            'redirect' => 'required_if:template,redirect|array:target,type',
            'redirect.target' => 'required_if:template,redirect|string',
            'redirect.type' => 'required_if:template,redirect|integer',
            'redirectWww' => 'sometimes|required|integer|between:-1,1',
            'source' => [
                'sometimes',
                'required_unless:template,redirect',
                'array:provider,repository,branch',
            ],
            'source.branch' => 'nullable|string',
            'source.provider' => 'required_unless:template,redirect|in:github,bitbucket',
            'source.repository' => [
                'required_unless:template,redirect',
                'regex:_^([a-z-]+)/([a-z-]+)$_i',
                'nullable',
            ],
            'ssl' => 'sometimes|required|boolean',
            'sslCertificate' => 'sometimes|required|string|filled',
            'sslPrivateKey' => 'sometimes|required|string|filled',
            'sslRedirect' => 'sometimes|required|boolean',
        ];
    }

    public function validated(): array
    {
        $data = parent::validated();

        return [
            'template' => $data['template'],
            'domain_name' => $data['domain'],
            'include_www' => $data['includeWww'] ?? false,
            'config' => $data['config'] ?? [],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var array{source: array{provider?:string, branch:string, repository?:string}} $config */
            $config = $validator->getData()['config'];

            if (isset($config['source']['repository'], $config['source']['provider'])) {
                $this->validateAppRepository($validator, $config['source']);
            }
        });
    }

    /**
     * @param array{branch?: string, provider: string, repository: string} $source
     */
    private function validateAppRepository(Validator $validator, array $source): void
    {
        $branch = $source['branch'] ?? '';
        $branch = $branch ? escapeshellarg($branch) : '';
        $repo = ProjectService::SOURCE_PROVIDERS[$source['provider']];
        $repo = str_replace('{repo}', $source['repository'], $repo);

        exec(sprintf(self::BRANCH_CMD, $repo, $branch), $_, $status);

        if (self::GIT_NO_REFS === $status) {
            $validator->errors()->add('config.source.branch', self::ERR_NO_REFS);
        } elseif (0 !== $status) {
            $message = self::GIT_NOT_FOUND === $status
                ? self::ERR_NOT_FOUND : self::ERR_NON_ZERO;

            $validator->errors()->add('config.source.repository', $message);
        }
    }
}
