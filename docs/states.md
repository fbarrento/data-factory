# States

States allow you to define reusable variations of your factory. They're perfect for creating different configurations of the same type of object.

## Defining Custom State Methods

Create dedicated methods for each state to provide a clean, expressive API:

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
            'php_major_version' => '8.3',
            'node_version' => '20',
            'uses_octane' => false,
            'uses_hibernation' => false,
        ];
    }

    public function production(): static
    {
        return $this->state([
            'name' => 'production',
            'slug' => 'production',
            'status' => 'running',
            'php_major_version' => '8.4',
            'node_version' => '22',
            'uses_octane' => true,
        ]);
    }

    public function staging(): static
    {
        return $this->state([
            'name' => 'staging',
            'slug' => 'staging',
            'status' => 'running',
            'php_major_version' => '8.4',
        ]);
    }

    public function hibernating(): static
    {
        return $this->state([
            'status' => 'hibernating',
            'uses_hibernation' => true,
        ]);
    }
}
```

Now you can use these methods for cleaner code:

```php
// Create a production environment
$production = EnvironmentFactory::new()->production()->make();

// Create a staging environment
$staging = EnvironmentFactory::new()->staging()->make();

// Create a hibernating environment
$hibernating = EnvironmentFactory::new()->hibernating()->make();
```

## Chaining States

You can chain multiple state methods together. Later states override earlier ones:

```php
$env = EnvironmentFactory::new()
    ->production()           // Sets production defaults
    ->hibernating()          // Overrides status to 'hibernating'
    ->make();

// Result: production environment, but hibernating
// status = 'hibernating', php_major_version = '8.4', uses_hibernation = true
```

## Overriding State Attributes

You can override any attribute when calling `make()`:

```php
$env = EnvironmentFactory::new()
    ->production()
    ->make([
        'name' => 'production-eu',  // Override the name
    ]);

// Result: production environment named 'production-eu'
```

## Attribute Merging Order

Attributes are merged in this order (later overrides earlier):

1. `definition()` - Base attributes from the factory
2. State methods - Attributes set by state methods like `->succeeded()`
3. `make()` attributes - Attributes passed directly to `make()`

```php
class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'status' => 'pending',      // 1. Base
            'branch_name' => 'main',
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'status' => 'deployment.succeeded',  // 2. Overrides base
        ]);
    }
}

$deployment = DeploymentFactory::new()
    ->succeeded()
    ->make([
        'status' => 'deployment.failed',  // 3. Final override
    ]);

// Result: status = 'deployment.failed'
```

## Using Faker in State Methods

Use Faker in your state methods to generate realistic, dynamic data:

```php
public function withRecentDeployment(): static
{
    return $this->state([
        'status' => 'running',
        'last_deployed_at' => $this->fake->dateTimeBetween('-1 day', 'now'),
    ]);
}

public function legacy(): static
{
    return $this->state([
        'created_at' => $this->fake->dateTimeBetween('-2 years', '-1 year'),
        'php_major_version' => '8.2',  // Older version
    ]);
}
```

## Real-World Example: Deployment States

Here's a complete example showing multiple state methods for different deployment scenarios:

```php
<?php

class DeploymentFactory extends Factory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'status' => 'pending',
            'branch_name' => 'main',
            'commit_hash' => $this->fake->sha1(),
            'commit_message' => $this->fake->sentence(),
            'started_at' => null,
            'finished_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'pending',
            'started_at' => null,
            'finished_at' => null,
        ]);
    }

    public function running(): static
    {
        return $this->state([
            'status' => 'deployment.running',
            'started_at' => $this->fake->dateTimeBetween('-10 minutes', 'now'),
        ]);
    }

    public function succeeded(): static
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => 'deployment.succeeded',
            'started_at' => $startedAt,
            'finished_at' => $this->fake->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function failed(): static
    {
        $startedAt = $this->fake->dateTimeBetween('-1 hour', '-30 minutes');

        return $this->state([
            'status' => 'deployment.failed',
            'failure_reason' => $this->fake->sentence(),
            'started_at' => $startedAt,
            'finished_at' => $this->fake->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function feature(): static
    {
        return $this->state([
            'branch_name' => 'feature/' . $this->fake->word(),
        ]);
    }
}

// Usage examples
$pendingDeploy = DeploymentFactory::new()->pending()->make();
$runningDeploy = DeploymentFactory::new()->running()->make();
$successDeploy = DeploymentFactory::new()->succeeded()->make();
$failedDeploy = DeploymentFactory::new()->failed()->make();
$featureSuccess = DeploymentFactory::new()->feature()->succeeded()->make();
```

## Next Steps

- [Sequences](sequences.md) - Cycle through values for multiple instances
- [Nested Factories](nested-factories.md) - Create complex object graphs
- [Advanced Examples](advanced-examples.md) - Real-world examples
