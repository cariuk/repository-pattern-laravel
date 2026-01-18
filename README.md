# Laravel Repository Pattern

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.0%2B-red.svg)](https://laravel.com/)

A flexible and powerful Laravel package that implements the Repository Pattern with built-in support for CRUD operations, filtering, sorting, pagination, eager loading, and soft deletes.

## Features

- **Clean Architecture**: Separation of concerns with trait-based composition
- **Automatic CRUD**: Pre-built create, read, update, and delete operations
- **Smart Filtering**: Request-driven filtering with custom filter hooks
- **Sorting & Pagination**: Built-in support for sorting and pagination
- **Eager Loading**: Control relationship loading via request parameters
- **Soft Delete Support**: Full support for Laravel's soft deletes with trash management
- **Lifecycle Hooks**: Extensible hooks for custom logic (onCreated, onUpdated, onDeleted, etc.)
- **Transaction Safety**: Automatic database transaction wrapping for write operations
- **Artisan Command**: Generate repositories quickly with `make:repository` command

## Requirements

- PHP 8.1 or higher
- Laravel 12.0 or higher

## Installation

Install the package via Composer:

```bash
composer require cariuk/laravel-repository-pattern
```

The service provider will be automatically registered.

## Quick Start

### 1. Generate a Repository

Use the Artisan command to generate a repository for your model:

```bash
php artisan make:repository UserRepository User
```

This will create `app/Repositories/UserRepository.php`:

```php
<?php

namespace App\Repositories;

use App\Models\User;
use RepositoryPatternLaravel\Repository;

class UserRepository extends Repository
{
    protected $fillable = [];

    public function __construct()
    {
        $this->model = User::class;
    }
}
```

If your model uses `SoftDeletes`, the command will automatically extend `RepositorySoftDelete` instead.

### 2. Configure the Repository

```php
<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use RepositoryPatternLaravel\Repository;

class UserRepository extends Repository
{
    // Fillable fields for create/update operations
    protected $fillable = ['name', 'email', 'password'];

    // Enable pagination (default: true)
    protected $paginationable = true;

    // Items per page (default: 10)
    protected $paginatePerPage = 15;

    // Allow sorting (default: true)
    protected $sortable = true;

    // Allowed fields for sorting
    protected $sortAllowedFields = ['id', 'name', 'email', 'created_at'];

    // Default sort field
    protected $defaultSortField = 'created_at';

    // Default sort direction (default: false)
    protected $defaultSortDescending = true;

    // Enable eager loading (default: false)
    protected $relationable = true;

    // Allowed relationships to load
    protected $relationAllowed = ['posts', 'profile', 'roles'];

    public function __construct()
    {
        $this->model = User::class;
    }

    /**
     * Apply custom filters to the query
     */
    protected function applyFilter(Request $request, Builder &$builder): void
    {
        // Search by name or email
        if ($request->filled('search')) {
            $builder->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $builder->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $builder->whereHas('roles', function($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $builder->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $builder->where('created_at', '<=', $request->to_date);
        }
    }
}
```

### 3. Use in Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $users = $this->repository->getList($request);
        return response()->json($users);
    }

    /**
     * Display a specific user
     */
    public function show(Request $request, int $id)
    {
        $user = $this->repository->getDetail($request, $id);
        return response()->json($user);
    }

    /**
     * Create a new user
     */
    public function store(UserStoreRequest $request)
    {
        $user = $this->repository->create($request);
        return response()->json($user, 201);
    }

    /**
     * Update an existing user
     */
    public function update(UserUpdateRequest $request, int $id)
    {
        $user = $this->repository->update($request, $id);
        return response()->json($user);
    }

    /**
     * Delete a user
     */
    public function destroy(Request $request, int $id)
    {
        $this->repository->delete($request, $id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
```

## API Reference

### Available Methods

#### Reading Operations

```php
// Get paginated/collection list
$users = $repository->getList($request);

// Get single record by ID
$user = $repository->getDetail($request, $id);

// Get single record with custom query modifier
$user = $repository->getDetail($request, $id, function(Builder &$builder) {
    $builder->where('status', 'active');
});
```

#### Creation & Update Operations

```php
// Create a new record
$user = $repository->create($request);

// Create with array data
$user = $repository->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Update existing record
$user = $repository->update($request, $id);

// Update with custom query modifier
$user = $repository->update($request, $id, function(Builder &$builder) {
    $builder->where('company_id', auth()->user()->company_id);
});
```

#### Deletion Operations

```php
// Delete a record
$repository->delete($request, $id);

// Delete with custom query modifier
$repository->delete($request, $id, function(Builder &$builder) {
    $builder->where('created_by', auth()->id());
});
```

#### Soft Delete Operations (only for RepositorySoftDelete)

```php
// Get trashed records
$trashedUsers = $repository->getTrashList($request);

// Force delete permanently
$repository->forceDelete($request, $id);

// Restore soft-deleted record
$user = $repository->restore($request, $id);
```

### Request Parameters

Control repository behavior via URL query parameters:

```
# Pagination
GET /users?page=2&limit=20

# Sorting
GET /users?sort=name&descending=true

# Eager loading
GET /users?with=posts,profile

# Custom filters (defined in applyFilter method)
GET /users?search=john&status=active&role=admin

# Combine multiple parameters
GET /users?page=1&limit=15&sort=created_at&descending=true&with=posts&search=john
```

## Advanced Usage

### Lifecycle Hooks

Override these methods to add custom logic at different stages:

```php
class UserRepository extends Repository
{
    /**
     * Called after a record is created
     */
    protected function onCreated(FormRequest|array $request, Model $object): void
    {
        // Send welcome email
        Mail::to($object->email)->send(new WelcomeEmail($object));

        // Log activity
        activity()
            ->performedOn($object)
            ->log('User created');
    }

    /**
     * Called after a record is updated
     */
    protected function onUpdated(FormRequest $request, Model $object): void
    {
        // Invalidate cache
        Cache::forget("user.{$object->id}");
    }

    /**
     * Called after create or update
     */
    protected function onSaved(FormRequest|array $request, Model $object): void
    {
        // Sync relationships
        if (is_object($request) && $request->has('roles')) {
            $object->roles()->sync($request->roles);
        }
    }

    /**
     * Called after a record is deleted
     */
    protected function onDeleted(Request $request, Model $object): void
    {
        // Clean up related files
        Storage::delete($object->avatar);
    }

    /**
     * Called after a record is force deleted (soft delete only)
     */
    protected function onForceDeleted(Request $request, Model $object): void
    {
        // Permanent cleanup
        $object->posts()->forceDelete();
    }

    /**
     * Called after a record is restored (soft delete only)
     */
    protected function onRestored(Request $request, Model $object): void
    {
        // Restore related data
        $object->posts()->restore();
    }
}
```

### Custom Data Processing

```php
class UserRepository extends Repository
{
    /**
     * Modify data before saving
     */
    public function getDataSave(array $data, $action): array
    {
        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        // Add metadata
        if ($action === 'create') {
            $data['created_by'] = auth()->id();
        }

        if ($action === 'update') {
            $data['updated_by'] = auth()->id();
        }

        return $data;
    }

    /**
     * Different fillable fields for different operations
     */
    protected function getFillable($method = null)
    {
        if ($method === 'create') {
            return ['name', 'email', 'password', 'role_id'];
        }

        if ($method === 'update') {
            // Don't allow changing email on update
            return ['name', 'role_id'];
        }

        return $this->fillable;
    }
}
```

### Using Query Modifiers

Query modifiers allow you to add custom conditions to queries:

```php
// In your controller or service
public function getUsersByCompany(Request $request, int $companyId, int $userId)
{
    return $this->repository->getDetail($request, $userId, function(Builder &$builder) use ($companyId) {
        $builder->where('company_id', $companyId)
                ->where('status', 'active');
    });
}

// Skip default filters
public function getAdminUser(Request $request, int $id)
{
    return $this->repository->getDetail(
        $request,
        $id,
        skipDefaultFilter: true // Skip applyFilter method
    );
}
```

### Working with Relationships

```php
class PostRepository extends Repository
{
    protected $relationable = true;
    protected $relationAllowed = ['author', 'comments', 'tags'];

    public function __construct()
    {
        $this->model = Post::class;
    }

    /**
     * Load nested relationships
     */
    protected function applyFilter(Request $request, Builder &$builder): void
    {
        // This will be combined with ?with=author,comments
        // to load relationships efficiently
    }
}

// Usage in controller
GET /posts?with=author,comments.user,tags
```

### Disable Pagination

```php
class UserRepository extends Repository
{
    protected $paginationable = true;
    protected $optionalPagination = true; // Allow disabling pagination

    // ...
}

// Request without pagination
GET /users?paginate=false
```

## Configuration Options

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$fillable` | `array` | `[]` | Allowed fields for mass assignment |
| `$paginationable` | `bool` | `true` | Enable/disable pagination |
| `$optionalPagination` | `bool` | `false` | Allow pagination to be disabled via request |
| `$paginatePerPage` | `int` | `10` | Default number of items per page |
| `$sortable` | `bool` | `true` | Enable/disable sorting |
| `$sortAllowedFields` | `array` | `['id']` | Fields allowed for sorting |
| `$defaultSortField` | `string\|null` | `null` | Default field to sort by |
| `$defaultSortDescending` | `bool` | `false` | Default sort direction |
| `$relationable` | `bool` | `false` | Enable/disable eager loading |
| `$relationAllowed` | `array` | `[]` | Relationships allowed to be loaded |

## Best Practices

### 1. Keep Repositories Focused

Each repository should handle one model:

```php
// Good
class UserRepository extends Repository { }
class PostRepository extends Repository { }

// Avoid
class UserPostRepository extends Repository { }
```

### 2. Use Form Requests for Validation

```php
// Good
public function store(UserStoreRequest $request)
{
    return $this->repository->create($request);
}

// Avoid validating in repository
```

### 3. Implement Service Layer for Complex Logic

```php
class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository,
        protected NotificationService $notificationService
    ) {}

    public function createUserWithRole(array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->create($data);
            $role = $this->roleRepository->getDetail(request(), $data['role_id']);
            $user->roles()->attach($role);

            $this->notificationService->sendWelcomeEmail($user);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### 4. Use Type Hints

```php
// Good
public function getUserPosts(int $userId): Collection
{
    return $this->repository->getDetail(request(), $userId)->posts;
}

// Avoid
public function getUserPosts($userId)
{
    return $this->repository->getDetail(request(), $userId)->posts;
}
```

### 5. Cache Frequently Accessed Data

```php
class UserRepository extends Repository
{
    public function getDetail(Request $request, $id, \Closure $modifier = null, $skipDefaultFilter = false): Model
    {
        return Cache::remember("user.{$id}", 3600, function() use ($request, $id, $modifier, $skipDefaultFilter) {
            return parent::getDetail($request, $id, $modifier, $skipDefaultFilter);
        });
    }

    protected function onUpdated(FormRequest $request, Model $object): void
    {
        Cache::forget("user.{$object->id}");
    }
}
```

## Testing

```php
use Tests\TestCase;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_can_create_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_can_get_paginated_list()
    {
        User::factory()->count(25)->create();

        $request = request();
        $result = $this->repository->getList($request);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
    }

    public function test_can_filter_users()
    {
        User::factory()->create(['name' => 'John Doe', 'status' => 'active']);
        User::factory()->create(['name' => 'Jane Doe', 'status' => 'inactive']);

        $request = request()->merge(['status' => 'active']);
        $result = $this->repository->getList($request);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('active', $result->first()->status);
    }
}
```

## Troubleshooting

### Transaction Deadlocks

If you experience transaction deadlocks with nested repositories:

```php
// Instead of calling repositories inside hooks
protected function onCreated(FormRequest $request, Model $object): void
{
    // Avoid - may cause deadlock
    $this->roleRepository->create(['user_id' => $object->id]);
}

// Use events or jobs
protected function onCreated(FormRequest $request, Model $object): void
{
    // Better - dispatched after transaction commits
    CreateDefaultRole::dispatch($object);
}
```

### Memory Issues with Large Datasets

For large datasets, disable pagination carefully:

```php
// Use chunking instead
$this->repository->getBuilder()
    ->chunk(1000, function($users) {
        foreach ($users as $user) {
            // Process user
        }
    });
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

Developed and maintained by [Cariuk](https://github.com/cariuk)

## Support

If you discover any security vulnerabilities, please email us instead of using the issue tracker.

For general questions and issues, please use the [GitHub issue tracker](https://github.com/cariuk/repository-pattern-laravel/issues).