<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function Which\{which};
use Robo\{Result};
use Robo\Contract\{TaskInterface};
use Robo\Task\{BaseTask};
use Symfony\Component\Finder\{Finder};
use Webmozart\PathUtil\{Path};

/** Removes PHP comments and whitespace by applying the `php_strip_whitespace()` function. */
class Minifier extends BaseTask implements TaskInterface {

  /** @var string The base path that is stripped from the computed path of the destination files. */
  private $base = '';

  /** @var string The path to the PHP executable. */
  private $binary = '';

  /** @var string The path of the destination directory. */
  private $destination;

  /** @var string The transform mode. */
  private $mode = TransformMode::safe;

  /** @var bool Value indicating whether to silent the minifier output. */
  private $silent = false;

  /** @var int The number of progress steps. */
  private $steps = 0;

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
   * @param string $path The new base path.
   * @return $this This instance.
   */
  function base(string $path): self {
    $this->base = Path::canonicalize($path);
    return $this;
  }

  /**
   * Sets the path to the PHP executable.
   * @param string $executable The new executable path.
   * @return $this This instance.
   */
  function binary(string $executable): self {
    $this->binary = str_replace('/', DIRECTORY_SEPARATOR, Path::canonicalize($executable));
    return $this;
  }

  /**
   * Sets a value indicating the type of transformation applied by this minifier.
   * @param string $transformMode The transform mode.
   * @return $this This instance.
   */
  function mode(string $transformMode): self {
    $this->mode = TransformMode::coerce($transformMode, TransformMode::safe);
    return $this;
  }

  /**
   * Returns the number of progress steps.
   * @return int The number of progress steps.
   */
  public function progressIndicatorSteps(): int {
    return $this->steps;
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
    foreach ($this->sources as $source) {
      $finder = new Finder;
      try { $finder->files()->followLinks()->in($source); }
      catch (\InvalidArgumentException $e) {
        try {
          if (mb_strpos($source, '/') === false) $source = "./$source";
          $parts = explode('/', $source);
          $directory = implode('/', array_slice($parts, 0, -1));

          /** @var string $pattern */
          $pattern = array_pop($parts);
          $finder = (new Finder)->files()->followLinks()->in($directory)->name($pattern);
        }

        catch (\InvalidArgumentException $e) {
          return Result::fromException($this, $e);
        }
      }

      foreach ($finder as $file) $files[$file->getRealPath()] = $file;
    }

    $this->steps = count($files);
    $this->startProgressIndicator();

    $basePath = mb_strlen($this->base) ? (string) realpath($this->base) : Path::getLongestCommonBasePath(array_keys($files));
    $count = 0;
    foreach ($files as $file) {
      if (!$this->silent) $this->printTaskInfo('Minifying {path}', ['path' => $file->getPathname()]);
      $output = Path::join($this->destination, Path::makeRelative($file->getRealPath(), $basePath));
      if (!is_dir($directory = dirname($output))) mkdir($directory, 0755, true);
      if (file_put_contents($output, $this->transformer->transform($file->getRealPath()))) $count++;
      $this->advanceProgressIndicator();
    }

    $this->transformer->close();
    $this->stopProgressIndicator();

    $fileLabel = $this->steps <= 1 ? 'file' : 'files';
    $context = ['count' => $count, 'total' => $this->steps, 'destination' => $this->destination];
    $message = "Minified {count} out of {total} PHP $fileLabel into {destination}";
    if ($count != $this->steps) return Result::error($this, $message, $context);

    $this->printTaskSuccess($message, $context);
    return Result::success($this, $message, $context);
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
    $this->destination = Path::canonicalize($destination);
    return $this;
  }
}
