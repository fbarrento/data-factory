# Basic Usage

## Creating Your First Factory

A factory is a class that extends `FBarrento\DataFactory\Factory` and defines how to create instances of your data objects.

Let's create a factory for a `Deployment` class based on Laravel Cloud API:

```php
<?php

use FBarrento\DataFactory\Factory;

class Deployment
{
    public function __construct(
        public string $id,
        public string $status,
        public string $branchName,
        public string $commitHash,
        public ?string $commitMessage = null,
    ) {}
}

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
}
```

## The `definition()` Method

The `definition()` method returns an array of default attributes. These attributes will be used to create instances of your object.

The keys in the array should match:
- Constructor parameter names (for objects)
- Array keys (for array factories)

## Creating Instances

### Single Instance

Use the `make()` method to create a single instance:

```php
$deployment = DeploymentFactory::new()->make();

echo $deployment->status; // "pending"
echo $deployment->branchName; // "main"
```

### Multiple Instances

Use the `count()` method to create multiple instances:

```php
$deployments = DeploymentFactory::new()->count(5)->make();

// Returns an array of 5 Deployment objects
foreach ($deployments as $deployment) {
    echo $deployment->id . "\n";
}
```

## Overriding Attributes

You can override specific attributes when calling `make()`:

```php
$deployment = DeploymentFactory::new()->make([
    'status' => 'deployment.succeeded',
    'branchName' => 'feature/new-feature',
]);

echo $deployment->status; // "deployment.succeeded"
echo $deployment->branchName; // "feature/new-feature"
echo $deployment->commitHash; // Still uses fake data from definition()
```

## The `new()` Method

The `new()` static method creates a new instance of the factory. This allows for method chaining:

```php
DeploymentFactory::new()
    ->count(3)
    ->make(['status' => 'running']);
```

## Object vs Array Factories

By default, `Factory` creates objects by passing the attributes to the class constructor.

For array-based data (useful for JSON/API responses), use `ArrayFactory` instead:

```php
use FBarrento\DataFactory\ArrayFactory;

class DeploymentArrayFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'pending',
            'branch_name' => 'main',
        ];
    }
}

$deployment = DeploymentArrayFactory::new()->make();
// Returns: ['id' => '...', 'status' => 'pending', 'branch_name' => 'main']
```

Learn more about [Array Factories](array-factories.md).

## Next Steps

- [Faker Integration](faker.md) - Generate realistic fake data
- [States](states.md) - Define reusable variations
- [Sequences](sequences.md) - Cycle through values for multiple instances
