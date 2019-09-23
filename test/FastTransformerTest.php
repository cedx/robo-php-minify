<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Robo\PhpMinify\FastTransformer` class. */
class FastTransformerTest extends TestCase {

  /** @test FastTransformer->close() */
  function testClose(): void {
    $transformer = new FastTransformer;

    it('should complete without any error', function() use ($transformer) {
      $transformer->listen();
      expect(function() use ($transformer) { $transformer->close(); })->to->not->throw;
    });

    it('should be callable multiple times', function() use ($transformer) {
      expect(function() use ($transformer) { $transformer->close(); })->to->not->throw;
      expect(function() use ($transformer) { $transformer->close(); })->to->not->throw;
    });
  }

  /** @test FastTransformer->isListening() */
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

  /** @test FastTransformer->listen() */
  function testListen(): void {
    $transformer = new FastTransformer;

    it('should complete without any error', function() use ($transformer) {
      expect(function() use ($transformer) { $transformer->listen(); })->to->not->throw;
    });

    it('should be callable multiple times', function() use ($transformer) {
      expect(function() use ($transformer) { $transformer->listen(); })->to->not->throw;
      expect(function() use ($transformer) { $transformer->listen(); })->to->not->throw;
    });

    $transformer->close();
  }

  /** @test FastTransformer->transform() */
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
