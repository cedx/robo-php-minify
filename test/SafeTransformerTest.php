<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/** @testdox Robo\PhpMinify\SafeTransformer */
class SafeTransformerTest extends TestCase {

  /** @testdox ->close() */
  function testClose(): void {
    $transformer = new SafeTransformer;

    it('should complete without any error', function() use ($transformer) {
      expect(fn() => $transformer->close())->to->not->throw;
    });

    it('should be callable multiple times', function() use ($transformer) {
      expect(fn() => $transformer->close())->to->not->throw;
      expect(fn() => $transformer->close())->to->not->throw;
    });
  }

  /** @testdox ->transform() */
  function testTransform(): void {
    $script = 'test/fixtures/sample.php';
    $transformer = new SafeTransformer;

    it('should remove the inline comments', function() use ($script, $transformer) {
      expect($transformer->transform($script))->to->contain("<?= 'Hello World!' ?>");
    });

    it('should remove the multi-line comments', function() use ($script, $transformer) {
      expect($transformer->transform($script))->to->contain('namespace dummy; class Dummy');
    });

    it('should remove the single-line comments', function() use ($script, $transformer) {
      expect($transformer->transform($script))->to->contain('$className = get_class($this); return $className;');
    });

    it('should remove the whitespace', function() use ($script, $transformer) {
      expect($transformer->transform($script))->to->contain('__construct() { }');
    });

    $transformer->close();
  }
}
