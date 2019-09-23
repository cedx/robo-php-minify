<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Provides a set of [Robo](https://robo.li) tasks. */
trait Tasks {

  /**
   * TODO
   * @param string|string[] $sources
   * @return Minifier
   */
  protected function taskPhpMinify($sources): Minifier {
    return $this->task(Minifier::class);
  }
}
