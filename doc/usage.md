# Usage
If you haven't used [Robo](https://robo.li) before, be sure to check out the [related documentation](https://robo.li/getting-started), as it explains how to create a `RoboFile.php` file and to define project tasks. Once you're familiar with that process, you may install the plug-in.

## Programming interface
The plug-in provides a single task, `PhpMinify`, that takes a list of [PHP](https://www.php.net) scripts as input, and remove the comments and whitespace in these files by applying the [`php_strip_whitespace()`](https://www.php.net/manual/en/function.php-strip-whitespace.php) function on their contents.
    
### **taskPhpMinify**(mixed $patterns)
Minifies the PHP scripts corresponding to the specified file patterns, and saves the resulting output to a destination directory specified using the `to()` method:

```php
<?php
class RoboFile extends \Robo\Tasks {
  use \Robo\PhpMinify\Tasks;

  function compressPhp(): \Robo\Result {
    return $this->taskPhpMinify('path/to/src/*.php')
      ->to('path/to/out')
      ->run();
  }
}
```

The file patterns use the same syntax as the [Symfony Finder component](https://symfony.com/doc/current/components/finder.html) (for example: `"path/*/to/*/*/src"`).

!!! tip
    You can provide several file patterns to the `taskPhpMinify()` method:
    the `$patterns` parameter can be a `string` (single pattern) or an array of `string` (multiple patterns).  

## Options
The `PhpMinify` task also support the following options:

### **base**(string $path)
When writing the minified files to the destination directory, the task tries to create the most adequate file tree by determining and removing the longest common base path of all processed files.

If the resulting file tree does not meet your expectations, or if you want to customize it, you can use the `base` option. It is treated as a base path that is stripped from the computed path of the destination files:

```php
<?php
class RoboFile extends \Robo\Tasks {
  use \Robo\PhpMinify\Tasks;

  function compressPhp(): void {
    // Given the script "src/subdir/script.php"...

    $this->taskPhpMinify('src/*.php')->to('out1')->run();
    // ...will probably create the file "out1/subdir/script.php".

    $this->taskPhpMinify('src/*.php')->base('.')->to('out2')->run();
    // ...will create the file "out2/src/subdir/script.php".

    $this->taskPhpMinify('src/*.php')->base('src/subdir')->to('out3')->run();
    // ...will create the file "out3/script.php".
  }
}
```

### **binary**(string $executable = `"php"`)
The `PhpMinify` task relies on the availability of the [PHP](https://www.php.net) executable on the target system. By default, the `PhpMinify` task will use the `php` binary found on the system path.

If you want to use a different one, you can provide the path to the `php` executable by using the `binary` option:

```php
<?php
class RoboFile extends \Robo\Tasks {
  use \Robo\PhpMinify\Tasks;

  function compressPhp(): \Robo\Result {
    return $this->taskPhpMinify('src/*.php')
      ->binary('C:\\Program Files\\PHP\\php.exe')
      ->to('out')
      ->run();
  }
}
```

### **mode**(\Robo\PhpMinify\TransformMode $transformMode = `TransformMode::safe`)
The `PhpMinify` task can work in two manners, which can be selected using the `mode` option:

- the `safe` mode: as its name implies, this mode is very reliable. But it is also very slow as it spawns a new PHP process for every file to be processed. This is the default mode.
- the `fast` mode: as its name implies, this mode is very fast, but it is not very reliable. It spawns a PHP web server that processes the input files, but on some systems this fails.

```php
<?php
class RoboFile extends \Robo\Tasks {
  use \Robo\PhpMinify\Tasks;

  function compressPhp(): \Robo\Result {
    return $this->taskPhpMinify('src/*.php')
      ->mode(\Robo\PhpMinify\TransformMode::fast)
      ->to('out')
      ->run();
  }
}
```

### **silent**(bool $value = `false`)
By default, the `PhpMinify` task prints to the standard output the paths of the minified scripts. You can disable this output by setting the `silent` option.

```php
<?php
class RoboFile extends \Robo\Tasks {
  use \Robo\PhpMinify\Tasks;

  function compressPhp(): \Robo\Result {
    return $this->taskPhpMinify('src/*.php')
      ->silent()
      ->to('out')
      ->run();
  }
}
```
