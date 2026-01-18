# Command & Stub Improvements

## Overview

The `make:repository` command stub has been updated to follow modern PHP 8.1+ standards and align with the OOP refactoring.

---

## Changes Made to Repository Stub

### Before (Old Stub)

```php
class UserRepository extends Repository
{
    protected $fillable = [];
    protected $paginationable = false;
    protected $relation = null; // Unused property

    public function __construct()
    {
        $this->model = User::class;
    }
}
```

**Problems:**
- ❌ No type hints
- ❌ Constructor doesn't call parent (missing DI)
- ❌ Unused properties (`$relation`)
- ❌ Missing `$paginatePerPage` property
- ❌ Missing `$defaultSortDescending` property
- ❌ No `applyFilter()` method template
- ❌ Poor documentation

---

### After (New Stub)

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

**Improvements:**
- ✅ All properties have type hints
- ✅ Modern PHP 8.1+ syntax
- ✅ Property initialization inline (no constructor needed)
- ✅ Complete property set (all available options)
- ✅ `applyFilter()` method with examples
- ✅ Better documentation with PHPDoc
- ✅ Generic type annotations (`array<string>`)
- ✅ Removed unused properties
- ✅ Auto-imports for `Builder` and `Request`

---

## Usage Examples

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

## Benefits of New Stub

### 1. Better IDE Support
- Full autocomplete
- Type checking
- Refactoring tools work properly

### 2. Compile-Time Safety
- Type errors caught before runtime
- No more `$paginationable = 'true'` (string instead of bool)

### 3. Self-Documenting
- All available options visible
- Clear documentation for each property
- Examples included

### 4. Modern PHP
- Uses PHP 8.1+ features
- Property type declarations
- Inline initialization
- Nullable types

### 5. Less Boilerplate
- No constructor needed for simple cases
- Properties initialized inline
- Clean and concise

---

## Command Details

### Signature
```bash
php artisan make:repository {name} {model}
```

### Arguments
- `{name}` - Repository name (without "Repository" suffix)
- `{model}` - Model class name

### Examples
```bash
# Creates UserRepository
php artisan make:repository User User

# Creates PostRepository
php artisan make:repository Post Post

# Creates Admin/UserRepository (nested)
php artisan make:repository Admin.User User

# Auto-detects SoftDeletes
php artisan make:repository Product Product  # extends RepositorySoftDelete if SoftDeletes used
```

### Generated Location
```
app/
└── Repositories/
    ├── UserRepository.php
    ├── PostRepository.php
    └── Admin/
        └── UserRepository.php
```

---

## Testing Generated Repositories

After generating a repository, test it:

```php
use App\Repositories\UserRepository;

// In your controller
class UserController extends Controller
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    public function index(Request $request)
    {
        // Automatic pagination, sorting, filtering, and eager loading
        return $this->repository->getList($request);
    }
}
```

Test with URL parameters:
```
GET /users?page=2&limit=20&sort=name&descending=true&search=john&status=active&with=posts,profile
```

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

## Conclusion

The improved stub provides:
- ✅ Modern PHP 8.1+ syntax
- ✅ Full type safety
- ✅ Better documentation
- ✅ Complete feature set out of the box
- ✅ Examples for common patterns
- ✅ 100% backward compatible

All new repositories will be generated with best practices by default!