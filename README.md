# Laravel Role Package

A simple and flexible Laravel package for managing Role posts with built-in migrations, routes.

## Prerequisites

- Laravel framework
- An authentication system (required)

## Installation

This package is not published on Packagist yet, so you need to add the GitHub repository manually to your main project's composer.json file.

### Step 0: Add Repository to Composer

Add the following inside your `composer.json` file:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/atikullahnasar/role"
    }
]
```

Save the file after adding this.

### Step 1: Install the Package

```bash
composer require atikullahnasar/role:dev-main
```

### Step 2: Publish the Migrations

```bash
php artisan vendor:publish --provider="atikullahnasar\role\Provider\RolePackageServiceProvider" --tag=role-migrations
```

### Step 2.1: Publish the Config File

This allows you to choose which template you want to use:

```bash
php artisan vendor:publish --tag=role-config
```

### Step 3: Run the Migrations

```bash
php artisan migrate
```
and then **Run the seeder independently**

If you prefer not to modify your `DatabaseSeeder.php`, you can run the seeder directly from the command line:

```bash
php artisan db:seed --class="atikullahnasar\setting\Database\Seeders\CountrySeeder"
```


## Usage

After installation, you can access the role management through the following URLs:

1. `/beft/roles` - Manage role 

### Example URLs
 `http://example.com/beft/roles`

## Configuration

After publishing the config file, you can customize the package settings in the `config/role.php` file. 
here you can decide which(tailwind/bootstrap) one want to use.

## Contributing

If you would like to contribute to this package, please submit a pull request or open an issue on the GitHub repository.

## License

This package is open-sourced software licensed under the MIT license.
