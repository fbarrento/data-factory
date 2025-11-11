# Testing with Data Factory

**Factories make your tests cleaner, more maintainable, and easier to understand.** Built with PEST in mind, Data Factory works with any PHP testing framework (PHPUnit, PEST, Codeception, etc.), letting you focus on what you're actually testing instead of cluttering tests with repetitive object creation.

## Why Factories for Testing?

Without factories, your tests are full of noise:
```php
// ❌ Hard to read, repetitive
it('processes deployment', function () {
    $deployment = new Deployment('uuid', 'succeeded', 'main', 'abc123',...); // 10+ args
    // actual test buried below
});
```

With factories, tests are focused:
```php
// ✅ Clear, concise
it('processes deployment', function () {
    $deployment = DeploymentFactory::new()->succeeded()->make();
    // actual test is obvious
});
```

📖 **Read more**: [Why Use Factories?](why-factories.md) - Complete guide to the testing problems factories solve

---

This guide shows you how to integrate Data Factory into your tests. While examples use PEST syntax, the concepts apply to any PHP testing framework (PHPUnit, Codeception, etc.).

## Basic Test Setup

```php
<?php

use Tests\Helpers\DeploymentFactory;

it('creates a deployment', function () {
    $deployment = DeploymentFactory::new()->make();

    expect($deployment->status)->toBe('pending');
    expect($deployment->branchName)->toBe('main');
});
```

## Using States in Tests

States make it easy to test different scenarios:

```php
it('handles successful deployments', function () {
    $deployment = DeploymentFactory::new()->succeeded()->make();

    expect($deployment->status)->toBe('deployment.succeeded');
    expect($deployment->finishedAt)->not->toBeNull();
});

it('handles failed deployments', function () {
    $deployment = DeploymentFactory::new()->failed()->make();

    expect($deployment->status)->toBe('deployment.failed');
    expect($deployment->failureReason)->not->toBeNull();
});
```

## Testing with Multiple Instances

```php
it('processes multiple deployments', function () {
    $deployments = DeploymentFactory::new()->count(5)->make();

    expect($deployments)->toHaveCount(5);
    expect($deployments[0])->toBeInstanceOf(Deployment::class);
});
```

## Using Datasets

PEST datasets work great with factories:

```php
it('handles different deployment statuses', function (string $status) {
    $deployment = DeploymentFactory::new()
        ->state(['status' => $status])
        ->make();

    expect($deployment->status)->toBe($status);
})->with([
    'pending',
    'deployment.running',
    'deployment.succeeded',
    'deployment.failed',
]);
```

## Testing Nested Structures

```php
it('creates application with repository', function () {
    $application = ApplicationFactory::new()->make();

    expect($application->repository)->toBeInstanceOf(Repository::class);
    expect($application->repository->fullName)->toContain('/');
});

it('creates application with environments', function () {
    $application = ApplicationFactory::new()
        ->withEnvironments()
        ->make();

    expect($application->environments)->toHaveCount(3);
    expect($application->defaultEnvironment->name)->toBe('production');
});
```

## Array Factory Testing

Test JSON responses with array factories:

```php
use Tests\Helpers\DeploymentArrayFactory;

it('returns deployment as array', function () {
    $data = DeploymentArrayFactory::new()->make();

    expect($data)->toBeArray();
    expect($data)->toHaveKeys(['id', 'status', 'branch_name']);
});

it('encodes deployment to JSON', function () {
    $data = DeploymentArrayFactory::new()->make();
    $json = json_encode($data);

    expect($json)->toBeJson();
    expect($json)->toContain('deployment');
});
```

## Testing API Responses

Mock API responses using array factories:

```php
use Tests\Helpers\ApplicationResourceFactory;

it('returns paginated applications', function () {
    $response = [
        'data' => ApplicationResourceFactory::new()->count(15)->make(),
        'meta' => [
            'total' => 15,
            'per_page' => 15,
        ],
    ];

    expect($response['data'])->toHaveCount(15);
    expect($response['data'][0])->toHaveKeys(['id', 'type', 'attributes']);
});
```

## Sequence Testing

Test variations across multiple instances:

```php
it('creates deployments with different statuses', function () {
    $deployments = DeploymentFactory::new()
        ->count(4)
        ->sequence(
            ['status' => 'pending'],
            ['status' => 'running'],
            ['status' => 'succeeded'],
            ['status' => 'failed']
        )
        ->make();

    expect($deployments[0]->status)->toBe('pending');
    expect($deployments[1]->status)->toBe('running');
    expect($deployments[2]->status)->toBe('succeeded');
    expect($deployments[3]->status)->toBe('failed');
});
```

## Custom Attributes in Tests

Override factory defaults for specific test cases:

```php
it('creates deployment for specific branch', function () {
    $deployment = DeploymentFactory::new()->make([
        'branchName' => 'feature/custom-feature',
        'commitMessage' => 'Add custom feature',
    ]);

    expect($deployment->branchName)->toBe('feature/custom-feature');
    expect($deployment->commitMessage)->toBe('Add custom feature');
});
```

## Common Testing Patterns

### Arrange-Act-Assert with Factories

Factories shine in the Arrange phase of your tests:

```php
it('deploys application successfully', function () {
    // Arrange - clean setup with factories
    $deployment = DeploymentFactory::new()->pending()->make();
    $deployer = new ApplicationDeployer();

    // Act - the code being tested
    $result = $deployer->deploy($deployment);

    // Assert - verify the outcome
    expect($result)->toBeTrue()
        ->and($deployment->status)->toBe('deployment.succeeded');
});
```

### Testing Edge Cases with States

Use state methods to easily test different scenarios:

```php
it('handles deployment failures gracefully', function () {
    $deployment = DeploymentFactory::new()->failed()->make();

    expect($deployment->failureReason)->not->toBeNull();
    // Your failure handling logic
});

it('handles long-running deployments', function () {
    $deployment = DeploymentFactory::new()->running()->make();

    expect($deployment->startedAt)->not->toBeNull()
        ->and($deployment->finishedAt)->toBeNull();
});
```

### Testing Relationships

Nested factories make testing relationships simple:

```php
it('environment includes current deployment', function () {
    $environment = EnvironmentFactory::new()->production()->make();

    expect($environment->currentDeployment)->toBeInstanceOf(Deployment::class)
        ->and($environment->currentDeployment->status)->toBe('deployment.succeeded');
});
```

### Testing Validation

Test validation rules with custom attributes:

```php
it('rejects deployment with invalid branch name', function () {
    $deployment = DeploymentFactory::new()->make([
        'branchName' => '',  // Invalid empty branch
    ]);

    expect(fn() => $validator->validate($deployment))
        ->toThrow(ValidationException::class);
});

it('accepts deployment with valid data', function () {
    $deployment = DeploymentFactory::new()->make();

    $validator->validate($deployment);  // Should not throw
})->throwsNoExceptions();
```

## Testing with HasDataFactory Trait

When your models use the `HasDataFactory` trait:

```php
use Tests\Models\Application;

it('creates application using factory method', function () {
    $application = Application::factory()->make();

    expect($application)->toBeInstanceOf(Application::class);
});

it('creates production application', function () {
    $application = Application::factory()
        ->withEnvironments()
        ->make();

    expect($application->defaultEnvironment->name)->toBe('production');
});
```

## Real-World Test Examples

### Testing Deployment Logic

```php
it('validates deployment status transitions', function () {
    $pending = DeploymentFactory::new()->make();
    $running = DeploymentFactory::new()->running()->make();
    $succeeded = DeploymentFactory::new()->succeeded()->make();

    expect($pending->status)->toBe('pending');
    expect($running->status)->toBe('deployment.running');
    expect($running->startedAt)->not->toBeNull();
    expect($succeeded->status)->toBe('deployment.succeeded');
    expect($succeeded->finishedAt)->not->toBeNull();
});
```

### Testing Environment Configuration

```php
it('configures production environment correctly', function () {
    $environment = EnvironmentFactory::new()->production()->make();

    expect($environment->name)->toBe('production');
    expect($environment->status)->toBe('running');
    expect($environment->phpMajorVersion)->toBe('8.4');
    expect($environment->usesOctane)->toBeTrue();
});

it('configures staging environment correctly', function () {
    $environment = EnvironmentFactory::new()->staging()->make();

    expect($environment->name)->toBe('staging');
    expect($environment->status)->toBe('running');
});
```

### Testing Application Relationships

```php
it('creates complete application with all relationships', function () {
    $application = ApplicationFactory::new()->complete()->make();

    expect($application->repository)->toBeInstanceOf(Repository::class);
    expect($application->organization)->toBeInstanceOf(Organization::class);
    expect($application->environments)->toHaveCount(3);
    expect($application->deployments)->toHaveCount(10);

    // Verify environment types
    $envNames = array_map(fn($env) => $env->name, $application->environments);
    expect($envNames)->toContain('production', 'staging');
});
```

### Testing JSON:API Responses

```php
it('returns valid JSON:API application resource', function () {
    $resource = ApplicationResourceFactory::new()->make();

    expect($resource)->toHaveKeys(['data', 'included']);
    expect($resource['data'])->toHaveKeys(['id', 'type', 'attributes', 'relationships']);
    expect($resource['data']['type'])->toBe('applications');
    expect($resource['included'])->toBeArray();
});

it('returns paginated JSON:API collection', function () {
    $collection = ApplicationCollectionFactory::new()->make();

    expect($collection)->toHaveKeys(['data', 'links', 'meta']);
    expect($collection['data'])->toHaveCount(15);
    expect($collection['meta'])->toHaveKeys(['current_page', 'total', 'per_page']);
});
```

## Organizing Test Factories

### Directory Structure

```
tests/
├── Helpers/
│   ├── ApplicationFactory.php
│   ├── EnvironmentFactory.php
│   ├── DeploymentFactory.php
│   ├── RepositoryFactory.php
│   └── OrganizationFactory.php
├── Feature/
│   ├── ApplicationTest.php
│   ├── EnvironmentTest.php
│   └── DeploymentTest.php
└── Unit/
    └── FactoryTest.php
```

### Test Helper Functions

Create helper functions for common test scenarios:

```php
// tests/Helpers/helpers.php

function createApplication(array $attributes = []): Application
{
    return Application::factory()->make($attributes);
}

function createProductionEnvironment(array $attributes = []): Environment
{
    return Environment::factory()->production()->make($attributes);
}

function createDeploymentTimeline(int $count = 10): array
{
    return Deployment::factory()
        ->count($count)
        ->sequence(
            ['status' => 'deployment.succeeded'],
            ['status' => 'deployment.succeeded'],
            ['status' => 'deployment.succeeded'],
            ['status' => 'deployment.failed']
        )
        ->make();
}
```

Then use them in tests:

```php
it('processes application', function () {
    $application = createApplication(['name' => 'Test App']);

    expect($application->name)->toBe('Test App');
});
```

## Performance Tips

### Reuse Factories

```php
// Good: Reuse factory instance
beforeEach(function () {
    $this->factory = DeploymentFactory::new();
});

it('creates deployment', function () {
    $deployment = $this->factory->make();
    expect($deployment)->toBeInstanceOf(Deployment::class);
});
```

### Minimize Nested Factory Depth

```php
// For simple tests, avoid deep nesting
it('tests deployment status', function () {
    $deployment = DeploymentFactory::new()->make();
    // Don't create full application just to test deployment
});

// Only use nested factories when testing relationships
it('tests application with deployments', function () {
    $application = ApplicationFactory::new()->withDeployments()->make();
    expect($application->deployments)->not->toBeEmpty();
});
```

## Best Practices

1. **Use States for Test Scenarios**: Define states like `->production()`, `->failed()` for common test cases
2. **Keep Factories Focused**: Each factory should represent one entity
3. **Use Sequences for Variations**: Test different values across multiple instances
4. **Override Attributes When Needed**: Use `make([...])` for test-specific values
5. **Test the Factory Itself**: Write tests to ensure your factories work correctly
6. **Use Datasets**: Combine PEST datasets with factories for comprehensive testing

## Next Steps

- [States](states.md) - Define reusable test scenarios
- [Sequences](sequences.md) - Create variations for testing
- [Advanced Examples](advanced-examples.md) - Complex testing scenarios
