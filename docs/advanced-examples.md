# Advanced Examples

This guide showcases real-world examples inspired by the Laravel Cloud API, demonstrating how to combine all of Data Factory's features to create complex, realistic test data.

## Complete Laravel Cloud API Models

### 1. Organization

A simple class representing an organization:

```php
<?php

use FBarrento\DataFactory\Factory;
use FBarrento\DataFactory\HasDataFactory;

class Organization
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
    ) {}

    protected static function newFactory(): OrganizationFactory
    {
        return new OrganizationFactory();
    }
}
```

The factory generates organizations with realistic company names:

```php
<?php

class OrganizationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
        ];
    }
}
```

### 2. Repository

The `Repository` class represents a Git repository:

```php
<?php

class Repository
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $fullName,
        public string $defaultBranch,
    ) {}

    protected static function newFactory(): RepositoryFactory
    {
        return new RepositoryFactory();
    }
}
```

The factory creates realistic repository data with a username/repo format:

```php
<?php

class RepositoryFactory extends Factory
{
    protected function definition(): array
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

    public function legacy(): static
    {
        return $this->state([
            'defaultBranch' => 'master',
        ]);
    }
}
```

### 3. Deployment

The `Deployment` class represents a deployment with various states and timestamps:

```php
<?php

class Deployment
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $status,
        public string $branchName,
        public string $commitHash,
        public string $commitMessage,
        public ?string $failureReason = null,
        public string $phpMajorVersion = '8.4',
        public bool $usesOctane = false,
        public ?\DateTimeInterface $startedAt = null,
        public ?\DateTimeInterface $finishedAt = null,
    ) {}

    protected static function newFactory(): DeploymentFactory
    {
        return new DeploymentFactory();
    }
}
```

The `DeploymentFactory` provides different deployment states with realistic timestamps and failure reasons:

```php
<?php

class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'pending',
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

    public function running(): static
    {
        return $this->state([
            'status' => 'deployment.running',
            'startedAt' => $this->fake->dateTimeBetween('-10 minutes', 'now'),
        ]);
    }

    public function succeeded(): static
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => 'deployment.succeeded',
            'startedAt' => $startedAt,
            'finishedAt' => $this->fake->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function failed(): static
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => 'deployment.failed',
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

    public function withOctane(): static
    {
        return $this->state([
            'usesOctane' => true,
        ]);
    }
}
```

### 4. Environment

The `Environment` class represents a deployment environment with configuration and optional current deployment:

```php
<?php

class Environment
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $status,
        public string $vanityDomain,
        public string $phpMajorVersion,
        public int $nodeVersion,
        public bool $usesOctane,
        public bool $usesHibernation,
        public ?Deployment $currentDeployment = null,
        public \DateTimeInterface $createdAt,
    ) {}

    protected static function newFactory(): EnvironmentFactory
    {
        return new EnvironmentFactory();
    }
}
```

The factory provides states for production, staging, and preview environments with nested deployments:

```php
<?php

class EnvironmentFactory extends Factory
{
    protected function definition(): array
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

    public function production(): static
    {
        return $this->state([
            'name' => 'production',
            'slug' => 'production',
            'status' => 'running',
            'phpMajorVersion' => '8.4',
            'nodeVersion' => 22,
            'usesOctane' => true,
            'currentDeployment' => fn () => Deployment::factory()->succeeded()->make(),
        ]);
    }

    public function staging(): static
    {
        return $this->state([
            'name' => 'staging',
            'slug' => 'staging',
            'status' => 'running',
            'phpMajorVersion' => '8.4',
            'currentDeployment' => fn () => Deployment::factory()->succeeded()->make(),
        ]);
    }

    public function preview(): static
    {
        return $this->state([
            'name' => $this->fake->word() . '-preview',
            'slug' => $this->fake->slug() . '-preview',
            'status' => 'hibernating',
            'usesHibernation' => true,
        ]);
    }

    public function withDeployment(string $status = 'succeeded'): static
    {
        return $this->state([
            'status' => 'running',
            'currentDeployment' => fn () => Deployment::factory()->$status()->make(),
        ]);
    }
}
```

### 5. Application (Complete)

The `Application` class brings everything together with nested resources:

```php
<?php

class Application
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $region,
        public Repository $repository,
        public Organization $organization,
        public ?Environment $defaultEnvironment = null,
        public array $environments = [],
        public array $deployments = [],
        public \DateTimeInterface $createdAt,
    ) {}

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}
```

The factory demonstrates advanced patterns with multiple nested factories, helper methods, and sequences:

```php
<?php

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company() . ' App',
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

    public function withEnvironments(): static
    {
        return $this->state([
            'defaultEnvironment' => fn () => Environment::factory()->production()->make(),
            'environments' => fn () => [
                Environment::factory()->production()->make(),
                Environment::factory()->staging()->make(),
                Environment::factory()->preview()->make(),
            ],
        ]);
    }

    public function withDeployments(int $count = 5): static
    {
        return $this->state([
            'deployments' => fn () => Deployment::factory()
                ->count($count)
                ->sequence(
                    ['status' => 'deployment.succeeded'],
                    ['status' => 'deployment.succeeded'],
                    ['status' => 'deployment.succeeded'],
                    ['status' => 'deployment.failed']
                )
                ->make(),
        ]);
    }

    public function complete(): static
    {
        return $this
            ->withEnvironments()
            ->withDeployments(10);
    }
}
```

## Usage Examples

### Basic Application

```php
$application = Application::factory()->make();
```

### Application with Environments

```php
$application = Application::factory()->withEnvironments()->make();

echo $application->defaultEnvironment->name;  // "production"
echo count($application->environments);        // 3
```

### Complete Application

```php
$application = Application::factory()->complete()->make();

// Has repository, organization, 3 environments, and 10 deployments
echo $application->repository->fullName;
echo $application->organization->name;
echo count($application->environments);   // 3
echo count($application->deployments);    // 10
```

### Multiple Applications

```php
$applications = Application::factory()
    ->complete()
    ->count(5)
    ->make();
```

### Custom Application

```php
$application = Application::factory()
    ->withEnvironments()
    ->make([
        'name' => 'My Custom Application',
        'region' => 'eu-west-1',
        'repository' => Repository::factory()->legacy()->make(),
    ]);
```

## JSON:API Response Examples

### Single Resource Response

```php
use FBarrento\DataFactory\ArrayFactory;

class DeploymentResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'deployments',
            'attributes' => [
                'status' => 'deployment.succeeded',
                'branch_name' => 'main',
                'commit_hash' => $this->fake->sha1(),
                'commit_message' => $this->fake->sentence(),
                'started_at' => $this->fake->iso8601(),
                'finished_at' => $this->fake->iso8601(),
            ],
            'relationships' => [
                'environment' => [
                    'data' => [
                        'type' => 'environments',
                        'id' => $this->fake->uuid(),
                    ],
                ],
            ],
        ];
    }
}

$response = [
    'data' => DeploymentResourceFactory::new()->make(),
];
```

### Paginated Collection Response

```php
class ApplicationCollectionFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'data' => fn () => ApplicationResourceFactory::new()->count(15)->make(),
            'links' => [
                'first' => 'https://cloud.laravel.com/api/applications?page=1',
                'last' => 'https://cloud.laravel.com/api/applications?page=5',
                'prev' => null,
                'next' => 'https://cloud.laravel.com/api/applications?page=2',
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 5,
                'per_page' => 15,
                'to' => 15,
                'total' => 73,
            ],
        ];
    }

    public function page(int $page, int $totalPages = 5): static
    {
        return $this->state([
            'links' => [
                'first' => 'https://cloud.laravel.com/api/applications?page=1',
                'last' => "https://cloud.laravel.com/api/applications?page={$totalPages}",
                'prev' => $page > 1 ? "https://cloud.laravel.com/api/applications?page=" . ($page - 1) : null,
                'next' => $page < $totalPages ? "https://cloud.laravel.com/api/applications?page=" . ($page + 1) : null,
            ],
            'meta' => [
                'current_page' => $page,
            ],
        ]);
    }
}

// First page
$page1 = ApplicationCollectionFactory::new()->make();

// Second page
$page2 = ApplicationCollectionFactory::new()->page(2)->make();

// Convert to JSON
$json = json_encode($page1, JSON_PRETTY_PRINT);
```

### Complete Application Resource with Includes

```php
class ApplicationResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        $appId = $this->fake->uuid();
        $repoId = $this->fake->uuid();
        $orgId = $this->fake->uuid();

        return [
            'data' => [
                'id' => $appId,
                'type' => 'applications',
                'attributes' => [
                    'name' => $this->fake->company() . ' App',
                    'slug' => $this->fake->slug(),
                    'region' => 'us-east-2',
                    'created_at' => $this->fake->iso8601(),
                ],
                'relationships' => [
                    'repository' => [
                        'data' => ['type' => 'repositories', 'id' => $repoId],
                    ],
                    'organization' => [
                        'data' => ['type' => 'organizations', 'id' => $orgId],
                    ],
                    'environments' => [
                        'data' => fn () => [
                            ['type' => 'environments', 'id' => $this->fake->uuid()],
                            ['type' => 'environments', 'id' => $this->fake->uuid()],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => $repoId,
                    'type' => 'repositories',
                    'attributes' => [
                        'name' => $this->fake->slug(),
                        'full_name' => $this->fake->userName() . '/' . $this->fake->slug(),
                    ],
                ],
                [
                    'id' => $orgId,
                    'type' => 'organizations',
                    'attributes' => [
                        'name' => $this->fake->company(),
                    ],
                ],
            ],
        ];
    }
}

$response = ApplicationResourceFactory::new()->make();
```

## Testing Scenarios

### Deployment Timeline

```php
// Simulate a week of deployments
$deployments = Deployment::factory()
    ->count(20)
    ->sequence(fn (Sequence $seq) => [
        'branchName' => $seq->index % 5 === 0 ? 'main' : "feature/task-{$seq->index}",
        'commitMessage' => "Deploy #{$seq->index}: {$this->fake->sentence()}",
    ])
    ->sequence(
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.failed']
    )
    ->make();
```

### Multi-Environment Application

```php
$environments = Environment::factory()
    ->count(4)
    ->sequence(
        ['name' => 'production', 'status' => 'running', 'usesOctane' => true],
        ['name' => 'staging', 'status' => 'running', 'usesOctane' => true],
        ['name' => 'development', 'status' => 'running', 'usesOctane' => false],
        ['name' => 'preview', 'status' => 'hibernating', 'usesHibernation' => true]
    )
    ->make();
```

## Next Steps

- [Testing](testing.md) - Use these examples in PEST tests
- [Nested Factories](nested-factories.md) - Deep dive into nested patterns
- [Array Factories](array-factories.md) - More on JSON:API responses
