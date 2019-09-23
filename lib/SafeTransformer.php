<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Removes comments and whitespace from a PHP script, by calling a PHP process. */
class SafeTransformer implements Transformer {

  /** Closes this transformer and releases any resources associated with it. */
  function close(): void {
    // Noop.
  }

  /**
   * Processes a PHP script.
   * @param string The path to the PHP script.
   * @return string The transformed script.
   */
  function transform(string $script): string {
    // TODO
  }
}
