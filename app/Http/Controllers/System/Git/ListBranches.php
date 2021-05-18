<?php

namespace Servidor\Http\Controllers\System\Git;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Servidor\Http\Controllers\System\Controller;
use Servidor\Projects\Application;

class ListBranches extends Controller
{
    private const GIT_COMMAND = "git ls-remote --heads '%s' | sed 's^.*refs/heads/^^'";

    /**
     * @var array
     */
    private $patterns = Application::SOURCE_PROVIDERS;

    public function __invoke(Request $request): JsonResponse
    {
        [$repo, $provider] = $this->validateParams((array) $request->query());

        $repo = str_replace('{repo}', (string) $repo, (string) $this->patterns[(string) $provider]);

        exec(sprintf(self::GIT_COMMAND, $repo), $branches);

        return response()->json($branches);
    }

    private function validateParams(array $data): array
    {
        $repo = $data['repository'] ?? '';
        $provider = $data['provider'] ?? 'custom';

        if (!\is_string($repo) || '' === $repo) {
            throw $this->fail('repository', 'Missing repository.');
        }
        if (!\is_string($provider)) {
            throw $this->fail('provider', 'Invalid provider.');
        }
        if (!isset($this->patterns[$provider])) {
            throw $this->fail('provider', 'Unsupported provider "' . $provider . '".');
        }

        return [$repo, $provider];
    }
}
