# Why Use Factories for Testing?

Test data factories solve common pain points when writing tests, making your test suite cleaner, more maintainable, and easier to understand.

Inspired by Laravel's Eloquent factories and built with PEST in mind, Data Factory brings the familiar factory pattern to any PHP project—without requiring Laravel or Eloquent.

**Works everywhere**: PHP 8.2+, fully tested on Windows, Linux, and macOS.

## The Problem

Without factories, test setup is repetitive, brittle, and clutters your test files:

```php
// ❌ tests/Feature/DeploymentTest.php - Without factories
it('processes successful deployment', function () {
    $deployment = new Deployment(
        id: '123e4567-e89b-12d3-a456-426614174000',
        status: 'deployment.succeeded',
        branchName: 'main',
        commitHash: 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0',
        commitMessage: 'Deploy feature X to production',
        failureReason: null,
        phpMajorVersion: '8.4',
        usesOctane: true,
        startedAt: new DateTime('2024-01-15 10:00:00'),
        finishedAt: new DateTime('2024-01-15 10:05:00')
    );

    // The actual test logic - buried in setup noise
    $result = $deployer->process($deployment);

    expect($result)->toBeTrue();
});

it('processes failed deployment', function () {
    // Copy-paste the same setup again with minor changes...
    $deployment = new Deployment(
        id: '223e4567-e89b-12d3-a456-426614174001',
        status: 'deployment.failed',
        branchName: 'main',
        commitHash: 'b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1',
        commitMessage: 'Deploy feature Y to production',
        failureReason: 'Health check failed',
        phpMajorVersion: '8.4',
        usesOctane: true,
        startedAt: new DateTime('2024-01-15 11:00:00'),
        finishedAt: new DateTime('2024-01-15 11:02:00')
    );

    $result = $deployer->process($deployment);

    expect($result)->toBeFalse();
});
```

**Problems:**
- **Repetition**: Each test duplicates 10+ lines of array setup code
- **Noise**: The interesting test logic is buried in array keys and values
- **Brittle**: Add one field to the deployment structure? Update 50+ tests
- **Hard to read**: What's actually being tested here?
- **Inconsistent**: Different tests might use slightly different test data

## The Solution

Factories provide reusable, expressive test data:

```php
// ✅ tests/Feature/DeploymentTest.php - With factories
it('processes successful deployment', function () {
    $deployment = Deployment::factory()->succeeded()->make();

    $result = $deployer->process($deployment);

    expect($result)->toBeTrue();
});

it('processes failed deployment', function () {
    $deployment = Deployment::factory()->failed()->make();

    $result = $deployer->process($deployment);

    expect($result)->toBeFalse();
});
```

**Benefits:**
- **One line of setup** instead of 10
- **Clear intent**: `->succeeded()` and `->failed()` are self-documenting
- **Focused tests**: The test logic stands out
- **Easy maintenance**: Change `Deployment` constructor once in the factory
- **Consistent data**: All tests use the same sensible defaults

## Common Testing Scenarios

### 1. Testing Different States

```php
// Without factories - repetitive
it('handles pending deployment', function () {
    $deployment = [
        'id' => '...',
        'status' => 'pending',
        'branch_name' => 'main',
        // ... 7 more fields
    ];
    // test logic
});

it('handles running deployment', function () {
    $deployment = [
        'id' => '...',
        'status' => 'running',
        'branch_name' => 'main',
        // ... 7 more fields
    ];
    // test logic
});

it('handles succeeded deployment', function () {
    $deployment = [
        'id' => '...',
        'status' => 'succeeded',
        'branch_name' => 'main',
        // ... 7 more fields
    ];
    // test logic
});

// With factories - expressive
it('handles pending deployment', function () {
    $deployment = Deployment::factory()->pending()->make();
    // test logic
});

it('handles running deployment', function () {
    $deployment = Deployment::factory()->running()->make();
    // test logic
});

it('handles succeeded deployment', function () {
    $deployment = Deployment::factory()->succeeded()->make();
    // test logic
});
```

### 2. Testing with Multiple Objects

```php
// Without factories - painful
it('processes batch of deployments', function () {
    $deployments = [
        ['id' => '...', 'status' => 'pending', /* 8 more fields */],
        ['id' => '...', 'status' => 'running', /* 8 more fields */],
        ['id' => '...', 'status' => 'succeeded', /* 8 more fields */],
        ['id' => '...', 'status' => 'failed', /* 8 more fields */],
        ['id' => '...', 'status' => 'succeeded', /* 8 more fields */],
    ];
    // test logic with 50+ lines of setup overhead
});

// With factories - simple
it('processes batch of deployments', function () {
    $deployments = Deployment::factory()->count(5)->make();
    // test logic
});
```

### 3. Testing Complex Nested Structures

```php
// Without factories - nightmare
it('creates application with full setup', function () {
    $application = [
        'id' => '...',
        'name' => 'My App',
        'repository' => [
            'id' => '...',
            'name' => 'my-repo',
            'full_name' => 'user/my-repo',
        ],
        'organization' => [
            'id' => '...',
            'name' => 'My Org',
        ],
        'environments' => [
            ['id' => '...', 'name' => 'production', /* 8 more fields */],
            ['id' => '...', 'name' => 'staging', /* 8 more fields */],
        ],
        'deployments' => [
            ['id' => '...', 'status' => 'succeeded', /* 8 more fields */],
            ['id' => '...', 'status' => 'succeeded', /* 8 more fields */],
            // ... 8 more deployments
        ],
    ];
    // test logic - 40+ lines later
});

// With factories - elegant
it('creates application with full setup', function () {
    $application = Application::factory()->complete()->make();
    // test logic - that's it!
});
```

### 4. Testing Edge Cases

```php
// Easy to test edge cases with custom attributes
it('handles deployment with very long commit message', function () {
    $deployment = Deployment::factory()->make([
        'commit_message' => str_repeat('a', 10000),
    ]);

    // test logic
});

it('handles deployment from feature branch', function () {
    $deployment = Deployment::factory()
        ->make(['branch_name' => 'feature/long-branch-name']);

    // test logic
});
```

## When to Use Factories vs Inline Data

### ✅ Use Factories When:

- Creating data structures with 3+ fields
- The same data type appears in multiple tests
- Testing different states or variations
- Creating complex nested structures
- You need realistic fake data (names, emails, UUIDs, etc.)

### ⚠️ Consider Inline Data When:

- The data is very simple (1-2 fields)
- The specific value is critical to the test
- It's used only once in the entire test suite

```php
// Inline is fine here - the specific value matters
it('validates minimum price', function () {
    $product = ['price' => 0.01];

    expect(validatePrice($product))->toBeTrue();
});

// But factories are better for most cases
it('calculates total price with tax', function () {
    $product = Product::factory()->make();

    expect(calculateTax($product))->toBeGreaterThan($product['price']);
});
```

## Real-World Impact

Here's what developers say after adopting factories:

**Before:**
- "I spend more time setting up test data than writing actual tests"
- "I avoid writing tests for complex scenarios because the setup is painful"
- "Every time we change a model, dozens of tests break"

**After:**
- "Tests are now quick to write and easy to read"
- "I actually enjoy testing complex scenarios now"
- "We can refactor with confidence - tests are resilient"

## The Factory Pattern Benefits

### DRY (Don't Repeat Yourself)
Define test data structures once, reuse everywhere. Change the definition in one place.

### Maintainability
When your `Deployment` class gains a new required parameter, update the factory definition—not 50 tests.

### Readability
`Deployment::factory()->succeeded()->make()` tells you exactly what kind of deployment you're testing.

### Flexibility
Easy to create variations:
- Default states
- Custom states with methods (`->pending()`, `->succeeded()`)
- Override any attribute with `make(['status' => 'custom'])`
- Use sequences for multiple variations

### Consistency
All tests use the same sensible defaults, making test failures easier to debug.

## Getting Started

Ready to clean up your test suite?

1. [Installation](installation.md) - Install Data Factory
2. [Basic Usage](basic-usage.md) - Create your first factory
3. [Testing Guide](testing.md) - Complete guide to using factories in PEST tests

## Next Steps

- [States](states.md) - Define reusable state variations for test scenarios
- [Sequences](sequences.md) - Test behavior across multiple variations
- [Nested Factories](nested-factories.md) - Create complex test object graphs
