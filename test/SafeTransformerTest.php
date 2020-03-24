<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use PHPUnit\Framework\{Assert, TestCase};
use function PHPUnit\Framework\{assertThat, isNull, stringContains};

/** @testdox Robo\PhpMinify\SafeTransformer */
class SafeTransformerTest extends TestCase {

  /** @testdox ->close() */
  function testClose(): void {
    $transformer = new SafeTransformer;

    // It should complete without any error.
    try {
      $transformer->close();
      assertThat(null, isNull());
    }

    catch (\Throwable $e) {
      Assert::fail($e->getMessage());
    }

    // It should be callable multiple times.
    try {
      $transformer->close();
      $transformer->close();
      assertThat(null, isNull());
    }

    catch (\Throwable $e) {
      Assert::fail($e->getMessage());
    }
  }

  /** @testdox ->transform() */
  function testTransform(): void {
    $script = 'test/fixtures/sample.php';
    $transformer = new SafeTransformer;

    // It should remove the inline comments.
    assertThat($transformer->transform($script), stringContains("<?= 'Hello World!' ?>"));

    // It should remove the multi-line comments.
    assertThat($transformer->transform($script), stringContains('namespace dummy; class Dummy'));

    // It should remove the single-line comments.
    assertThat($transformer->transform($script), stringContains('$className = get_class($this); return $className;'));

    // It should remove the whitespace.
    assertThat($transformer->transform($script), stringContains('__construct() { }'));
    $transformer->close();
  }
}
