<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use function PHPUnit\Expect\{expect, it};
use League\Container\{ContainerAwareInterface, ContainerAwareTrait};
use PHPUnit\Framework\{TestCase};
use Robo\{Robo, TaskAccessor};
use Robo\Collection\{CollectionBuilder};
use Symfony\Component\Console\Output\{NullOutput};

/** @testdox Robo\PhpMinify\Minifier */
class MinifierTest extends TestCase implements ContainerAwareInterface {
  use ContainerAwareTrait;
  use Tasks;
  use TaskAccessor;

  /**
   * Scaffolds the collection builder.
   * @return CollectionBuilder The newly created collection builder.
   */
  function collectionBuilder(): CollectionBuilder {
    return CollectionBuilder::create($this->getContainer(), new \Robo\Tasks);
  }

  /** Sets up the dependency injection container. */
  function setup(): void {
    $this->setContainer(Robo::createDefaultContainer(null, new NullOutput));
  }

  /** @testdox ->run() */
  function testRun(): void {
    it('should remove the comments and whitespace using the fast transformer', function() {
      $testDir = 'var/test/Minifier.run.fast';
      $this->taskPhpMinify('test/fixtures')->mode(TransformMode::fast)->silent()->to($testDir)->run();

      expect("$testDir/sample.php")->file()->to->exist;
      expect((string) @file_get_contents("$testDir/sample.php"))->to->contain("<?= 'Hello World!' ?>")
        ->and->contain('namespace dummy; class Dummy')
        ->and->contain('$className = get_class($this); return $className;')
        ->and->contain('__construct() { }');
    });

    it('should remove the comments and whitespace using the safe transformer', function() {
      $testDir = 'var/test/Minifier.run.safe';
      $this->taskPhpMinify('test/fixtures')->mode(TransformMode::safe)->silent()->to($testDir)->run();

      expect("$testDir/sample.php")->file()->to->exist;
      expect((string) @file_get_contents("$testDir/sample.php"))->to->contain("<?= 'Hello World!' ?>")
        ->and->contain('namespace dummy; class Dummy')
        ->and->contain('$className = get_class($this); return $className;')
        ->and->contain('__construct() { }');
    });
  }
}
