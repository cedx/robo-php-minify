# Installation

## Requirements
Before installing **Robo-PHP-Minify**, you need to make sure you have [PHP](https://www.php.net)
and [Composer](https://getcomposer.org), the PHP package manager, up and running.

!!! warning
    Robo-PHP-Minify requires PHP >= **7.2.0**.

You can verify if you're already good to go with the following commands:

```shell
php --version
# PHP 7.3.8 (cli) (built: Jul 30 2019 12:44:08) ( NTS MSVC15 (Visual C++ 2017) x64 )

composer --version
# Composer version 1.9.0 2019-08-02 20:55:32
```

!!! info
    If you plan to play with the package sources, you will also need
    [Material for MkDocs](https://squidfunk.github.io/mkdocs-material).

## Installing with Composer package manager

### 1. Install it
From a command prompt, run:

```shell
composer require cedx/robo-php-minify
```

### 2. Import it
Now in your [PHP](https://www.php.net) code, you can use:

```php
<?php
use Robo\PhpMinify\{Tasks, TransformMode};
```