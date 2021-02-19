<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Servidor\Projects\Application;
use Servidor\Rules\Domain;

class CreateProjectRequest extends FormRequest
{
    private const BRANCH_CMD = 'git ls-remote --heads --exit-code "%s" %s';

    private const ERR_NO_REFS = "This branch doesn't exist.";
    private const ERR_NON_ZERO = 'Branch listing failed. Is this repo valid?';
    private const ERR_NOT_FOUND = "This repo couldn't be found. Does it require auth?";

    private const GIT_NO_REFS = 2;
    private const GIT_NOT_FOUND = 128;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:projects,name',
            'applications' => 'array',
            'applications.*.template' => 'required|in:html,php,laravel',
            'applications.*.domain' => [new Domain()],
            'applications.*.provider' => 'required|in:github,bitbucket',
            'applications.*.repository' => 'required|nullable|regex:_^([a-z-]+)/([a-z-]+)$_i',
            'applications.*.branch' => 'nullable|string',
            'redirects' => 'array',
            'redirects.*.domain' => ['required', new Domain()],
            'redirects.*.target' => 'required|string',
            'redirects.*.type' => 'required|integer',
            'is_enabled' => 'boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $apps = $validator->getData()['applications'] ?? [];

            if (!is_array($apps) || empty($apps)) {
                return;
            }

            /**
             * @var array{provider: string, repository: string, branch?: string}
             */
            $app = $apps[0];

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

        exec(sprintf(self::BRANCH_CMD, $repo, $branch), $o, $status);
        unset($o);

        if (self::GIT_NO_REFS === $status) {
            $validator->errors()->add('applications.0.branch', self::ERR_NO_REFS);
        } elseif (0 !== $status) {
            $message = self::GIT_NOT_FOUND === $status
                ? self::ERR_NOT_FOUND : self::ERR_NON_ZERO;

            $validator->errors()->add('applications.0.repository', $message);
        }
    }
}
