# Class Integration

The `HasDataFactory` trait allows you to integrate factory functionality directly into your classes, providing a clean and intuitive API.

## Adding the Trait

Use the `HasDataFactory` trait on your class and implement the `newFactory()` method:

```php
<?php

use FBarrento\DataFactory\HasDataFactory;

class Application
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $region,
    ) {}

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}
```

## Using the Static `factory()` Method

Once the trait is added, you can call `factory()` directly on your class:

```php
// Create a single instance
$application = Application::factory()->make();

// Create multiple instances
$applications = Application::factory()->count(5)->make();

// Use states
$production = Application::factory()->production()->make();

// Override attributes
$custom = Application::factory()->make([
    'name' => 'My Application',
    'region' => 'eu-west-1',
]);
```

## Complete Example

```php
<?php

use FBarrento\DataFactory\Factory;
use FBarrento\DataFactory\HasDataFactory;

// Model class
class Deployment
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $status,
        public string $branchName,
        public string $commitHash,
        public ?string $commitMessage = null,
    ) {}

    protected static function newFactory(): DeploymentFactory
    {
        return new DeploymentFactory();
    }
}

// Factory class
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
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'status' => 'deployment.succeeded',
        ]);
    }
}

// Usage
$deployment = Deployment::factory()->make();
$succeeded = Deployment::factory()->succeeded()->make();
$multiple = Deployment::factory()->count(10)->make();
```

## Benefits

### 1. Clean Syntax

```php
// With trait
$app = Application::factory()->make();

// Without trait
$app = ApplicationFactory::new()->make();
```

### 2. IDE Autocompletion

Your IDE can autocomplete `Application::factory()` when you have the class open.

### 3. Consistency

If you're familiar with Laravel's Eloquent factories, this syntax will feel natural:

```php
// Laravel Eloquent
$user = User::factory()->create();

// Data Factory
$user = User::factory()->make();
```

## Real-World Example: Laravel Cloud Classes

```php
<?php

use FBarrento\DataFactory\HasDataFactory;

class Environment
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $status,
        public string $phpMajorVersion,
        public bool $usesOctane,
    ) {}

    protected static function newFactory(): EnvironmentFactory
    {
        return new EnvironmentFactory();
    }
}

class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'slug' => $this->fake->slug(),
            'status' => 'running',
            'phpMajorVersion' => '8.4',
            'usesOctane' => false,
        ];
    }

    public function production(): static
    {
        return $this->state([
            'name' => 'production',
            'slug' => 'production',
            'usesOctane' => true,
        ]);
    }

    public function staging(): static
    {
        return $this->state([
            'name' => 'staging',
            'slug' => 'staging',
        ]);
    }
}

// Clean usage in tests
$production = Environment::factory()->production()->make();
$staging = Environment::factory()->staging()->make();
$environments = Environment::factory()->count(5)->make();
```

## Multiple Classes Example

```php
<?php

// Organization Class
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

// Repository Class
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

class RepositoryFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->slug(),
            'fullName' => $this->fake->userName() . '/' . $this->fake->slug(),
            'defaultBranch' => 'main',
        ];
    }
}

// Application Class with nested factories
class Application
{
    use HasDataFactory;

    public function __construct(
        public string $id,
        public string $name,
        public Repository $repository,
        public Organization $organization,
    ) {}

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}

class ApplicationFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->company(),
            'repository' => Repository::factory(),  // Uses the trait!
            'organization' => Organization::factory(),  // Uses the trait!
        ];
    }
}

// Usage
$application = Application::factory()->make();
// $application->repository and $application->organization are automatically created
```

## When to Use the Trait

### Use `HasDataFactory` when:
- You have dedicated classes (domain models, DTOs, value objects)
- You want clean, consistent syntax
- You're building a domain model
- You want IDE autocompletion on the class

### Skip the trait when:
- You're only creating arrays (use `ArrayFactory` directly)
- You don't have dedicated classes
- You prefer explicit factory instantiation
- You're creating ad-hoc test data

## Organization Pattern

A common pattern is to organize your code like this:

```
src/
├── Domain/
│   ├── Application.php
│   ├── Environment.php
│   └── Deployment.php
└── Factories/
    ├── ApplicationFactory.php
    ├── EnvironmentFactory.php
    └── DeploymentFactory.php
```

Or co-locate factories with classes:

```
src/
├── Application/
│   ├── Application.php
│   └── ApplicationFactory.php
├── Environment/
│   ├── Environment.php
│   └── EnvironmentFactory.php
└── Deployment/
    ├── Deployment.php
    └── DeploymentFactory.php
```

## Type Safety

The trait maintains full type safety:

```php
class Application
{
    use HasDataFactory;

    // ... constructor

    protected static function newFactory(): ApplicationFactory  // Return type enforced
    {
        return new ApplicationFactory();
    }
}
```

Your IDE and static analysis tools (like PHPStan) will know that `Application::factory()` returns an `ApplicationFactory` instance.

## Next Steps

- [Advanced Examples](advanced-examples.md) - Complete real-world examples
- [Testing](testing.md) - Use factories with the trait in PEST tests
- [Nested Factories](nested-factories.md) - Combine trait usage with nested factories
