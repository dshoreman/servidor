<?php

namespace Tests\Feature\Api\System\Git;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListBranchesTest extends TestCase
{
    use RequiresAuth;

    protected string $endpoint = '/api/system/git/branches';

    /** @test */
    public function guest_cannot_list_branches(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response->assertJsonCount(1);
    }

    /**
     * @test
     *
     * @dataProvider repositoryProvider
     */
    public function user_can_list_branches(string $repo, ?string $provider = ''): void
    {
        $response = $this->authed()->getJson($this->endpoint . '?' . (
            $provider ? "provider={$provider}&repository={$repo}" : "repository={$repo}"
        ));

        $response->assertJsonMissingValidationErrors(['repository', 'provider']);
        $response->assertOk();
        $response->assertJson(['develop', 'master']);
    }

    /** @test */
    public function repository_is_required(): void
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['repository']);
    }

    /** @test */
    public function provider_must_be_a_string(): void
    {
        $response = $this->authed()->getJson(
            $this->endpoint . '?repository=foo/bar&provider[]=42&provider[]=69',
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['provider']);
    }

    /** @test */
    public function provider_must_be_supported(): void
    {
        $response = $this->authed()->getJson(
            $this->endpoint . '?repository=foo/bar&provider=gitlab',
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['provider']);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function repositoryProvider(): array
    {
        return [
            'github repo' => ['dshoreman/servidor-test-site', 'github'],
            'custom uri' => ['https://github.com/dshoreman/servidor-test-site.git'],
        ];
    }
}
