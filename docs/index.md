# Data Factory Documentation

A powerful test data factory library for PHP 8.4+. Create flexible, type-safe test data for your PHP applications without framework dependencies.

## Why Data Factory?

- **Framework-agnostic**: Works with any PHP class or array structure, not tied to Eloquent
- **Type-safe**: Full PHP 8.4+ generics support with 100% type coverage
- **Flexible**: Supports both objects and arrays, perfect for API testing
- **Powerful sequences**: Built-in cycling sequences with closure support
- **Clean syntax**: Chainable API inspired by Laravel but lighter and more versatile
- **Well-tested**: 100% code coverage, PHPStan level 9 compliant

## Features

- ✅ Create single or multiple instances with `make()` and `count()`
- ✅ Generate realistic fake data with integrated Faker support
- ✅ Define reusable states for different variations
- ✅ Use sequences to cycle through values
- ✅ Nest factories for complex object graphs
- ✅ Create arrays instead of objects with `ArrayFactory`
- ✅ Integrate with your classes using `HasDataFactory` trait

## Documentation

### Getting Started
- [Installation](installation.md) - Install and setup Data Factory
- [Basic Usage](basic-usage.md) - Create your first factory

### Core Features
- [Faker Integration](faker.md) - Generate realistic fake data
- [States](states.md) - Define reusable state variations
- [Sequences](sequences.md) - Cycle through values for multiple instances
- [Nested Factories](nested-factories.md) - Create complex object graphs

### Advanced Usage
- [Array Factories](array-factories.md) - Generate arrays for JSON/API responses
- [Model Integration](model-integration.md) - Integrate factories with your classes using HasDataFactory trait
- [Advanced Examples](advanced-examples.md) - Real-world Laravel Cloud API examples
- [Testing](testing.md) - Use factories in your PEST tests

## Quick Example

```php
use FBarrento\DataFactory\Factory;

class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'pending',
            'branch_name' => 'main',
            'commit_hash' => $this->fake->sha1(),
            'started_at' => null,
            'finished_at' => null,
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'status' => 'deployment.succeeded',
            'started_at' => $this->fake->dateTime(),
            'finished_at' => $this->fake->dateTime(),
        ]);
    }
}

// Create a single deployment (pending by default)
$deployment = DeploymentFactory::new()->make();

// Create 5 successful deployments
$deployments = DeploymentFactory::new()
    ->succeeded()
    ->count(5)
    ->make();
```

## Support

- [GitHub Issues](https://github.com/fbarrento/data-factory/issues)
- [Packagist](https://packagist.org/packages/fbarrento/data-factory)
