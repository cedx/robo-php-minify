<?php declare(strict_types=1);

use Robo\{Result, Tasks};
use Robo\PhpMinify\{TransformMode};

// Load the dependencies.
require_once __DIR__.'/vendor/autoload.php';

/** Provides tasks for the build system. */
class RoboFile extends Tasks {
  use \Robo\PhpMinify\Tasks;

  /**
   * Builds the project.
   * @return Result The task result.
   */
  function build(): Result {
    return $this->taskPhpMinify('src')->mode(TransformMode::fast)->to('build')->run();
  }
}
