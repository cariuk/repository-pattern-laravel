# Repository Command & Stub Improvements

## Overview

The `make:repository` command generates modern repository classes following PHP 8.1+ standards with full type safety and comprehensive configuration options. Compatible with **Laravel 10.x, 11.x, and 12.x**.

---

## What's New in the Repository Stub

### Modern Repository Class Structure

```php
class UserRepository extends Repository
{
    /**
     * Model class name
     *
     * @var string
     */
    protected string $model = User::class;

    /**
     * Fillable fields for mass assignment
     *
     * @var array<string>
     */
    protected array $fillable = [];

    /**
     * Enable/disable pagination
     *
     * @var bool
     */
    protected bool $paginationable = true;

    /**
     * Allow pagination to be optional via request
     *
     * @var bool
     */
    protected bool $optionalPagination = false;

    /**
     * Number of items per page
     *
     * @var int
     */
    protected int $paginatePerPage = 10;

    /**
     * Enable/disable sorting
     *
     * @var bool
     */
    protected bool $sortable = true;

    /**
     * Fields allowed for sorting
     *
     * @var array<string>
     */
    protected array $sortAllowedFields = ['id'];

    /**
     * Default sort field
     *
     * @var string|null
     */
    protected ?string $defaultSortField = null;

    /**
     * Default sort direction (descending)
     *
     * @var bool
     */
    protected bool $defaultSortDescending = false;

    /**
     * Enable/disable eager loading
     *
     * @var bool
     */
    protected bool $relationable = false;

    /**
     * Relationships allowed to be loaded
     *
     * @var array<string>
     */
    protected array $relationAllowed = [];

    /**
     * Apply custom filters to the query
     *
     * Override this method to add your custom filters
     *
     * @param Request $request
     * @param Builder $builder
     * @return void
     */
    protected function applyFilter(Request $request, Builder &$builder): void
    {
        // Example: Search filter
        // if ($request->filled('search')) {
        //     $builder->where('name', 'like', '%' . $request->search . '%');
        // }

        // Example: Status filter
        // if ($request->filled('status')) {
        //     $builder->where('status', $request->status);
        // }
    }
}
```

**Key Features:**
- ✅ Full type safety with PHP 8.1+ syntax
- ✅ No constructor needed for simple repositories
- ✅ Complete property documentation with PHPDoc
- ✅ Built-in `applyFilter()` method template
- ✅ Auto-imports for common classes
- ✅ Compatible with Laravel 10.x, 11.x, and 12.x

---

## Command Usage

### Basic Repository

```bash
php artisan make:repository UserRepository User
```

**Generated File:** `app/Repositories/UserRepository.php`

```php
<?php

namespace App\Repositories;

use App\Models\User;
use RepositoryPatternLaravel\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserRepository extends Repository
{
    protected string $model = User::class;
    protected array $fillable = [];
    // ... all properties with defaults

    protected function applyFilter(Request $request, Builder &$builder): void
    {
        // Add your filters here
    }
}
```

### With Soft Deletes

```bash
php artisan make:repository PostRepository Post
```

If `Post` model uses `SoftDeletes`, it will automatically extend `RepositorySoftDelete`:

```php
<?php

namespace App\Repositories;

use App\Models\Post;
use RepositoryPatternLaravel\RepositorySoftDelete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostRepository extends RepositorySoftDelete
{
    protected string $model = Post::class;
    // ... includes soft delete methods (forceDelete, restore, getTrashList)
}
```

---

## Customization Guide

### 1. Configure Fillable Fields

```php
protected array $fillable = [
    'name',
    'email',
    'password',
    'phone',
];
```

### 2. Configure Pagination

```php
protected bool $paginationable = true;
protected int $paginatePerPage = 20; // Items per page
protected bool $optionalPagination = true; // Allow ?paginate=false
```

### 3. Configure Sorting

```php
protected bool $sortable = true;
protected array $sortAllowedFields = ['id', 'name', 'created_at'];
protected ?string $defaultSortField = 'created_at';
protected bool $defaultSortDescending = true;
```

### 4. Configure Relationships

```php
protected bool $relationable = true;
protected array $relationAllowed = ['posts', 'profile', 'roles'];
```

### 5. Add Custom Filters

```php
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
```

---

## Migration Guide for Existing Repositories

If you have existing repositories generated with the old stub, you can update them:

### Old Style (Still Works - Backward Compatible)

```php
class UserRepository extends Repository
{
    protected $fillable = [];

    public function __construct()
    {
        $this->model = User::class;
    }
}
```

### New Style (Recommended)

```php
class UserRepository extends Repository
{
    protected string $model = User::class;
    protected array $fillable = [];

    // No constructor needed - parent constructor handles DI

    protected function applyFilter(Request $request, Builder &$builder): void
    {
        // Your filters
    }
}
```

### With Dependency Injection (Advanced)

```php
use RepositoryPatternLaravel\Contracts\TransactionManagerInterface;
use RepositoryPatternLaravel\Contracts\ExceptionFactoryInterface;

class UserRepository extends Repository
{
    protected string $model = User::class;

    public function __construct(
        ?TransactionManagerInterface $transactionManager = null,
        ?ExceptionFactoryInterface $exceptionFactory = null
    ) {
        parent::__construct($transactionManager, $exceptionFactory);
    }
}
```

---

## Command Reference

### Signature
```bash
php artisan make:repository {name} {model}
```

**Arguments:**
- `{name}` - Repository class name (without "Repository" suffix)
- `{model}` - Model class name

**Examples:**
```bash
# Basic repository
php artisan make:repository User User

# Nested repository
php artisan make:repository Admin.User User

# Auto-detects SoftDeletes
php artisan make:repository Product Product
```

**Output Location:** `app/Repositories/{Name}Repository.php`

---

## Using in Controllers

```php
use App\Repositories\UserRepository;

class UserController extends Controller
{
    public function __construct(protected UserRepository $repository) {}

    public function index(Request $request)
    {
        return $this->repository->getList($request);
    }
}
```

**API Example:**
```
GET /users?page=2&limit=20&sort=name&descending=true&search=john&with=posts
```

---

## Benefits

| Feature | Description |
|---------|-------------|
| **Type Safety** | Full PHP 8.1+ type hints prevent runtime errors |
| **IDE Support** | Complete autocomplete and refactoring tools |
| **Self-Documenting** | All options visible with comprehensive PHPDoc |
| **Less Boilerplate** | No constructor needed for simple cases |
| **Modern PHP** | Uses latest PHP 8.1+ features and best practices |

---

## Troubleshooting

### Issue: "Class 'App\Models\User' not found"

**Solution:** Make sure the model exists:
```bash
php artisan make:model User
```

### Issue: Generated repository shows type errors

**Solution:**
1. Clear cache: `composer dump-autoload`
2. Restart IDE
3. Check PHP version (requires 8.1+)

### Issue: Old constructor style still generated

**Solution:**
1. Package may be cached
2. Run `composer update cariuk/laravel-repository-pattern`
3. Clear artisan cache: `php artisan clear-compiled`

---

## Laravel Version Compatibility

| Laravel Version | PHP Version | Status |
|----------------|-------------|--------|
| Laravel 12.x | PHP 8.2+ | ✅ Fully Supported |
| Laravel 11.x | PHP 8.2+ | ✅ Fully Supported |
| Laravel 10.x | PHP 8.1+ | ✅ Fully Supported |

---

## Summary

The `make:repository` command generates production-ready repository classes with:
- ✅ Modern PHP 8.1+ syntax with full type safety
- ✅ Complete configuration options out of the box
- ✅ Built-in filter method template with examples
- ✅ Automatic soft delete detection
- ✅ 100% backward compatible with older code
- ✅ Support for Laravel 10.x, 11.x, and 12.x

All repositories are generated following best practices and SOLID principles by default.