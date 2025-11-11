<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Environment>
 */
class EnvironmentFactory extends Factory
{
    protected string $dataObject = Environment::class;

    public function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'slug' => $this->fake->slug(),
            'status' => 'stopped',
            'vanityDomain' => $this->fake->domainName(),
            'phpMajorVersion' => '8.3',
            'nodeVersion' => 20,
            'usesOctane' => false,
            'usesHibernation' => false,
            'currentDeployment' => null,
            'createdAt' => $this->fake->dateTimeBetween('-6 months'),
        ];
    }

    public function production(): self
    {
        return $this->state([
            'name' => 'production',
            'slug' => 'production',
            'status' => 'running',
            'phpMajorVersion' => '8.4',
            'nodeVersion' => 22,
            'usesOctane' => true,
            'currentDeployment' => function (): Deployment {
                /** @var Deployment */
                $deployment = Deployment::factory()->succeeded()->make();

                return $deployment;
            },
        ]);
    }

    public function staging(): self
    {
        return $this->state([
            'name' => 'staging',
            'slug' => 'staging',
            'status' => 'running',
            'phpMajorVersion' => '8.4',
            'currentDeployment' => function (): Deployment {
                /** @var Deployment */
                $deployment = Deployment::factory()->succeeded()->make();

                return $deployment;
            },
        ]);
    }

    public function preview(): self
    {
        return $this->state([
            'name' => $this->fake->word().'-preview',
            'slug' => $this->fake->slug().'-preview',
            'status' => 'hibernating',
            'usesHibernation' => true,
        ]);
    }

    public function withDeployment(string $status = 'succeeded'): self
    {
        return $this->state([
            'status' => 'running',
            // @phpstan-ignore-next-line return.type
            'currentDeployment' => fn (): Deployment => match ($status) {
                'running' => Deployment::factory()->running()->make(),
                'succeeded' => Deployment::factory()->succeeded()->make(),
                'failed' => Deployment::factory()->failed()->make(),
                default => Deployment::factory()->make(),
            },
        ]);
    }
}
