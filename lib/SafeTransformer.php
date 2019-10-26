<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Removes comments and whitespace from a PHP script, by calling a PHP process. */
class SafeTransformer implements Transformer {

  /** @var string The path to the PHP executable. */
  private string $executable;

  /**
   * Creates a new safe transformer.
   * @param string $executable The path to the PHP executable.
   */
  function __construct(string $executable = 'php') {
    $this->executable = $executable;
  }

  /** Closes this transformer and releases any resources associated with it. */
  function close(): void {
    // Noop.
  }

  /**
   * Processes a PHP script.
   * @param string $script The path to the PHP script.
   * @return string The transformed script.
   */
  function transform(string $script): string {
    $phpExecutable = escapeshellarg($this->executable);
    $phpScript = escapeshellarg((string) realpath($script));
    exec("$phpExecutable -w $phpScript", $output);
    return implode(PHP_EOL, $output);
  }
}
