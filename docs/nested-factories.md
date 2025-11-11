# Nested Factories

Nested factories allow you to create complex object graphs with relationships between entities. This is essential for modeling real-world data structures like API responses with nested resources.

## Why Nested Factories?

Consider a Laravel Cloud Application that has:
- A Repository
- Multiple Environments
- Multiple Deployments
- An Organization

Nested factories make it easy to create these complex structures.

## Three Ways to Nest Factories

### 1. Static `factory()` Method (Recommended)

The cleanest approach uses the static `factory()` method:

```php
<?php

use FBarrento\DataFactory\Factory;

class RepositoryFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->slug(),
            'full_name' => $this->fake->userName() . '/' . $this->fake->slug(),
            'default_branch' => 'main',
        ];
    }
}

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'slug' => $this->fake->slug(),
            'repository' => RepositoryFactory::factory(),  // Nested factory
            'region' => 'us-east-2',
        ];
    }
}

$application = ApplicationFactory::new()->make();
// $application->repository is a Repository object
```

### 2. Direct Factory Instantiation

You can also create a new factory instance directly:

```php
class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => new RepositoryFactory(),  // Direct instantiation
        ];
    }
}
```

### 3. Closure-Based (For Dynamic Control)

Use a closure when you need dynamic behavior or want to customize the nested factory:

```php
class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => fn () => RepositoryFactory::factory()
                ->make(['default_branch' => 'develop']),
        ];
    }
}
```

## Multiple Nested Factories

Create multiple nested resources:

```php
class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => RepositoryFactory::factory(),
            'organization' => OrganizationFactory::factory(),
            'default_environment' => EnvironmentFactory::factory(),
        ];
    }
}
```

## Arrays of Nested Factories

Create arrays of nested resources using closures:

```php
class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => RepositoryFactory::factory(),
            'environments' => fn () => EnvironmentFactory::new()->count(3)->make(),
            'deployments' => fn () => DeploymentFactory::new()->count(5)->make(),
        ];
    }
}

$application = ApplicationFactory::new()->make();
// $application->environments is an array of 3 Environment objects
// $application->deployments is an array of 5 Deployment objects
```

## Overriding Nested Factory Attributes

You can override attributes of nested factories when creating instances:

```php
$application = ApplicationFactory::new()->make([
    'name' => 'My Custom App',
    'repository' => RepositoryFactory::new()->make([
        'default_branch' => 'feature/custom',
    ]),
]);
```

## Deep Nesting

Nest factories multiple levels deep:

```php
class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'current_deployment' => DeploymentFactory::factory(),
            'primary_domain' => DomainFactory::factory(),
        ];
    }
}

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => RepositoryFactory::factory(),
            'default_environment' => EnvironmentFactory::factory(),
            // default_environment has its own nested deployment and domain
        ];
    }
}

$application = ApplicationFactory::new()->make();
// $application->default_environment->current_deployment is a Deployment
// $application->default_environment->primary_domain is a Domain
```

## Fresh Instances

Each nested factory creates a fresh instance:

```php
$app1 = ApplicationFactory::new()->make();
$app2 = ApplicationFactory::new()->make();

// Each application gets its own unique repository
$app1->repository->id !== $app2->repository->id  // true
```

## Nested Factories with States

Combine nested factories with states:

```php
class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'current_deployment' => DeploymentFactory::factory(),
        ];
    }

    public function production(): static
    {
        return $this->state([
            'name' => 'production',
            'current_deployment' => fn () => DeploymentFactory::new()
                ->succeeded()
                ->make(),
        ]);
    }
}

$env = EnvironmentFactory::new()->production()->make();
// $env->current_deployment has status 'deployment.succeeded'
```

## Real-World Example: Complete Application

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

class RepositoryFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->slug(),
            'full_name' => $this->fake->userName() . '/' . $this->fake->slug(),
            'default_branch' => 'main',
        ];
    }
}

class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'deployment.succeeded',
            'branch_name' => 'main',
            'commit_hash' => $this->fake->sha1(),
            'started_at' => $this->fake->dateTime(),
            'finished_at' => $this->fake->dateTime(),
        ];
    }
}

class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'status' => 'running',
            'current_deployment' => DeploymentFactory::factory(),
        ];
    }
}

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'slug' => $this->fake->slug(),
            'region' => 'us-east-2',
            'repository' => RepositoryFactory::factory(),
            'organization' => OrganizationFactory::factory(),
            'default_environment' => EnvironmentFactory::factory(),
            'environments' => fn () => EnvironmentFactory::new()->count(3)->make(),
            'deployments' => fn () => DeploymentFactory::new()->count(5)->make(),
        ];
    }
}

// Create a complete application with all nested resources
$application = ApplicationFactory::new()->make();

// Access nested data
echo $application->name;
echo $application->repository->full_name;
echo $application->organization->name;
echo $application->default_environment->current_deployment->status;
echo count($application->environments);  // 3
echo count($application->deployments);   // 5
```

## Nested Array Factories

You can nest `ArrayFactory` instances for creating JSON structures:

```php
use FBarrento\DataFactory\ArrayFactory;

class RepositoryArrayFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->slug(),
        ];
    }
}

class ApplicationArrayFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => RepositoryArrayFactory::factory(),
        ];
    }
}

$data = ApplicationArrayFactory::new()->make();
// Returns nested arrays, perfect for JSON encoding
```

## Next Steps

- [Array Factories](array-factories.md) - Generate arrays for JSON/API responses
- [Advanced Examples](advanced-examples.md) - Complete JSON:API response examples
- [Testing](testing.md) - Use nested factories in tests
