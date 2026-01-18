# Auto-Detection of Fillable Fields

## Overview

Repository now **automatically detects fillable fields from the Model**, eliminating the need to duplicate this configuration in the repository.

**Follows DRY Principle:** Don't Repeat Yourself ✅

---

## Before (Duplication Problem) ❌

```php
// Model
class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'phone'];
}

// Repository - DUPLICATION!
class UserRepository extends Repository
{
    protected array $fillable = ['name', 'email', 'password', 'phone']; // ❌ Duplicate!
}
```

**Problems:**
- ❌ Code duplication (DRY violation)
- ❌ Need to update two places when adding fields
- ❌ Easy to get out of sync
- ❌ Maintenance nightmare

---

## After (Auto-Detection) ✅

```php
// Model
class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'phone'];
}

// Repository - No fillable needed!
class UserRepository extends Repository
{
    protected string $model = User::class;
    // ✅ Fillable automatically taken from model!
}
```

**Benefits:**
- ✅ No duplication (DRY principle)
- ✅ Single source of truth (Model)
- ✅ Less code to maintain
- ✅ Automatic sync

---

## How It Works

### Default Behavior

The `Creation` trait now auto-detects fillable:

```php
protected function getFillable(?string $method = null): array
{
    // If repository defines fillable, use it
    if ($this->fillable !== null) {
        return $this->fillable;
    }

    // Otherwise, get from model
    return $this->getModel()->getFillable();
}
```

### Generated Repository

```php
class UserRepository extends Repository
{
    protected string $model = User::class;

    // Fillable is commented out by default (uses model's fillable)
    // protected ?array $fillable = ['name', 'email'];

    // ... other properties
}
```

---

## Use Cases

### 1. Default - Use Model's Fillable ✅

**Most Common Case (95% of repositories)**

```php
// Model
class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'published_at',
    ];
}

// Repository - Nothing needed!
class PostRepository extends Repository
{
    protected string $model = Post::class;
    // ✅ Automatically uses: ['title', 'content', 'author_id', 'published_at']
}
```

**Usage:**
```php
// In controller
$post = $this->postRepository->create($request);
// Automatically uses fillable from Post model
```

---

### 2. Override Fillable (Rare Cases)

**When you need different fillable for repository operations**

```php
// Model
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ⚠️ Sensitive field
    ];
}

// Repository - Override for security
class UserRepository extends Repository
{
    protected string $model = User::class;

    // ✅ Override to exclude sensitive fields
    protected ?array $fillable = ['name', 'email', 'password'];
    // Role can only be set via dedicated method, not mass assignment
}
```

---

### 3. Different Fillable for Create vs Update

**Override `getFillable()` method for fine-grained control**

```php
class UserRepository extends Repository
{
    protected string $model = User::class;

    /**
     * Get fillable fields for specific operation
     */
    protected function getFillable(?string $method = null): array
    {
        if ($method === 'create') {
            return ['name', 'email', 'password', 'role'];
        }

        if ($method === 'update') {
            // Don't allow changing email or role on update
            return ['name', 'password'];
        }

        // Default: use model's fillable
        return parent::getFillable($method);
    }
}
```

**Usage:**
```php
// Create - allows all fields
$user = $repository->create([
    'name' => 'John',
    'email' => 'john@example.com',  // ✅ Allowed
    'password' => 'secret',
    'role' => 'admin',
]);

// Update - restricted fields
$user = $repository->update($request, $id);
// Email and role won't be updated even if present in request
```

---

### 4. Department-Specific Fillable

**Different repositories for different departments**

```php
// Model
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'cost',
        'margin',
    ];
}

// For Sales Team
class SalesProductRepository extends Repository
{
    protected string $model = Product::class;

    // Only allow price changes
    protected ?array $fillable = ['name', 'description', 'price'];
}

// For Finance Team
class FinanceProductRepository extends Repository
{
    protected string $model = Product::class;

    // Full access to cost and margin
    protected ?array $fillable = ['name', 'description', 'price', 'cost', 'margin'];
}
```

---

## Migration Guide

### Existing Repositories

If you have existing repositories with `$fillable` defined:

#### Option 1: Remove Fillable (Recommended)

```php
// Before
class UserRepository extends Repository
{
    protected array $fillable = ['name', 'email', 'password'];
}

// After
class UserRepository extends Repository
{
    // ✅ Removed - uses model's fillable automatically
}
```

#### Option 2: Keep Fillable (If Needed)

```php
class UserRepository extends Repository
{
    // ✅ Still works - takes precedence over model's fillable
    protected ?array $fillable = ['name', 'email'];
}
```

---

## Best Practices

### ✅ DO

1. **Use model's fillable by default**
   ```php
   // Just omit $fillable property
   class UserRepository extends Repository
   {
       protected string $model = User::class;
   }
   ```

2. **Override only when necessary**
   ```php
   // Only when you need different fillable
   protected ?array $fillable = ['safe_field_1', 'safe_field_2'];
   ```

3. **Document why you override**
   ```php
   /**
    * Override fillable to prevent role manipulation
    */
   protected ?array $fillable = ['name', 'email'];
   ```

### ❌ DON'T

1. **Don't duplicate model's fillable**
   ```php
   // ❌ BAD - Exact duplicate of model
   protected ?array $fillable = ['name', 'email', 'password'];
   ```

2. **Don't use empty array**
   ```php
   // ❌ BAD - Blocks all fields
   protected ?array $fillable = [];

   // ✅ GOOD - Use null or omit
   protected ?array $fillable = null;
   ```

---

## Advanced Examples

### Example 1: API vs Admin Repository

```php
// Model
class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'status',
        'featured',
    ];
}

// For Public API
class ArticleApiRepository extends Repository
{
    protected string $model = Article::class;

    // Users can only set title and content
    protected ?array $fillable = ['title', 'content'];

    protected function getFillable(?string $method = null): array
    {
        // Auto-set author from authenticated user
        return ['title', 'content'];
    }

    protected function getDataSave(array $data, string $action): array
    {
        if ($action === 'create') {
            $data['author_id'] = auth()->id();
            $data['status'] = 'draft';
        }
        return $data;
    }
}

// For Admin Panel
class ArticleAdminRepository extends Repository
{
    protected string $model = Article::class;

    // Admins have full access - use model's fillable
    // protected ?array $fillable = null; // Default behavior
}
```

### Example 2: Wizard/Multi-Step Forms

```php
class UserRegistrationRepository extends Repository
{
    protected string $model = User::class;

    protected function getFillable(?string $method = null): array
    {
        // Step-based fillable
        return match($method) {
            'step1' => ['name', 'email'],
            'step2' => ['phone', 'address'],
            'step3' => ['password', 'password_confirmation'],
            default => $this->getModel()->getFillable(),
        };
    }

    public function completeStep1(array $data): Model
    {
        return $this->create($data);
    }

    public function completeStep2(array $data, int $userId): Model
    {
        $user = $this->getModel()->find($userId);
        $fillable = $this->getFillable('step2');
        $user->fill(array_intersect_key($data, array_flip($fillable)));
        $user->save();
        return $user;
    }
}
```

---

## Testing

### Test Auto-Detection

```php
use Tests\TestCase;
use App\Repositories\UserRepository;
use App\Models\User;

class UserRepositoryTest extends TestCase
{
    public function test_uses_model_fillable_by_default()
    {
        $repository = new UserRepository();

        // Model's fillable
        $modelFillable = (new User())->getFillable();

        // Should match
        $this->assertEquals($modelFillable, $repository->getFillable());
    }

    public function test_can_override_fillable()
    {
        $repository = new class extends Repository {
            protected string $model = User::class;
            protected ?array $fillable = ['name', 'email'];
        };

        $this->assertEquals(['name', 'email'], $repository->getFillable());
    }
}
```

---

## Summary

### Before
- ❌ Define fillable in both Model and Repository
- ❌ Risk of sync issues
- ❌ More code to maintain

### After
- ✅ Define fillable only in Model
- ✅ Repository auto-detects
- ✅ Single source of truth
- ✅ Override only when needed

**Result: Cleaner, DRY-compliant code! 🎉**