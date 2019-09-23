<?php declare(strict_types=1);
use Robo\{Result, Tasks};

// Load the dependencies.
require_once __DIR__.'/vendor/autoload.php';

/** Provides tasks for the build system. */
class RoboFile extends Tasks {
  use \Robo\PhpMinify\Tasks;

  /**
   * Compresses a given set of PHP scripts.
   * @return Result The task result.
   */
  function minifyPhp(): Result {
    return $this->taskPhpMinify('path/to/src/**/*.php')
      ->to('path/to/out')
      ->run();
  }
}
