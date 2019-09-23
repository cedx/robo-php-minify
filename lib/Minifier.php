<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use Robo\{Result};
use Robo\Contract\{TaskInterface};
use Robo\Task\{BaseTask};

/** Removes PHP comments and whitespace by applying the `php_strip_whitespace()` function. */
class Minifier extends BaseTask implements TaskInterface {

  /** Runs this task. */
  function run(): Result {
    // TODo
  }
}
