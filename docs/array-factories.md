# Array Factories

While the default `Factory` class creates objects, `ArrayFactory` creates associative arrays. This is perfect for:

- JSON API responses
- Database seeding
- Array-based data structures
- Testing API endpoints

## Creating an Array Factory

Extend `ArrayFactory` instead of `Factory`. This is especially useful for JSON:API resource formats:

```php
<?php

use FBarrento\DataFactory\ArrayFactory;

class DeploymentResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'deployments',
            'attributes' => [
                'status' => 'pending',
                'branch_name' => 'main',
                'commit_hash' => $this->fake->sha1(),
                'started_at' => null,
                'finished_at' => null,
            ],
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'attributes' => [
                'status' => 'deployment.succeeded',
                'started_at' => $this->fake->iso8601(),
                'finished_at' => $this->fake->iso8601(),
            ],
        ]);
    }
}

$deployment = DeploymentResourceFactory::new()->succeeded()->make();

// Result is an array:
// [
//     'id' => '123e4567-e89b-12d3-a456-426614174000',
//     'type' => 'deployments',
//     'attributes' => [
//         'status' => 'deployment.succeeded',
//         ...
//     ]
// ]
```

## The `new()` Static Method

`ArrayFactory` includes a static `new()` method for clean chaining:

```php
// With new() - clean and chainable
$deployments = DeploymentArrayFactory::new()
    ->count(5)
    ->make();

// Without new() - also works
$factory = new DeploymentArrayFactory();
$deployments = $factory->count(5)->make();
```

## JSON:API Resource Format

Array factories are perfect for JSON:API format responses:

```php
class ApplicationResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'applications',
            'attributes' => [
                'name' => $this->fake->company(),
                'slug' => $this->fake->slug(),
                'region' => 'us-east-2',
                'created_at' => $this->fake->iso8601(),
            ],
            'relationships' => [
                'repository' => [
                    'data' => [
                        'type' => 'repositories',
                        'id' => $this->fake->uuid(),
                    ],
                ],
                'organization' => [
                    'data' => [
                        'type' => 'organizations',
                        'id' => $this->fake->uuid(),
                    ],
                ],
            ],
        ];
    }
}
```

## All Factory Features Work

Array factories support all the same features as object factories:

### States

State methods work the same way with array factories:

```php
class DeploymentResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'deployments',
            'attributes' => [
                'status' => 'pending',
                'started_at' => null,
                'finished_at' => null,
            ],
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'attributes' => [
                'status' => 'deployment.succeeded',
                'started_at' => $this->fake->iso8601(),
                'finished_at' => $this->fake->iso8601(),
            ],
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'attributes' => [
                'status' => 'deployment.failed',
                'failure_reason' => $this->fake->sentence(),
                'started_at' => $this->fake->iso8601(),
                'finished_at' => $this->fake->iso8601(),
            ],
        ]);
    }
}

$succeeded = DeploymentResourceFactory::new()->succeeded()->make();
$failed = DeploymentResourceFactory::new()->failed()->make();
```

### Sequences

```php
$deployments = DeploymentResourceFactory::new()
    ->count(4)
    ->sequence(
        ['attributes' => ['status' => 'pending']],
        ['attributes' => ['status' => 'running']],
        ['attributes' => ['status' => 'succeeded']],
        ['attributes' => ['status' => 'failed']]
    )
    ->make();
```

### Count

```php
$deployments = DeploymentResourceFactory::new()->count(10)->make();
// Returns array of 10 deployment resource arrays
```

## Nested Array Factories

Create complex nested structures by combining array factories:

```php
class RepositoryArrayFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'repositories',
            'attributes' => [
                'name' => $this->fake->slug(),
                'full_name' => $this->fake->userName() . '/' . $this->fake->slug(),
            ],
        ];
    }
}

class ApplicationArrayFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'applications',
            'attributes' => [
                'name' => $this->fake->company(),
            ],
            'relationships' => [
                'repository' => [
                    'data' => fn () => RepositoryArrayFactory::new()->make(),
                ],
            ],
        ];
    }
}
```

## JSON Encoding

Array factories make it easy to generate JSON responses:

```php
$deployment = DeploymentResourceFactory::new()->succeeded()->make();
$json = json_encode($deployment, JSON_PRETTY_PRINT);

echo $json;
// {
//     "id": "123e4567-e89b-12d3-a456-426614174000",
//     "type": "deployments",
//     "attributes": {
//         "status": "deployment.succeeded",
//         "branch_name": "main",
//         ...
//     }
// }
```

## Pagination Response Factory

Create complete paginated JSON:API responses:

```php
class DeploymentCollectionFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'data' => fn () => DeploymentResourceFactory::new()->count(15)->make(),
            'links' => [
                'first' => 'https://api.example.com/deployments?page=1',
                'last' => 'https://api.example.com/deployments?page=5',
                'prev' => null,
                'next' => 'https://api.example.com/deployments?page=2',
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
}

$response = DeploymentCollectionFactory::new()->make();
```

## Testing API Responses

Perfect for mocking API responses in tests:

```php
// Mock a Laravel Cloud API response
$mockResponse = ApplicationResourceFactory::new()->make([
    'attributes' => [
        'name' => 'Test Application',
        'region' => 'us-west-2',
    ],
]);

// Use in HTTP tests
$client->get('/applications/123')
    ->assertJson($mockResponse);
```

## Real-World Example: Complete JSON:API Response

```php
class EnvironmentResourceFactory extends ArrayFactory
{
    protected function definition(): array
    {
        return [
            'id' => $this->fake->uuid(),
            'type' => 'environments',
            'attributes' => [
                'name' => $this->fake->word(),
                'slug' => $this->fake->slug(),
                'status' => 'running',
                'php_major_version' => '8.4',
                'uses_octane' => true,
                'created_at' => $this->fake->iso8601(),
            ],
            'relationships' => [
                'application' => [
                    'data' => [
                        'type' => 'applications',
                        'id' => $this->fake->uuid(),
                    ],
                ],
                'currentDeployment' => [
                    'data' => [
                        'type' => 'deployments',
                        'id' => $this->fake->uuid(),
                    ],
                ],
            ],
            'links' => [
                'self' => [
                    'href' => 'https://api.laravel.cloud/environments/' . $this->fake->uuid(),
                ],
            ],
        ];
    }

    public function production(): static
    {
        return $this->state([
            'attributes' => [
                'name' => 'production',
                'slug' => 'production',
                'php_major_version' => '8.4',
                'uses_octane' => true,
            ],
        ]);
    }
}

// Create production environment response
$environment = EnvironmentResourceFactory::new()->production()->make();

// Create multiple environments
$environments = EnvironmentResourceFactory::new()->count(3)->make();

// With pagination wrapper
$response = [
    'data' => EnvironmentResourceFactory::new()->count(10)->make(),
    'meta' => [
        'total' => 10,
        'per_page' => 15,
    ],
];
```

## When to Use Array Factories

Use `ArrayFactory` when:
- Testing JSON API endpoints
- Mocking external API responses
- Seeding databases with array data
- Working with array-based data structures
- You don't need object instances

Use regular `Factory` when:
- Creating domain objects
- Working with typed classes
- You need object methods and type safety

## Next Steps

- [Model Integration](model-integration.md) - Attach factories to models
- [Advanced Examples](advanced-examples.md) - Complete Laravel Cloud API examples
- [Testing](testing.md) - Use array factories in your tests
