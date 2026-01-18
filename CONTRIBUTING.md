# Contributing to Laravel Repository Pattern

First off, thank you for considering contributing to Laravel Repository Pattern! 🎉

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Commit Convention](#commit-convention)
- [Versioning & Releases](#versioning--releases)
- [Pull Request Process](#pull-request-process)
- [Coding Standards](#coding-standards)

---

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

---

## Getting Started

### Ways to Contribute

- 🐛 Report bugs
- 💡 Suggest new features
- 📝 Improve documentation
- 🔧 Submit bug fixes
- ✨ Add new features
- ⚡ Performance improvements

---

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & npm (for versioning tools)
- Git

### Installation

1. **Fork the repository**

2. **Clone your fork**
   ```bash
   git clone https://github.com/YOUR_USERNAME/repository-pattern-laravel.git
   cd repository-pattern-laravel
   ```

3. **Install dependencies**
   ```bash
   # PHP dependencies
   composer install

   # Node dependencies (for standard-version)
   npm install
   ```

4. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

---

## Commit Convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Changes that don't affect code meaning (formatting, etc.)
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **perf**: Performance improvement
- **test**: Adding or updating tests
- **build**: Changes to build system or dependencies
- **ci**: Changes to CI configuration
- **chore**: Other changes that don't modify src or test files

### Examples

```bash
# New feature
git commit -m "feat(repository): add auto-detection for fillable fields"

# Bug fix
git commit -m "fix(creation): resolve type hint mismatch in update method"

# Documentation
git commit -m "docs(readme): add examples for custom filters"

# Breaking change
git commit -m "feat(contracts)!: remove constructor from Repository interface

BREAKING CHANGE: Repository interface no longer declares __construct()"

# With scope and body
git commit -m "refactor(traits): remove method_exists runtime checks

Replace runtime method checking with abstract methods for compile-time safety.
This improves IDE support and prevents runtime errors."
```

### Scopes

Common scopes in this project:
- `repository` - Base repository class
- `traits` - Trait files (Creation, Reading, etc.)
- `contracts` - Interface files
- `commands` - Artisan commands
- `infrastructure` - Infrastructure implementations
- `docs` - Documentation
- `tests` - Test files

### Breaking Changes

If your commit introduces breaking changes, add `!` after the type/scope and include `BREAKING CHANGE:` in the footer:

```
feat(contracts)!: change method signature

BREAKING CHANGE: Repository::delete() now requires $id parameter to be int|string
```

---

## Versioning & Releases

This project uses [Standard Version](https://github.com/conventional-changelog/standard-version) for automated versioning and changelog generation.

### Semantic Versioning

We follow [SemVer](https://semver.org/):
- **MAJOR** (1.0.0): Breaking changes
- **MINOR** (0.1.0): New features (backward compatible)
- **PATCH** (0.0.1): Bug fixes (backward compatible)

### Creating a Release

**For Maintainers Only**

```bash
# Automatic version bump based on commits
npm run release

# Specific version bump
npm run release:patch   # 3.0.0 → 3.0.1
npm run release:minor   # 3.0.0 → 3.1.0
npm run release:major   # 3.0.0 → 4.0.0

# First release
npm run release:first

# Dry run (preview without committing)
npm run release:dry-run
```

### What Standard Version Does

1. ✅ Analyzes commit messages since last release
2. ✅ Determines version bump (major/minor/patch)
3. ✅ Updates `package.json` version
4. ✅ Generates/updates `CHANGELOG.md`
5. ✅ Creates git commit: `chore(release): X.Y.Z`
6. ✅ Creates git tag: `vX.Y.Z`

### After Release

```bash
# Push commits and tags
git push --follow-tags origin main

# Publish to Packagist (automatic via webhook)
# No manual action needed if webhook is configured
```

---

## Pull Request Process

### Before Submitting

1. ✅ Ensure code follows [PSR-12](https://www.php-fig.org/psr/psr-12/)
2. ✅ Add/update tests if applicable
3. ✅ Update documentation if needed
4. ✅ Run tests: `composer test` (if test suite exists)
5. ✅ Ensure all commits follow convention
6. ✅ Update README if adding features

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Related Issues
Fixes #123

## Testing
- [ ] Tested manually
- [ ] Added/updated unit tests
- [ ] All tests pass

## Checklist
- [ ] Code follows PSR-12
- [ ] Commits follow conventional commits
- [ ] Documentation updated
- [ ] No breaking changes (or documented if yes)
```

### PR Review Process

1. **Automated Checks**: CI runs (if configured)
2. **Code Review**: Maintainer reviews code
3. **Discussion**: Address feedback/questions
4. **Approval**: PR approved by maintainer
5. **Merge**: Squash and merge (maintainer)

---

## Coding Standards

### PHP Standards

We follow **PSR-12** coding style:

```php
// ✅ GOOD
class UserRepository extends Repository
{
    protected string $model = User::class;

    protected function applyFilter(Request $request, Builder &$builder): void
    {
        if ($request->filled('search')) {
            $builder->where('name', 'like', '%' . $request->search . '%');
        }
    }
}

// ❌ BAD
class UserRepository extends Repository {
    protected $model = User::class; // Missing type hint

    protected function applyFilter(Request $request, Builder &$builder) { // Missing return type
        if($request->filled('search')){ // Missing spaces
            $builder->where('name','like','%'.$request->search.'%'); // Missing spaces
        }
    }
}
```

### Type Hints

Always use type hints:

```php
// ✅ GOOD
protected string $model;
protected array $fillable;
protected bool $paginationable;
protected ?int $limit;

public function create(FormRequest|array $request): Model

// ❌ BAD
protected $model;
protected $fillable;
public function create($request)
```

### Documentation

Add PHPDoc for complex methods:

```php
/**
 * Apply custom filters to the query
 *
 * Override this method to add your custom filters based on request parameters.
 * The builder is passed by reference and can be modified directly.
 *
 * @param Request $request HTTP request with filter parameters
 * @param Builder $builder Eloquent query builder
 * @return void
 */
protected function applyFilter(Request $request, Builder &$builder): void
{
    // Implementation
}
```

### SOLID Principles

Follow SOLID principles:

```php
// ✅ GOOD - Dependency Injection
public function __construct(
    ?TransactionManagerInterface $transactionManager = null
) {
    $this->transactionManager = $transactionManager ?? new LaravelTransactionManager();
}

// ❌ BAD - Direct dependency
use Illuminate\Support\Facades\DB;
DB::beginTransaction();
```

### Tests (When Available)

```php
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $repository = new UserRepository();
        $user = $repository->create(['name' => 'John']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->name);
    }
}
```

---

## Development Workflow

### Typical Workflow

1. **Create Issue** (if not exists)
2. **Fork & Clone**
3. **Create Branch**: `git checkout -b feature/my-feature`
4. **Make Changes**
5. **Commit**: Use conventional commits
6. **Push**: `git push origin feature/my-feature`
7. **Create PR**: Use PR template
8. **Address Feedback**
9. **Merge**: Maintainer merges

### Branch Naming

- `feature/feature-name` - New features
- `fix/bug-description` - Bug fixes
- `docs/what-changed` - Documentation
- `refactor/what-refactored` - Refactoring
- `test/what-tested` - Tests

---

## Questions?

- **Issues**: [GitHub Issues](https://github.com/cariuk/repository-pattern-laravel/issues)
- **Discussions**: [GitHub Discussions](https://github.com/cariuk/repository-pattern-laravel/discussions)
- **Email**: hademopilie@gmail.com

---

## Recognition

Contributors will be recognized in:
- README.md contributors section
- CHANGELOG.md for their contributions
- Git history

Thank you for making Laravel Repository Pattern better! 🚀
