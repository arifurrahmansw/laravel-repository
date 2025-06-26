
# 🧰 Laravel Repository Generator

A Laravel package that provides a clean, reusable, and extendable **Repository Pattern** implementation with artisan command support. Designed to keep your business logic separate from data access.


<p>
<a href="https://packagist.org/packages/arifurrahmansw/laravel-repository">
<img alt="Packagist Stars" src="https://img.shields.io/packagist/stars/arifurrahmansw/laravel-repository">
</a>
<a href="https://packagist.org/packages/arifurrahmansw/laravel-repository">
    <img alt="GitHub issues" src="https://img.shields.io/github/issues/arifurrahmansw/laravel-repository">
</a>
<a href="https://packagist.org/packages/arifurrahmansw/laravel-repository"><img src="https://img.shields.io/packagist/dt/arifurrahmansw/laravel-repository" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/arifurrahmansw/laravel-repository"><img src="https://img.shields.io/packagist/v/arifurrahmansw/laravel-repository" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/arifurrahmansw/laravel-repository"><img src="https://img.shields.io/packagist/l/arifurrahmansw/laravel-repository" alt="License"></a>
</p>
---

## 📦 Package Name

**`arifurrahmansw/laravel-repository`**

> Build maintainable Laravel apps using the repository pattern with ease.

---

## 🚀 Features

-   🧠 Simple command to generate repository pattern files
-   📁 Automatically generates:
    -   Repository Interface
    -   Repository Class (extends `BaseRepository`)
    -   Optional Eloquent Model
-   🔌 Auto-binds interface to implementation in your `RepositoryServiceProvider`
-   ⚙️ Customizable stub publishing
-   🧪 Compatible with Laravel 10, 11, 12+

---

## 📥 Installation

Install the package via Composer:

```bash
composer require arifurrahmansw/laravel-repository
```
Publish the package assets (provider, stubs, etc.):

```bash
php artisan vendor:publish --tag=laravel-repository-provider
```
---

## 🔧 Configuration

This package supports Laravel's auto-discovery out of the box.

If you want to register the service provider manually, add it to your `config/app.php` providers array:

```php
'providers' => [
    // Other service providers...

    App\Providers\RepositoryServiceProvider::class,
],
```
---
## ✨ Usage

📁 Generate a Repository

To generate a full repository structure for a model:

```php
php artisan make:repo User
```

This will:

✅ Create `App\Models\User` (if it doesn’t exist)

✅ Generate `UserInterface.php` and `UserRepository.php` under `App\Repositories\User`

✅ Register binding automatically inside `App\Providers\RepositoryServiceProvider`

🔀 Generate Repository Without Model

```bash
php artisan make:repo Post --no-model
```

🗂 Directory Structure (Generated)

```bash
app/
├── Models/
│   └── User.php
└── Repositories/
    └── User/
        ├── UserInterface.php
        └── UserRepository.php

```

🔁 Interface Binding

The package auto-registers this in your `RepositoryServiceProvider`:

```php
$this->app->bind(
    \App\Repositories\User\UserInterface::class,
    \App\Repositories\User\UserRepository::class
);
```

## 📚 BaseRepository

All generated repositories extend `ArifurRahmanSw\Repository\BaseRepository`.

✨ Available Methods

```php
all();
find($id);
create(array $data);
update($id, array $data);
delete($id);
```

## 🛠 Helper Response Methods

```php
formatResponse(bool $status, string $message, string $redirect_to, $data = null);
successResponse(int $code, string $message, $data = null);
jsonResponse(string $message = null, array|object $data = [], int $statusCode = 200);

```

## 🧪 Example Usage in Controller

```php
use App\Repositories\User\UserInterface;

class UserController extends Controller
{
    protected UserInterface $repo;

    public function __construct(UserInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $users = $this->repo->all();
        return view('users.index', compact('users'));
    }
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to open issues or submit pull requests.
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [arifurrahmansw](https://github.com/arifurrahmansw)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
