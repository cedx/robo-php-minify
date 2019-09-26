<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Robo\PhpMinify\Server` class. */
class ServerTest extends TestCase {

  /** @var \ReflectionClass The object used to change the visibility of inaccessible class members. */
  private static $reflection;

  /** @beforeClass This method is called before the first test of this test class is run. */
  static function setUpBeforeClass(): void {
    self::$reflection = new \ReflectionClass(Server::class);
  }

  /** @test Server->processRequest() */
  function testProcessRequest(): void {
    $method = self::$reflection->getMethod('processRequest');
    $method->setAccessible(true);

    it('should throw an exception if the input request is invalid', function() use ($method) {
      expect(function() use ($method) { $method->invoke(new Server, []); })->to->throw(\LogicException::class);
    });

    it('should throw an exception if the requested file does not exist', function() use ($method) {
      expect(function() use ($method) { $method->invoke(new Server, ['file' => 'dummy.txt']); })->to->throw(\RuntimeException::class);
    });

    it('should remove the comments and whitespace of the requested file', function() use ($method) {
      $output = $method->invoke(new Server, ['file' => 'test/fixtures/sample.php']);
      expect($output)->to->contain("<?= 'Hello World!' ?>")
        ->and->contain('namespace dummy; class Dummy')
        ->and->contain('$className = get_class($this); return $className;')
        ->and->contain('__construct() { }');
    });
  }
}
