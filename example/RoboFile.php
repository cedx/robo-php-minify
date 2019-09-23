<?php declare(strict_types=1);

use Robo\{Result, Tasks};
use Robo\PhpMinify\{TransformMode};

// Load the dependencies.
require_once __DIR__.'/vendor/autoload.php';

/** Provides tasks for the build system. */
class RoboFile extends Tasks {
  use \Robo\PhpMinify\Tasks;

  /**
   * Compresses a given set of PHP scripts.
   * @return Result The task result.
   */
  function compressPhp(): Result {
    $isWindows = PHP_OS_FAMILY == 'Windows';
    return $this->taskPhpMinify('path/to/src/**/*.php')
      ->binary($isWindows ? 'C:\\Program Files\\PHP\\php.exe' : '/usr/bin/php')
      ->mode($isWindows ? TransformMode::safe : TransformMode::fast)
      ->silent(stream_isatty(STDOUT))
      ->to('path/to/out')
      ->run();
  }
}
