<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Provides a set of [Robo](https://robo.li) tasks. */
trait Tasks {

  /**
   * Creates a task minifying a set of PHP scripts.
   * @param string|string[] $sources The file patterns of the input scripts.
   * @return Minifier The newly created task.
   */
  protected function taskPhpMinify($sources): Minifier {
    return $this->task(Minifier::class, $sources);
  }
}
