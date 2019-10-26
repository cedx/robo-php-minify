<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/** @testdox Robo\PhpMinify\FastTransformer */
class FastTransformerTest extends TestCase {

  /** @testdox ->close() */
  function testClose(): void {
    $transformer = new FastTransformer;

    it('should complete without any error', function() use ($transformer) {
      $transformer->listen();
      expect(fn() => $transformer->close())->to->not->throw;
    });

    it('should be callable multiple times', function() use ($transformer) {
      expect(fn() => $transformer->close())->to->not->throw;
      expect(fn() => $transformer->close())->to->not->throw;
    });
  }

  /** @testdox ->isListening() */
  function testIsListening(): void {
    $transformer = new FastTransformer;

    it('should return whether the server is listening', function() use ($transformer) {
      expect($transformer->isListening())->to->be->false;

      $transformer->listen();
      expect($transformer->isListening())->to->be->true;

      $transformer->close();
      expect($transformer->isListening())->to->be->false;
    });
  }

  /** @testdox ->listen() */
  function testListen(): void {
    $transformer = new FastTransformer;

    it('should complete without any error', function() use ($transformer) {
      expect(fn() => $transformer->listen())->to->not->throw;
    });

    it('should be callable multiple times', function() use ($transformer) {
      expect(fn() => $transformer->listen())->to->not->throw;
      expect(fn() => $transformer->listen())->to->not->throw;
    });

    $transformer->close();
  }

  /** @testdox ->transform() */
  function testTransform(): void {
    $script = 'test/fixtures/sample.php';
    $transformer = new FastTransformer;

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
