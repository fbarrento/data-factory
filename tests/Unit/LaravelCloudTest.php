<?php

declare(strict_types=1);

use Tests\Examples\LaravelCloud\Application;
use Tests\Examples\LaravelCloud\Deployment;
use Tests\Examples\LaravelCloud\DeploymentStatus;
use Tests\Examples\LaravelCloud\Environment;
use Tests\Examples\LaravelCloud\Organization;
use Tests\Examples\LaravelCloud\Repository;

// Organization Tests
test('creates organization', function (): void {
    /** @var Organization $org */
    $org = Organization::factory()->make();

    expect($org)->toBeInstanceOf(Organization::class)
        ->and($org->id)->toBeString()
        ->and($org->name)->toBeString();
});

test('creates multiple organizations', function (): void {
    /** @var Organization[] $orgs */
    $orgs = Organization::factory()->count(3)->make();

    expect($orgs)->toHaveCount(3);

    foreach ($orgs as $org) {
        expect($org)->toBeInstanceOf(Organization::class);
    }
});

// Repository Tests
test('creates repository', function (): void {
    /** @var Repository $repo */
    $repo = Repository::factory()->make();

    expect($repo)->toBeInstanceOf(Repository::class)
        ->and($repo->id)->toBeString()
        ->and($repo->name)->toBeString()
        ->and($repo->fullName)->toContain('/')
        ->and($repo->defaultBranch)->toBe('main');
});

test('repository legacy state uses master branch', function (): void {
    /** @var Repository $repo */
    $repo = Repository::factory()->legacy()->make();

    expect($repo->defaultBranch)->toBe('master');
});

test('repository full name format is correct', function (): void {
    /** @var Repository $repo */
    $repo = Repository::factory()->make();

    [$username, $repoName] = explode('/', $repo->fullName);

    expect($username)->toBeString()
        ->and($repoName)->toBe($repo->name);
});

// Deployment Tests
test('creates deployment with random status', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->make();

    expect($deployment)->toBeInstanceOf(Deployment::class)
        ->and($deployment->status)->toBeInstanceOf(DeploymentStatus::class)
        ->and($deployment->branchName)->toBe('main')
        ->and($deployment->commitHash)->toBeString()
        ->and($deployment->commitMessage)->toBeString()
        ->and($deployment->phpMajorVersion)->toBe('8.4')
        ->and($deployment->usesOctane)->toBeFalse();
});

test('deployment pending state', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->pending()->make();

    expect($deployment->status)->toBe(DeploymentStatus::Pending)
        ->and($deployment->status->value)->toBe('pending');
});

test('deployment running state has started timestamp', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->running()->make();

    expect($deployment->status)->toBe(DeploymentStatus::Running)
        ->and($deployment->status->value)->toBe('deployment.running')
        ->and($deployment->startedAt)->toBeInstanceOf(\DateTimeInterface::class)
        ->and($deployment->finishedAt)->toBeNull();
});

test('deployment succeeded state has timestamps', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->succeeded()->make();

    expect($deployment->status)->toBe(DeploymentStatus::Succeeded)
        ->and($deployment->status->value)->toBe('deployment.succeeded')
        ->and($deployment->startedAt)->toBeInstanceOf(\DateTimeInterface::class)
        ->and($deployment->finishedAt)->toBeInstanceOf(\DateTimeInterface::class)
        ->and($deployment->failureReason)->toBeNull();
});

test('deployment failed state has failure reason', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->failed()->make();

    expect($deployment->status)->toBe(DeploymentStatus::Failed)
        ->and($deployment->status->value)->toBe('deployment.failed')
        ->and($deployment->failureReason)->toBeString()
        ->and($deployment->startedAt)->toBeInstanceOf(\DateTimeInterface::class)
        ->and($deployment->finishedAt)->toBeInstanceOf(\DateTimeInterface::class);
});

test('deployment with octane state', function (): void {
    /** @var Deployment $deployment */
    $deployment = Deployment::factory()->withOctane()->make();

    expect($deployment->usesOctane)->toBeTrue();
});

test('deployment sequence with multiple statuses', function (): void {
    /** @var Deployment[] $deployments */
    $deployments = Deployment::factory()
        ->count(4)
        ->sequence(
            ['status' => DeploymentStatus::Succeeded],
            ['status' => DeploymentStatus::Failed]
        )
        ->make();

    expect($deployments[0]->status)->toBe(DeploymentStatus::Succeeded)
        ->and($deployments[1]->status)->toBe(DeploymentStatus::Failed)
        ->and($deployments[2]->status)->toBe(DeploymentStatus::Succeeded)
        ->and($deployments[3]->status)->toBe(DeploymentStatus::Failed);
});

// Environment Tests
test('creates environment', function (): void {
    /** @var Environment $env */
    $env = Environment::factory()->make();

    expect($env)->toBeInstanceOf(Environment::class)
        ->and($env->id)->toBeString()
        ->and($env->name)->toBeString()
        ->and($env->slug)->toBeString()
        ->and($env->status)->toBe('stopped')
        ->and($env->vanityDomain)->toBeString()
        ->and($env->phpMajorVersion)->toBe('8.3')
        ->and($env->nodeVersion)->toBe(20)
        ->and($env->usesOctane)->toBeFalse()
        ->and($env->usesHibernation)->toBeFalse()
        ->and($env->currentDeployment)->toBeNull()
        ->and($env->createdAt)->toBeInstanceOf(\DateTimeInterface::class);
});

test('environment production state', function (): void {
    /** @var Environment $env */
    $env = Environment::factory()->production()->make();

    expect($env->name)->toBe('production')
        ->and($env->slug)->toBe('production')
        ->and($env->status)->toBe('running')
        ->and($env->phpMajorVersion)->toBe('8.4')
        ->and($env->nodeVersion)->toBe(22)
        ->and($env->usesOctane)->toBeTrue()
        ->and($env->currentDeployment)->toBeInstanceOf(Deployment::class);

    if ($env->currentDeployment !== null) {
        expect($env->currentDeployment->status)->toBe(DeploymentStatus::Succeeded);
    }
});

test('environment staging state', function (): void {
    /** @var Environment $env */
    $env = Environment::factory()->staging()->make();

    expect($env->name)->toBe('staging')
        ->and($env->slug)->toBe('staging')
        ->and($env->status)->toBe('running')
        ->and($env->currentDeployment)->toBeInstanceOf(Deployment::class);
});

test('environment preview state', function (): void {
    /** @var Environment $env */
    $env = Environment::factory()->preview()->make();

    expect($env->name)->toEndWith('-preview')
        ->and($env->slug)->toEndWith('-preview')
        ->and($env->status)->toBe('hibernating')
        ->and($env->usesHibernation)->toBeTrue();
});

test('environment with deployment method', function (): void {
    /** @var Environment $env */
    $env = Environment::factory()->withDeployment('failed')->make();

    expect($env->status)->toBe('running')
        ->and($env->currentDeployment)->toBeInstanceOf(Deployment::class);

    if ($env->currentDeployment !== null) {
        expect($env->currentDeployment->status)->toBe(DeploymentStatus::Failed);
    }
});

// Application Tests
test('creates basic application', function (): void {
    /** @var Application $app */
    $app = Application::factory()->make();

    expect($app)->toBeInstanceOf(Application::class)
        ->and($app->id)->toBeString()
        ->and($app->name)->toEndWith(' App')
        ->and($app->slug)->toBeString()
        ->and($app->region)->toBeIn(['us-east-1', 'us-east-2', 'us-west-2', 'eu-west-1'])
        ->and($app->repository)->toBeInstanceOf(Repository::class)
        ->and($app->organization)->toBeInstanceOf(Organization::class)
        ->and($app->defaultEnvironment)->toBeNull()
        ->and($app->environments)->toBeEmpty()
        ->and($app->deployments)->toBeEmpty()
        ->and($app->createdAt)->toBeInstanceOf(\DateTimeInterface::class);
});

test('application with environments', function (): void {
    /** @var Application $app */
    $app = Application::factory()->withEnvironments()->make();

    expect($app->defaultEnvironment)->toBeInstanceOf(Environment::class)
        ->and($app->environments)->toHaveCount(3)
        ->and($app->environments[0])->toBeInstanceOf(Environment::class)
        ->and($app->environments[0]->name)->toBe('production')
        ->and($app->environments[1]->name)->toBe('staging')
        ->and($app->environments[2]->name)->toEndWith('-preview');

    if ($app->defaultEnvironment !== null) {
        expect($app->defaultEnvironment->name)->toBe('production');
    }
});

test('application with deployments', function (): void {
    /** @var Application $app */
    $app = Application::factory()->withDeployments(8)->make();

    expect($app->deployments)->toHaveCount(8);

    foreach ($app->deployments as $deployment) {
        expect($deployment)->toBeInstanceOf(Deployment::class);
    }
});

test('application complete has everything', function (): void {
    /** @var Application $app */
    $app = Application::factory()->complete()->make();

    expect($app->defaultEnvironment)->toBeInstanceOf(Environment::class)
        ->and($app->environments)->toHaveCount(3)
        ->and($app->deployments)->toHaveCount(10)
        ->and($app->repository)->toBeInstanceOf(Repository::class)
        ->and($app->organization)->toBeInstanceOf(Organization::class);
});

test('multiple complete applications', function (): void {
    /** @var Application[] $apps */
    $apps = Application::factory()->complete()->count(3)->make();

    expect($apps)->toHaveCount(3);

    foreach ($apps as $app) {
        expect($app->environments)->toHaveCount(3)
            ->and($app->deployments)->toHaveCount(10);
    }
});

test('application with custom attributes', function (): void {
    /** @var Application $app */
    $app = Application::factory()
        ->withEnvironments()
        ->make([
            'name' => 'My Custom App',
            'region' => 'eu-west-1',
        ]);

    expect($app->name)->toBe('My Custom App')
        ->and($app->region)->toBe('eu-west-1')
        ->and($app->environments)->toHaveCount(3);
});

test('application with legacy repository', function (): void {
    /** @var Application $app */
    $app = Application::factory()
        ->make([
            'repository' => Repository::factory()->legacy()->make(),
        ]);

    expect($app->repository->defaultBranch)->toBe('master');
});

// Integration Tests
test('deployment timeline simulation', function (): void {
    /** @var Deployment[] $deployments */
    $deployments = Deployment::factory()
        ->count(12)
        ->sequence(
            ['status' => DeploymentStatus::Succeeded],
            ['status' => DeploymentStatus::Succeeded],
            ['status' => DeploymentStatus::Succeeded],
            ['status' => DeploymentStatus::Failed]
        )
        ->make();

    $succeeded = array_filter($deployments, fn (\Tests\Examples\LaravelCloud\Deployment $d): bool => $d->status === DeploymentStatus::Succeeded);
    $failed = array_filter($deployments, fn (\Tests\Examples\LaravelCloud\Deployment $d): bool => $d->status === DeploymentStatus::Failed);

    expect($succeeded)->toHaveCount(9)
        ->and($failed)->toHaveCount(3);
});

test('multi-environment setup', function (): void {
    /** @var Environment[] $envs */
    $envs = Environment::factory()
        ->count(4)
        ->sequence(
            ['name' => 'production', 'status' => 'running', 'usesOctane' => true],
            ['name' => 'staging', 'status' => 'running', 'usesOctane' => true],
            ['name' => 'development', 'status' => 'running', 'usesOctane' => false],
            ['name' => 'preview', 'status' => 'hibernating', 'usesHibernation' => true]
        )
        ->make();

    expect($envs[0]->name)->toBe('production')
        ->and($envs[0]->usesOctane)->toBeTrue()
        ->and($envs[1]->name)->toBe('staging')
        ->and($envs[2]->name)->toBe('development')
        ->and($envs[3]->name)->toBe('preview')
        ->and($envs[3]->usesHibernation)->toBeTrue();
});
