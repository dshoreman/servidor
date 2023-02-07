<?php

namespace Servidor\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Servidor\Projects\Application;
use Servidor\Rules\Domain;

class NewProjectApp extends FormRequest
{
    private const BRANCH_CMD = 'git ls-remote --heads --exit-code "%s" %s';

    private const ERR_NO_REFS = "This branch doesn't exist.";
    private const ERR_NON_ZERO = 'Branch listing failed. Is this repo valid?';
    private const ERR_NOT_FOUND = "This repo couldn't be found. Does it require auth?";

    private const GIT_NO_REFS = 2;
    private const GIT_NOT_FOUND = 128;

    public function rules(): array
    {
        return [
            'template' => 'required|in:html,php,laravel',
            'domain' => [new Domain()],
            'includeWww' => 'boolean',
            'provider' => 'required|in:github,bitbucket',
            'repository' => 'required|nullable|regex:_^([a-z-]+)/([a-z-]+)$_i',
            'branch' => 'nullable|string',
            'config' => 'sometimes|required|array:phpVersion,redirectWww,ssl,sslCertificate,sslPrivateKey,sslRedirect',
            'config.phpVersion' => 'sometimes|required|regex:/^[7-8]\.[0-4]$/',
            'config.ssl' => 'sometimes|required|boolean',
            'config.sslCertificate' => 'sometimes|required|string|filled',
            'config.sslPrivateKey' => 'sometimes|required|string|filled',
            'config.sslRedirect' => 'sometimes|required|boolean',
            'config.redirectWww' => 'sometimes|required|boolean',
        ];
    }

    public function validated(): array
    {
        $data = parent::validated();

        return [
            'template' => $data['template'],
            'domain_name' => $data['domain'],
            'include_www' => $data['includeWww'] ?? false,
            'source_provider' => $data['provider'],
            'source_repository' => $data['repository'],
            'source_branch' => $data['branch'] ?? '',
            'config' => $data['config'] ?? [],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /**
             * @var array{provider?: string, repository?: string, branch?: string}
             */
            $app = $validator->getData();

            if (isset($app['repository'], $app['provider'])) {
                $this->validateAppRepository($validator, $app);
            }
        });
    }

    /** @param array{provider: string, repository: string, branch?: string} $app */
    private function validateAppRepository(Validator $validator, array $app): void
    {
        $branch = $app['branch'] ?? '';
        $branch = $branch ? escapeshellarg($branch) : '';
        $repo = Application::SOURCE_PROVIDERS[$app['provider']];
        $repo = str_replace('{repo}', $app['repository'], $repo);

        exec(sprintf(self::BRANCH_CMD, $repo, $branch), $_, $status);

        if (self::GIT_NO_REFS === $status) {
            $validator->errors()->add('branch', self::ERR_NO_REFS);
        } elseif (0 !== $status) {
            $message = self::GIT_NOT_FOUND === $status
                ? self::ERR_NOT_FOUND : self::ERR_NON_ZERO;

            $validator->errors()->add('repository', $message);
        }
    }
}
