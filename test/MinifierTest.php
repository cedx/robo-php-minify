<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Robo\PhpMinify\Minifier` class. */
class MinifierTest extends TestCase {

  /** @test Minifier->run() */
  function testRun(): void {
    it('should remove the comments and whitespace using the fast transformer', function() {
      $testDir = 'var/test/Minifier.run.fast';
      (new Minifier('test/fixtures'))->mode(TransformMode::fast)->silent()->to($testDir)->run();

      expect("$testDir/sample.php")->file()->to->exist;
      expect((string) @file_get_contents("$testDir/sample.php"))->to->contain("<?= 'Hello World!' ?>")
        ->and->contain('namespace dummy; class Dummy')
        ->and->contain('$className = get_class($this); return $className;')
        ->and->contain('__construct() { }');
    });

    it('should remove the comments and whitespace using the safe transformer', function() {
      $testDir = 'var/test/Minifier.run.safe';
      (new Minifier('test/fixtures'))->mode(TransformMode::safe)->silent()->to($testDir)->run();

      expect("$testDir/sample.php")->file()->to->exist;
      expect((string) @file_get_contents("$testDir/sample.php"))->to->contain("<?= 'Hello World!' ?>")
        ->and->contain('namespace dummy; class Dummy')
        ->and->contain('$className = get_class($this); return $className;')
        ->and->contain('__construct() { }');
    });
  }
}
