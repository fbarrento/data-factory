# Data Factory Documentation

**The modern test data factory for PEST.** Create expressive, maintainable test data for your PEST test suite.

Inspired by Laravel's Eloquent factories, Data Factory follows the same intuitive API you already know—but works with any PHP class, not just Eloquent models.

> **PHP 8.2+ | Fully tested on Windows, Linux, and macOS**

## Why Data Factory?

- **Familiar API**: Inspired by Laravel's factories—if you know Laravel, you already know this
- **Built with PEST in mind**: Works with any PHP testing framework, optimized for PEST's expressive syntax
- **Write DRY test code**: Define test data once, reuse across all tests
- **Readable tests**: Clear intent with named states like `->succeeded()`
- **Easy complex objects**: Create nested graphs without boilerplate
- **Framework-agnostic**: Works with any PHP class or array structure, not tied to Eloquent
- **Type-safe**: Full PHP 8.2+ support with 100% type coverage
- **Well-tested**: 100% code coverage, PHPStan level 9 compliant

## Features

- ✅ Clean, focused test files without repetitive setup code
- ✅ Expressive state methods for testing different scenarios
- ✅ Generate realistic fake data with integrated Faker
- ✅ Create complex object graphs with nested factories
- ✅ Test variations easily with sequences
- ✅ Support for both objects and arrays (perfect for API testing)
- ✅ Integrate with your classes using `HasDataFactory` trait

## Documentation

### Getting Started
- [Installation](installation.md) - Install and setup Data Factory
- [Basic Usage](basic-usage.md) - Create your first factory

### Testing with PEST
- [Why Use Factories?](why-factories.md) - The testing problems factories solve
- [Testing Guide](testing.md) - Complete guide to using factories in PEST tests

### Core Features
- [Faker Integration](faker.md) - Generate realistic fake data
- [States](states.md) - Define reusable state variations for test scenarios
- [Sequences](sequences.md) - Test behavior across multiple variations
- [Nested Factories](nested-factories.md) - Create complex test object graphs

### Advanced Usage
- [Array Factories](array-factories.md) - Generate arrays for JSON/API testing
- [Class Integration](model-integration.md) - Integrate factories with your classes using HasDataFactory trait
- [Advanced Examples](advanced-examples.md) - Real-world Laravel Cloud API examples

## Quick Example

```php
// tests/Factories/DeploymentFactory.php
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
        ];
    }

    public function succeeded(): static
    {
        return $this->state(['status' => 'deployment.succeeded']);
    }
}

// tests/Feature/DeploymentTest.php
it('handles successful deployments', function () {
    $deployment = Deployment::factory()->succeeded()->make();

    expect($deployment->status)->toBe('deployment.succeeded');
});
```

## Support

- [GitHub Issues](https://github.com/fbarrento/data-factory/issues)
- [Packagist](https://packagist.org/packages/fbarrento/data-factory)
