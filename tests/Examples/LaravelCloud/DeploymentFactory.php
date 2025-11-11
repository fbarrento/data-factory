<?php

declare(strict_types=1);

namespace Tests\Examples\LaravelCloud;

use FBarrento\DataFactory\Factory;

/**
 * @extends Factory<Deployment>
 */
class DeploymentFactory extends Factory
{
    protected string $dataObject = Deployment::class;

    public function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => $this->fake->randomElement(DeploymentStatus::cases()),
            'branchName' => 'main',
            'commitHash' => $this->fake->sha1(),
            'commitMessage' => $this->fake->sentence(),
            'failureReason' => null,
            'phpMajorVersion' => '8.4',
            'usesOctane' => false,
            'startedAt' => null,
            'finishedAt' => null,
        ];
    }

    public function running(): self
    {
        return $this->state([
            'status' => DeploymentStatus::Running,
            'startedAt' => $this->fake->dateTimeBetween('-10 minutes', 'now'),
        ]);
    }

    public function succeeded(): self
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => DeploymentStatus::Succeeded,
            'startedAt' => $startedAt,
            'finishedAt' => $this->fake->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function failed(): self
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => DeploymentStatus::Failed,
            'failureReason' => $this->fake->randomElement([
                'Build failed: npm install exited with code 1',
                'Deployment timeout: exceeded 15 minute limit',
                'Health check failed: application not responding',
                'Database migration failed',
            ]),
            'startedAt' => $startedAt,
            'finishedAt' => $this->fake->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function pending(): self
    {
        return $this->state([
            'status' => DeploymentStatus::Pending,
        ]);
    }

    public function withOctane(): self
    {
        return $this->state([
            'usesOctane' => true,
        ]);
    }
}
