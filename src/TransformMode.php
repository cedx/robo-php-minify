<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use Enum\{EnumTrait};

/** Defines the type of transformation applied by a minifier. */
final class TransformMode {
  use EnumTrait;

  /** @var string Applies a fast transformation. */
  const fast = 'fast';

  /** @var string Applies a safe transformation. */
  const safe = 'safe';
}
