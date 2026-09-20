# Hatchyu Laravel Eloquent Foundation

Foundational Eloquent traits and scopes for strict, opinionated Laravel applications.

This package provides a set of reusable traits for common Eloquent patterns such as UUID keys, audit fields (creator/updater), media uploading, and ownership scoping.

> [!WARNING]
> This package is currently under active development and is primarily being used in my personal Laravel projects. Expect changes and improvements as it continues to evolve.

## Installation

```bash
composer require hatchyu/laravel-eloquent-foundation
```

Optionally, publish the configuration (if available in future updates):

```bash
php artisan vendor:publish --tag=eloquent-foundation-config
```

## Usage

### 1. Primary Keys (UUID / ULID)

Easily switch your models to use UUIDs or ULIDs as primary keys.

**UUID:**
```php
use Hatchyu\Eloquent\Foundation\Concerns\UuidPrimary;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use UuidPrimary;
}
```

**ULID:**
```php
use Hatchyu\Eloquent\Foundation\Concerns\UlidPrimary;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use UlidPrimary;
}
```

### 2. Audit Trails (Creator, Updater, Deleter)

Automatically track who created, updated, or deleted a record. These traits assume you have standard `created_by`, `updated_by`, and `deleted_by` columns in your database.

```php
use Hatchyu\Eloquent\Foundation\Concerns\HasCreator;
use Hatchyu\Eloquent\Foundation\Concerns\HasUpdater;
use Hatchyu\Eloquent\Foundation\Concerns\HasDeleter;

class Post extends Model
{
    use HasCreator, HasUpdater, HasDeleter;
    
    // Automatically sets 'created_by' to Auth::id() on creation
    // Automatically sets 'updated_by' to Auth::id() on update
    // Automatically sets 'deleted_by' to Auth::id() on soft deletion
}
```

### 3. Media Uploading

The `HasMediaUploader` trait simplifies handling file uploads directly in your Eloquent models. It automatically detects `UploadedFile` instances assigned to attributes, uploads them to storage, and saves the path.

```php
use Hatchyu\Eloquent\Foundation\Concerns\HasMediaUploader;

class User extends Model
{
    use HasMediaUploader;

    // Optional: Define upload path (defaults to 'uploads/default')
    const FILE_UPLOAD_PATH = 'uploads/avatars';
}

// Usage in Controller
$user->avatar = $request->file('avatar'); 
$user->save(); // File is uploaded, path is saved to DB
```

**Features:**
- Automatic storage to `public` disk.
- Deletes the old file when replaced.
- Safe for usage within transactions (uploads effectively happen `afterCommit`).

### 4. Image URL Accessors

The `HasImageUrls` trait provides dynamic accessors for your image paths, useful for APIs.

```php
use Hatchyu\Eloquent\Foundation\Concerns\HasImageUrls;

class User extends Model
{
    use HasImageUrls;

    protected array $imageFields = ['avatar'];
}
```

**Result:**
If you have an `avatar` column, you automatically get:
- `$user->avatar_url` (Original URL)
- `$user->avatar_thumb_url` (Thumbnail URL)

You can customize the dimensions by overriding `imageDimensions()` in your model.

### 5. Ownership & Row Scoping

The `HasOwner` and `BelongsToOwnerTenant` traits provide automatic assignment and global query scoping based on the authenticated user.

```php
use Hatchyu\Eloquent\Foundation\Concerns\BelongsToOwnerTenant;

class Post extends Model
{
    use BelongsToOwnerTenant;
}
```

**Functionality:**
- **Global Scope (`OwnerScope`):** Automatically scopes queries to `owner_id = Auth::id()`.
- **Auto-Assignment:** Automatically sets `owner_id = Auth::id()` when creating models.
- **Escape Macro:** Allows bypassing the owner scope when needed using `Post::withoutOwner()->get()`.
- **Customizable Column:** Configured via `config('eloquent-foundation.columns.owner')` (defaults to `owner_id`).

## Helpers

The package includes global helper functions:

- `eloquent_ulid()`: Generate a valid ULID string.
- `avatar_url($path, $width, $height)`: Generate a URL for an image path (supports resizing placeholders).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
