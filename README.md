

# Laravel Blog Package

A simple and flexible Laravel package for managing blog posts with built-in migrations, routes, and admin panel integration.

## Features

- Blog post management
- Category management
- Built-in migrations
- Pre-configured routes
- Admin panel integration
- Customizable templates

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
        "url": "https://github.com/atikullahnasar/blog"
    }
]
```

Save the file after adding this.

### Step 1: Install the Package

```bash
composer require atikullahnasar/blog:dev-main
```

### Step 2: Publish the Migrations

```bash
php artisan vendor:publish --provider="atikullahnasar\blog\Provider\BlogPackageServiceProvider" --tag=blog-migrations
```

### Step 2.1: Publish the Config File

This allows you to choose which template you want to use:

```bash
php artisan vendor:publish --tag=blog-config
```

### Step 3: Run the Migrations

```bash
php artisan migrate
```

## Usage

After installation, you can access the blog management through the following URLs:

1. `/beft/blog-categories` - Manage blog categories
2. `/beft/blogs` - Manage blog posts

### Example URLs

1. `http://example.com/beft/blog-categories`
2. `http://example.com/beft/blogs`

## Configuration

After publishing the config file, you can customize the package settings in the `config/blog.php` file.

## Contributing

If you would like to contribute to this package, please submit a pull request or open an issue on the GitHub repository.

## License

This package is open-sourced software licensed under the MIT license.
