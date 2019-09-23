<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Defines the type of transformation applied by a minifier. */
abstract class TransformMode {

  /** @var string Applies a fast transformation. */
  const fast = 'fast';

  /** @var string Applies a safe transformation. */
  const safe = 'safe';
}
