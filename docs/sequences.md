# Sequences

Sequences allow you to cycle through a set of values when creating multiple instances. This is essential for testing behavior across different variations.

💡 **Perfect for testing**: Sequences let you easily test how your code handles different states or values in a single test.

## Basic Sequences

Use the `sequence()` method with an array of values to cycle through:

```php
<?php

use FBarrento\DataFactory\Factory;

class DeploymentFactory extends Factory
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

$deployments = Deployment::factory()
    ->count(4)
    ->sequence(
        ['status' => 'pending'],
        ['status' => 'deployment.running'],
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.failed']
    )
    ->make();

// Results:
// $deployments[0]->status = 'pending'
// $deployments[1]->status = 'deployment.running'
// $deployments[2]->status = 'deployment.succeeded'
// $deployments[3]->status = 'deployment.failed'
```

## Sequence Wrapping

If you create more instances than sequence values, the sequence wraps around:

```php
$deployments = Deployment::factory()
    ->count(6)
    ->sequence(
        ['status' => 'pending'],
        ['status' => 'running'],
        ['status' => 'succeeded']
    )
    ->make();

// Results:
// $deployments[0]->status = 'pending'
// $deployments[1]->status = 'running'
// $deployments[2]->status = 'succeeded'
// $deployments[3]->status = 'pending'     // Wraps around
// $deployments[4]->status = 'running'     // Wraps around
// $deployments[5]->status = 'succeeded'   // Wraps around
```

## Closures with Index

Use a closure to access the sequence index and create dynamic values:

```php
use FBarrento\DataFactory\Sequence;

$deployments = Deployment::factory()
    ->count(5)
    ->sequence(fn (Sequence $sequence) => [
        'branch_name' => 'feature/branch-' . $sequence->index,
    ])
    ->make();

// Results:
// $deployments[0]->branch_name = 'feature/branch-0'
// $deployments[1]->branch_name = 'feature/branch-1'
// $deployments[2]->branch_name = 'feature/branch-2'
// $deployments[3]->branch_name = 'feature/branch-3'
// $deployments[4]->branch_name = 'feature/branch-4'
```

## Multiple Sequences

You can chain multiple sequences together. Each applies independently:

```php
$deployments = Deployment::factory()
    ->count(4)
    ->sequence(
        ['status' => 'succeeded'],
        ['status' => 'failed']
    )
    ->sequence(
        ['branch_name' => 'main'],
        ['branch_name' => 'develop']
    )
    ->make();

// Results:
// $deployments[0]: status = 'succeeded', branch_name = 'main'
// $deployments[1]: status = 'failed',    branch_name = 'develop'
// $deployments[2]: status = 'succeeded', branch_name = 'main'
// $deployments[3]: status = 'failed',    branch_name = 'develop'
```

## Combining Sequences with States

Sequences can be combined with state methods:

```php
class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'php_major_version' => '8.3',
        ];
    }

    public function production(): static
    {
        return $this->state([
            'name' => 'production',
            'status' => 'running',
        ]);
    }
}

$environments = Environment::factory()
    ->production()  // Apply production state first
    ->count(3)
    ->sequence(
        ['php_major_version' => '8.2'],
        ['php_major_version' => '8.3'],
        ['php_major_version' => '8.4']
    )
    ->make();

// All environments are production, but with different PHP versions
```

## Real-World Example: Deployment Timeline

Create a realistic deployment timeline with varying statuses and timestamps:

```php
use FBarrento\DataFactory\Sequence;

$deployments = Deployment::factory()
    ->count(10)
    ->sequence(fn (Sequence $seq) => [
        'branch_name' => $seq->index % 3 === 0 ? 'main' : "feature/task-{$seq->index}",
        'commit_message' => "Deployment #{$seq->index}: {$this->fake->sentence()}",
    ])
    ->sequence(
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.succeeded'],
        ['status' => 'deployment.failed']  // 1 in 4 fails
    )
    ->make();
```

## Advanced Pattern: Environment Progression

Create environments that simulate a progressive deployment strategy:

```php
class EnvironmentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'name' => $this->fake->word(),
            'status' => 'stopped',
            'uses_octane' => false,
        ];
    }
}

$environments = Environment::factory()
    ->count(4)
    ->sequence(
        [
            'name' => 'development',
            'status' => 'running',
            'uses_octane' => false,
        ],
        [
            'name' => 'staging',
            'status' => 'running',
            'uses_octane' => true,
        ],
        [
            'name' => 'preview',
            'status' => 'hibernating',
            'uses_octane' => false,
        ],
        [
            'name' => 'production',
            'status' => 'running',
            'uses_octane' => true,
        ]
    )
    ->make();
```

## Using Faker in Sequences

Combine sequences with Faker for dynamic, varied data:

```php
use FBarrento\DataFactory\Sequence;

$applications = Application::factory()
    ->count(5)
    ->sequence(fn (Sequence $seq) => [
        'name' => $this->fake->company() . " App {$seq->index}",
        'region' => $this->fake->randomElement(['us-east-1', 'us-west-2', 'eu-west-1']),
    ])
    ->make();
```

## Sequence Reset

Sequences reset after each `make()` call:

```php
// First batch
$batch1 = Deployment::factory()
    ->count(2)
    ->sequence(['status' => 'pending'], ['status' => 'running'])
    ->make();
// $batch1[0]->status = 'pending', $batch1[1]->status = 'running'

// Second batch (sequence resets)
$batch2 = Deployment::factory()
    ->count(2)
    ->sequence(['status' => 'pending'], ['status' => 'running'])
    ->make();
// $batch2[0]->status = 'pending', $batch2[1]->status = 'running'
```

## Error Handling

Sequences must not be empty:

```php
// This will throw an exception
Deployment::factory()
    ->sequence()  // Empty sequence - throws InvalidArgumentException
    ->make();
```

## Next Steps

- [Nested Factories](nested-factories.md) - Create complex object graphs
- [Array Factories](array-factories.md) - Generate arrays for JSON responses
- [Advanced Examples](advanced-examples.md) - Complex real-world scenarios
