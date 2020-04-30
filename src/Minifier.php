<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use Robo\{Result};
use Robo\Contract\{TaskInterface};
use Robo\Task\{BaseTask};
use Symfony\Component\Finder\{Finder};
use Symfony\Component\Finder\Exception\{DirectoryNotFoundException};
use Webmozart\PathUtil\{Path};
use function Which\{which};

/** Removes PHP comments and whitespace by applying the `php_strip_whitespace()` function. */
class Minifier extends BaseTask implements TaskInterface {

  /** @var \SplFileInfo|null The base path that is stripped from the computed path of the destination files. */
  private ?\SplFileInfo $base = null;

  /** @var \SplFileInfo|null The path to the PHP executable. */
  private ?\SplFileInfo $binary = null;

  /** @var \SplFileInfo|null The path of the destination directory. */
  private ?\SplFileInfo $destination = null;

  /** @var string The transform mode. */
  private string $mode = TransformMode::safe;

  /** @var bool Value indicating whether to silent the minifier output. */
  private bool $silent = false;

  /** @var int The number of progress steps. */
  private int $steps = 0;

  /** @var string[] The file patterns of the input scripts. */
  private array $sources;

  /**
   * Creates a new minifier.
   * @param string|string[] $patterns The file patterns corresponding to the input scripts.
   */
  function __construct($patterns) {
    assert(is_string($patterns) || is_array($patterns));
    $this->sources = is_array($patterns) ? $patterns : [$patterns];
  }

  /**
   * Sets the base path that is stripped from the computed path of the destination files.
   * @param string $path The new base path.
   * @return $this This instance.
   */
  function base(string $path): self {
    assert(mb_strlen($path) > 0);
    $this->base = new \SplFileInfo(str_replace('/', DIRECTORY_SEPARATOR, Path::canonicalize($path)));
    return $this;
  }

  /**
   * Sets the path to the PHP executable.
   * @param string $executable The new executable path.
   * @return $this This instance.
   */
  function binary(string $executable): self {
    assert(mb_strlen($executable) > 0);
    $this->binary = new \SplFileInfo(str_replace('/', DIRECTORY_SEPARATOR, Path::canonicalize($executable)));
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
    $binary = mb_strlen($this->binary) ? $this->binary : which('php', false, fn() => 'php');
    $transformer = $this->mode == TransformMode::fast ? new FastTransformer($binary) : new SafeTransformer($binary);

    /** @var \SplFileInfo[] $files */
    $files = [];
    foreach ($this->sources as $source) {
      try {
        $finder = new Finder;
        $finder->files()->followLinks()->in($source);
      }

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

      foreach ($finder as $file) $files[] = $file;
    }

    $this->steps = count($files);
    $this->startProgressIndicator();

    if (mb_strlen($this->base)) $basePath = (string) realpath($this->base);
    else {
      $directories = array_map(fn($file) => $file->getPathInfo()->getRealPath(), $files);
      $basePath = Path::getLongestCommonBasePath($directories) ?: (string) getcwd();
    }

    $count = 0;
    foreach ($files as $file) {
      if (!$this->silent) $this->printTaskInfo('Minifying {path}', ['path' => $file->getPathname()]);

      $output = new \SplFileInfo(Path::join($this->destination, Path::makeRelative((string) $file->getRealPath(), $basePath)));
      $directory = $output->getPathInfo();
      if (!$directory->isDir()) mkdir($directory->getPathname(), 0755, true);
      if ($output->openFile('w')->fwrite($transformer->transform($file))) $count++;

      $this->advanceProgressIndicator();
    }

    $transformer->close();
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
    assert(mb_strlen($destination) > 0);
    $this->destination = new \SplFileInfo(str_replace('/', DIRECTORY_SEPARATOR, Path::canonicalize($destination)));
    return $this;
  }
}
