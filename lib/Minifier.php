<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function Which\{which};
use Robo\{Result};
use Robo\Contract\{TaskInterface};
use Robo\Task\{BaseTask};
use Symfony\Component\Finder\{Finder};

/** Removes PHP comments and whitespace by applying the `php_strip_whitespace()` function. */
class Minifier extends BaseTask implements TaskInterface {

  /** @var string The base path that is stripped from the computed path of the destination files. */
  private $base = '';

  /** @var string The path to the PHP executable. */
  private $binary = '';

  /** @var string The transform mode. */
  private $mode = TransformMode::safe;

  /** @var string The path of the output directory. */
  private $output;

  /** @var bool Value indicating whether to silent the minifier output. */
  private $silent = false;

  /** @var string[] The file patterns of the input scripts. */
  private $sources;

  /** @var Transformer The instance used to process the PHP code. */
  private $transformer;

  /**
   * Creates a new minifier.
   * @param string|string[] $patterns The file patterns corresponding to the input scripts.
   */
  function __construct($patterns) {
    $this->sources = is_array($patterns) ? $patterns : [$patterns];
  }

  /**
   * Sets the base path that is stripped from the computed path of the destination files.
   * @param string $value The new base path.
   * @return $this This instance.
   */
  function base(string $value): self {
    $this->base = $value;
    return $this;
  }

  /**
   * Sets the path to the PHP executable.
   * @param string $value The new executable path.
   * @return $this This instance.
   */
  function binary(string $value): self {
    $this->binary = $value;
    return $this;
  }

  /**
   * Sets a value indicating the type of transformation applied by this minifier.
   * @param string $value The transform mode.
   * @return $this This instance.
   */
  function mode(string $value): self {
    $this->mode = $value;
    return $this;
  }

  /**
   * Runs this task.
   * @return Result The task result.
   */
  function run(): Result {
    /** @var string $binary */
    $binary = mb_strlen($this->binary) ? $this->binary : which('php', false, function() { return 'php'; });
    $this->transformer = $this->mode == TransformMode::fast ? new FastTransformer($binary) : new SafeTransformer($binary);

    $files = [];
    foreach ($this->sources as $pattern) {
      $finder = new Finder;
      try { $finder->files()->followLinks()->in($pattern); }

      catch (\InvalidArgumentException $e) {
        if (strpos($pattern, '/') === false) $pattern = "./$pattern";

        $parts = explode('/', $pattern);
        $directory = implode('/', array_slice($parts, 0, -1));

        try { $finder->files()->followLinks()->in($directory)->name(array_pop($parts)); }
        catch (\InvalidArgumentException $e) { return Result::fromException($this, $e); }
      }

      foreach ($finder as $file) $files[] = $file->getRealPath();
    }

    foreach ($files as $path) $this->transformer->transform($path);
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
    $this->output = rtrim($destination, '/'.DIRECTORY_SEPARATOR);
    return $this;
  }
}
