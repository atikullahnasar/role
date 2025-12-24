# Laravel Role & Permission Management

A simple yet powerful Laravel package for managing roles and permissions with a clean, intuitive admin panel. It provides built-in migrations, routes, and views to get you started quickly.

![Laravel](https://img.shields.io/badge/Laravel-^9.0|^10.0|^11.0-red)
![PHP](https://img.shields.io/badge/PHP-^8.1-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## Features

-   **Role Management:** Create, edit, and delete user roles.
-   **Permission Management:** Define granular permissions and group them for easy organization.
-   **Role-Permission Assignment:** Seamlessly assign multiple permissions to a role.
-   **Admin Panel Integration:** Provides a ready-to-use interface at `/beft/roles` to manage everything.
-   **"Super Admin" Logic:** Easily designate a "Super Admin" role that gains exclusive access to the permission management interface.
-   **Framework Agnostic UI:** Choose to use the package views with either **Bootstrap** or **Tailwind CSS** via a simple configuration setting.
-   **Database Ready:** Includes all necessary migrations to set up your `roles`, `permissions`, and `role_has_permissions` tables.

## Prerequisites

-   PHP ^8.1
-   Laravel ^9.0 / ^10.0 / ^11.0
-   An existing authentication system in your Laravel project.

## Installation & Setup

Follow these steps to install and configure the package in your Laravel project.

### Step 1: Add the Repository

Since this package is not yet on Packagist, you must add the GitHub repository to your main project's `composer.json` file.

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/atikullahnasar/role"
    }
]
```

Save the file after adding this entry.

### Step 2: Install the Package

Use Composer to install the package from the repository you just added.

```bash
composer require atikullahnasar/role:dev-main
```

### Step 3: Publish Assets

Publish the package's migrations and configuration file to your application.

```bash
# Publish the database migrations
php artisan vendor:publish --provider="atikullahnasar\role\Provider\RolePackageServiceProvider" --tag=role-migrations

# Publish the configuration file
php artisan vendor:publish --tag=role-config
```

### Step 4: Run the Migrations

Execute the migrations to create the necessary tables in your database.

```bash
php artisan migrate
```

### Step 5: Seed Default Permissions

This package includes a seeder for default permissions. You can run it independently.

```bash
php artisan db:seed --class="atikullahnasar\role\Database\Seeders\PermissionSeeder"
```

## How It Works: The "Super Admin" Feature

This package has a special built-in feature for managing permissions:

1.  **Add a `role` Column:** First, ensure your `users` table has a `role` column (e.g., `string('role')->nullable()`).
2.  **Assign the "superAdmin" Role:** Assign the role value `superAdmin` to any user you wish to grant administrative access.
3.  **Access the Panel:** When a user logged in with the `superAdmin` role browses to `/beft/roles`, they will see a **"Permission Management"** link, allowing them to manage the application's permissions.

Users with any other role will only be able to manage roles, not the underlying permissions.

## Usage

After completing the installation, you can access the role management panel from your browser.

-   **URL:** `/beft/roles`

**Example:**
If your application is running at `http://example.com`, you can access the panel at:
`http://example.com/beft/roles`

## Configuration

After publishing the configuration file, you can find it at `config/role.php`. Here, you can customize package settings, such as choosing the UI framework for the admin panel.

```php
// config/role.php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Framework
    |--------------------------------------------------------------------------
    |
    | This option controls which UI framework the package views will use.
    | Supported options: "tailwind", "bootstrap"
    |
    */
    'ui_framework' => 'tailwind', // or 'bootstrap'
];
```

## Contributing

Contributions, bug reports, and feature requests are welcome! Feel free to open an issue or submit a pull request on the [GitHub repository](https://github.com/atikullahnasar/role).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
