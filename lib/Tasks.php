<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Provides a set of [Robo](https://robo.li) tasks. */
trait Tasks {

  /**
   * @return Minifier
   */
  protected function taskPhpMinify(): Minifier {
    return $this->task(Minifier::class);
  }
}
