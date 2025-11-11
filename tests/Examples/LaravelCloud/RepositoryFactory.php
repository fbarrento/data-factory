<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Repository>
 */
class RepositoryFactory extends Factory
{
    protected string $dataObject = Repository::class;

    public function definition(): array
    {
        $username = $this->fake->userName();
        $repoName = $this->fake->slug();

        return [
            'id' => $this->fake->uuid(),
            'name' => $repoName,
            'fullName' => "{$username}/{$repoName}",
            'defaultBranch' => 'main',
        ];
    }

    public function legacy(): self
    {
        return $this->state(['defaultBranch' => 'master']);
    }
}
