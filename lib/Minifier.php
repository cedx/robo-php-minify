<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use Robo\{Result};
use Robo\Contract\{TaskInterface};
use Robo\Task\{BaseTask};

/** Removes PHP comments and whitespace by applying the `php_strip_whitespace()` function. */
class Minifier extends BaseTask implements TaskInterface {

  /** @var string The path of the output directory. */
  private $destination;

  /** @var bool Value indicating whether to silent the minifier output. */
  private $silent = false;

  /** @var string[] The file patterns of the input scripts. */
  private $sources;

  /** @var Transformer The instance used to process the PHP code. */
  private $transformer;

  /**
   * Creates a new minifier.
   * @param string|string[] $sources The file patterns of the input scripts.
   */
  function __construct($sources) {
    $this->sources = is_array($sources) ? $sources : $sources;
    $this->transformer = new SafeTransformer;
  }

  /**
   * Sets a value indicating the type of transformation applied by this minifier.
   * @param string $value The transform mode.
   * @return $this This instance.
   */
  function mode(string $value): self {
    $this->transformer = $value == TransformMode::fast ? new FastTransformer : new SafeTransformer;
    return $this;
  }

  /**
   * Runs this task.
   * @return Result The task result.
   */
  function run(): Result {
    // TODO get the file list.
    $files = [];
    foreach ($files as $file) {

    }

    $this->transformer->close();
    return Result::success($this);
  }

  /**
   * Sets a value indicating whether to silent the minifier output.
   * @param bool $value `true` to silent the minifier output, otherwise `false`.
   * @return $this This instance.
   */
  function silent(bool $value = true): self {
    $this->silent = $value;
    return $this;
  }

  /**
   * Sets the path of the output directory.
   * @param string $destination The destination directory for the minified scripts.
   * @return $this This instance.
   */
  function to(string $destination): self {
    $this->destination = $destination;
    return $this;
  }
}
