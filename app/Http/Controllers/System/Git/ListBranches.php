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

        $repo = str_replace('{repo}', $repo, $this->patterns[(string) $provider]);

        exec(sprintf(self::GIT_COMMAND, $repo), $branches);

        return response()->json($branches);
    }

    private function validateParams(array $data): array
    {
        if (!is_string($repo = $data['repository'] ?? '') || '' === $repo) {
            throw $this->fail('repository', 'Missing repository.');
        }

        if (!is_string($provider = ($data['provider'] ?? 'custom'))) {
            throw $this->fail('provider', 'Invalid provider.');
        }
        if (!isset($this->patterns[$provider])) {
            throw $this->fail('provider', 'Unsupported provider "' . $provider . '".');
        }

        return [$repo, $provider];
    }
}
