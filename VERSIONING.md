# Versioning Guide

This document explains how versioning works in this project using **Standard Version**.

---

## Overview

We use:
- **[Semantic Versioning](https://semver.org/)** for version numbers
- **[Conventional Commits](https://www.conventionalcommits.org/)** for commit messages
- **[Standard Version](https://github.com/conventional-changelog/standard-version)** for automation

---

## Semantic Versioning

Version format: **MAJOR.MINOR.PATCH** (e.g., `3.2.1`)

### Version Increments

| Type | Example | When to Use |
|------|---------|-------------|
| **MAJOR** | 3.0.0 → 4.0.0 | Breaking changes (BC breaks) |
| **MINOR** | 3.0.0 → 3.1.0 | New features (backward compatible) |
| **PATCH** | 3.0.0 → 3.0.1 | Bug fixes (backward compatible) |

### Examples

```bash
# PATCH (3.0.0 → 3.0.1)
fix(creation): resolve null pointer in update method

# MINOR (3.0.0 → 3.1.0)
feat(repository): add support for custom transaction managers

# MAJOR (3.0.0 → 4.0.0)
feat(contracts)!: remove deprecated methods

BREAKING CHANGE: Removed Repository::oldMethod(). Use newMethod() instead.
```

---

## Conventional Commits

### Format

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Commit Types

| Type | Description | Version Impact | Appears in Changelog |
|------|-------------|----------------|---------------------|
| `feat` | New feature | MINOR | ✅ Yes |
| `fix` | Bug fix | PATCH | ✅ Yes |
| `perf` | Performance improvement | PATCH | ✅ Yes |
| `refactor` | Code refactoring | - | ✅ Yes |
| `docs` | Documentation | - | ✅ Yes |
| `style` | Code style/formatting | - | ❌ No (hidden) |
| `test` | Tests | - | ❌ No (hidden) |
| `chore` | Maintenance | - | ❌ No (hidden) |
| `build` | Build system | - | ❌ No (hidden) |
| `ci` | CI configuration | - | ❌ No (hidden) |

### Breaking Changes

Add `!` after type/scope and include `BREAKING CHANGE:` in footer:

```
feat(contracts)!: change Repository constructor

BREAKING CHANGE: Repository now requires TransactionManagerInterface in constructor.
Migration guide: Pass null to use default implementation.
```

---

## Using Standard Version

### Prerequisites

```bash
# Install dependencies
npm install
```

### Release Commands

#### 1. Automatic Release (Recommended)

Analyzes commits and determines version automatically:

```bash
npm run release
```

**What it does:**
- Analyzes commits since last tag
- Bumps version based on commit types
- Generates/updates CHANGELOG.md
- Creates commit: `chore(release): X.Y.Z`
- Creates tag: `vX.Y.Z`

#### 2. Specific Version Bump

Force a specific version type:

```bash
# Patch: 3.0.0 → 3.0.1
npm run release:patch

# Minor: 3.0.0 → 3.1.0
npm run release:minor

# Major: 3.0.0 → 4.0.0
npm run release:major
```

#### 3. First Release

For the first release of a new version:

```bash
npm run release:first
```

#### 4. Dry Run

Preview what would happen without making changes:

```bash
npm run release:dry-run
```

**Output shows:**
- Version that would be created
- Commits that would be included
- Changelog that would be generated

---

## Release Workflow

### Step-by-Step Process

#### 1. Make Changes

```bash
git checkout -b feature/my-feature
# Make your changes
```

#### 2. Commit with Convention

```bash
git add .
git commit -m "feat(repository): add auto-fillable detection

Repositories now automatically detect fillable fields from the model.
This eliminates code duplication and follows DRY principle."
```

#### 3. Create Pull Request

```bash
git push origin feature/my-feature
# Create PR on GitHub
```

#### 4. Merge to Main

After PR approval, merge to main branch.

#### 5. Create Release (Maintainers Only)

```bash
# Switch to main
git checkout main
git pull origin main

# Run standard-version
npm run release

# Review the changes
git log -1          # See release commit
git tag             # See created tag
cat CHANGELOG.md    # Review changelog

# Push to remote
git push --follow-tags origin main
```

#### 6. Publish (Automatic)

- GitHub webhook triggers Packagist update
- New version appears on Packagist automatically

---

## Version Detection Examples

### Example 1: Bug Fixes Only

**Commits:**
```
fix(creation): handle null values in fillable
fix(reading): pagination with zero results
```

**Result:**
- Version: 3.0.0 → 3.0.1 (PATCH)
- Changelog includes both fixes

### Example 2: New Features

**Commits:**
```
feat(repository): add transaction manager interface
feat(contracts): add exception factory
fix(traits): resolve type mismatch
```

**Result:**
- Version: 3.0.0 → 3.1.0 (MINOR)
- Changelog includes features and fixes

### Example 3: Breaking Changes

**Commits:**
```
feat(contracts)!: remove constructor from interface

BREAKING CHANGE: Repository interface no longer declares __construct()
```

**Result:**
- Version: 3.0.0 → 4.0.0 (MAJOR)
- Changelog includes breaking change warning

### Example 4: Mixed Commits

**Commits:**
```
feat(repository): add fillable auto-detection
fix(stub): update generated template
docs(readme): add examples
chore: update dependencies
```

**Result:**
- Version: 3.0.0 → 3.1.0 (MINOR - feat triggers minor)
- Changelog includes:
  - ✅ Features section (feat)
  - ✅ Bug Fixes section (fix)
  - ✅ Documentation section (docs)
  - ❌ Chore (hidden)

---

## Changelog Format

Generated CHANGELOG.md follows this structure:

```markdown
# Changelog

## [3.1.0](https://github.com/cariuk/repository-pattern-laravel/compare/v3.0.0...v3.1.0) (2026-01-18)

### Features

* **repository:** add fillable auto-detection ([abc123](https://github.com/cariuk/repository-pattern-laravel/commit/abc123))
* **contracts:** add transaction manager interface ([def456](https://github.com/cariuk/repository-pattern-laravel/commit/def456))

### Bug Fixes

* **creation:** resolve type hint mismatch ([789ghi](https://github.com/cariuk/repository-pattern-laravel/commit/789ghi))
* **traits:** fix encapsulation issues ([jkl012](https://github.com/cariuk/repository-pattern-laravel/commit/jkl012))

### Documentation

* **readme:** add comprehensive examples ([mno345](https://github.com/cariuk/repository-pattern-laravel/commit/mno345))
```

---

## Best Practices

### ✅ DO

1. **Follow conventional commit format**
   ```bash
   git commit -m "feat(scope): description"
   ```

2. **Use meaningful scopes**
   ```bash
   feat(repository): ...    # Good
   feat(stuff): ...         # Bad
   ```

3. **Write clear descriptions**
   ```bash
   fix(creation): resolve null pointer in update method    # Good
   fix: bug                                                # Bad
   ```

4. **Document breaking changes**
   ```bash
   feat!: change API

   BREAKING CHANGE: Detailed explanation and migration guide
   ```

5. **Test before releasing**
   ```bash
   npm run release:dry-run  # Preview first
   ```

### ❌ DON'T

1. **Don't mix types in one commit**
   ```bash
   # Bad - multiple types
   git commit -m "feat: add feature and fix bug and update docs"

   # Good - separate commits
   git commit -m "feat: add feature"
   git commit -m "fix: fix bug"
   git commit -m "docs: update docs"
   ```

2. **Don't forget scope**
   ```bash
   # Bad
   git commit -m "feat: add something"

   # Good
   git commit -m "feat(repository): add transaction support"
   ```

3. **Don't manually edit version**
   ```bash
   # Bad - manual edit
   vim package.json  # Change version manually

   # Good - use standard-version
   npm run release
   ```

4. **Don't skip conventional format**
   ```bash
   # Bad
   git commit -m "added new feature"

   # Good
   git commit -m "feat(repository): add new feature"
   ```

---

## Configuration

### `.versionrc.json`

Configuration file for Standard Version:

```json
{
  "header": "# Changelog\n\nAll notable changes...",
  "types": [
    { "type": "feat", "section": "Features" },
    { "type": "fix", "section": "Bug Fixes" },
    { "type": "perf", "section": "Performance Improvements" },
    { "type": "refactor", "section": "Code Refactoring" },
    { "type": "docs", "section": "Documentation" },
    { "type": "style", "hidden": true },
    { "type": "chore", "hidden": true },
    { "type": "test", "hidden": true }
  ],
  "bumpFiles": [
    { "filename": "package.json", "type": "json" }
  ]
}
```

### `package.json` Scripts

```json
{
  "scripts": {
    "release": "standard-version",
    "release:minor": "standard-version --release-as minor",
    "release:major": "standard-version --release-as major",
    "release:patch": "standard-version --release-as patch",
    "release:first": "standard-version --first-release",
    "release:dry-run": "standard-version --dry-run"
  }
}
```

---

## Troubleshooting

### Issue: "No commits since last release"

**Solution:** Make commits with conventional format.

### Issue: "Wrong version bumped"

**Cause:** Commit type determines version bump.

**Solution:**
- `fix:` commits → PATCH
- `feat:` commits → MINOR
- `feat!:` or `BREAKING CHANGE:` → MAJOR

Use specific release command if needed:
```bash
npm run release:major  # Force major bump
```

### Issue: "Changelog not updated"

**Solution:** Ensure commits follow conventional format. Only typed commits appear.

### Issue: "Need to undo release"

```bash
# Delete tag
git tag -d vX.Y.Z
git push origin :refs/tags/vX.Y.Z

# Reset commit
git reset --hard HEAD~1
git push -f origin main
```

---

## Resources

- [Semantic Versioning](https://semver.org/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Standard Version](https://github.com/conventional-changelog/standard-version)
- [Keep a Changelog](https://keepachangelog.com/)

---

## Quick Reference

```bash
# Development
git commit -m "feat(scope): description"
git commit -m "fix(scope): description"

# Release (maintainers only)
npm run release              # Auto version
npm run release:dry-run      # Preview
git push --follow-tags origin main

# Version format
MAJOR.MINOR.PATCH
  │     │     └─ fix: commits
  │     └─────── feat: commits
  └───────────── BREAKING CHANGE: or !
```

---

Happy versioning! 🚀