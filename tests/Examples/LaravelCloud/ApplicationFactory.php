<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected string $dataObject = Application::class;

    public function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company().' App',
            'slug' => $this->fake->slug(),
            'region' => $this->fake->randomElement(['us-east-1', 'us-east-2', 'us-west-2', 'eu-west-1']),
            'repository' => Repository::factory(),
            'organization' => Organization::factory(),
            'defaultEnvironment' => null,
            'environments' => [],
            'deployments' => [],
            'createdAt' => $this->fake->dateTimeBetween('-1 year', '-1 month'),
        ];
    }

    public function withEnvironments(): self
    {
        return $this->state([
            'defaultEnvironment' => function (): Environment {
                /** @var Environment */
                $env = Environment::factory()->production()->make();

                return $env;
            },
            'environments' => function (): array {
                /** @var Environment */
                $prod = Environment::factory()->production()->make();
                /** @var Environment */
                $staging = Environment::factory()->staging()->make();
                /** @var Environment */
                $preview = Environment::factory()->preview()->make();

                return [$prod, $staging, $preview];
            },
        ]);
    }

    public function withDeployments(int $count = 5): self
    {
        return $this->state([
            'deployments' => function () use ($count): array {
                /** @var array<int, Deployment> */
                $deployments = Deployment::factory()
                    ->count($count)
                    ->sequence(
                        ['status' => DeploymentStatus::Succeeded],
                        ['status' => DeploymentStatus::Succeeded],
                        ['status' => DeploymentStatus::Succeeded],
                        ['status' => DeploymentStatus::Failed]
                    )
                    ->make();

                return $deployments;
            },
        ]);
    }

    public function complete(): self
    {
        return $this
            ->withEnvironments()
            ->withDeployments(10);
    }
}
