
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

✅ Generate `UserInterface.php` and `UserAbstract.php` under `App\Repositories\User`

✅ Register binding automatically inside `App\Providers\RepositoryServiceProvider`

🔀 Generate Repository Without Model

```bash
php artisan make:repo User --no-model
```

🗂 Directory Structure (Generated)

```bash
app/
├── Models/
│   └── User.php
├── Http/
│   └── Controllers/
│       └── UserController.php
├── Repositories/
│   └── User/
│       ├── UserInterface.php
│       └── UserAbstract.php
└── Providers/
    └── RepositoryServiceProvider.php


```

🔁 Interface Binding

The package auto-registers this in your `RepositoryServiceProvider`:

```php
$this->app->bind(
    \App\Repositories\User\UserInterface::class,
    \App\Repositories\User\UserAbstract::class
);
```

## 📚 BaseRepository

All generated repositories extend `ArifurRahmanSw\Repository\BaseRepository`.

✨ Available Methods

```php
public function paginate(int $limit = 10): LengthAwarePaginator;
public function all(): Collection;
public function combo(string $key = 'id', string $value = 'name'): Collection;
public function find(int $id): ?Model;
public function findBy(string $field, $value): ?Model;
public function store(array $data): object;
public function update(int $id, array $data): object;
public function destroy(int $id): object;
public function statusUpdate(int $id): object;
public function search(array $filters = [], int $limit = 10): LengthAwarePaginator;
public function restore(int $id): object;
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
      /**
     * The repository instance.
     *
     * @var UserInterface
     */
    protected UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->user->paginate(10);
        return view('users.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreUserRequest $request)
    {
        $result = $this->user->store($request->validated());

        if ($result->status) {
            return redirect()->route($result->redirect_to)->with('success', $result->message);
        }

        return back()->with('danger', $result->message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->user->find($id);
        return view('users.edit', compact('data'));
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $result = $this->user->update($id, $request->validated());

        if ($result->status) {
            return redirect()->route($result->redirect_to)->with('success', $result->message);
        }

        return back()->with('danger', $result->message);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(int $id)
    {
        $result = $this->user->destroy($id);

        if ($result->status) {
            return redirect()->route($result->redirect_to)->with('success', $result->message);
        }

        return back()->with('danger', $result->message);
    }

    /**
     * Toggle status of the resource.
     */
    public function statusUpdate(int $id)
    {
        $result = $this->user->statusUpdate($id);

        if ($result->status) {
            return redirect()->route($result->redirect_to ?? 'users.index')
                ->with('success', $result->message);
        }

        return back()->with('danger', $result->message);
    }

    /**
     * Search resource by filters.
     */
    public function search(array $filters = [], int $limit = 10): LengthAwarePaginator
    {
        return $this->user->search($filters, $limit);
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(int $id)
    {
        $result = $this->user->restore($id);

        if ($result->status) {
            return redirect()->route($result->redirect_to)->with('success', $result->message);
        }

        return back()->with('danger', $result->message);
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
