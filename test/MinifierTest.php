<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use League\Container\{ContainerAwareInterface, ContainerAwareTrait};
use PHPUnit\Framework\{TestCase};
use Robo\{Robo, TaskAccessor};
use Robo\Collection\{CollectionBuilder};
use Symfony\Component\Console\Output\{NullOutput};
use function PHPUnit\Framework\{assertThat, fileExists, logicalAnd, stringContains};

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
    // It should remove the comments and whitespace using the fast transformer.
    $testDir = 'var/test/Minifier.run.fast';
    $this->taskPhpMinify('test/fixtures')->mode(TransformMode::fast)->silent()->to($testDir)->run();

    $output = new \SplFileObject("$testDir/sample.php");
    assertThat($output->getPathname(), fileExists());
    assertThat((string) $output->fread($output->getSize()), logicalAnd(
      stringContains("<?= 'Hello World!' ?>"),
      stringContains('namespace dummy; class Dummy'),
      stringContains('$className = get_class($this); return $className;'),
      stringContains('__construct() { }')
    ));

    // It should remove the comments and whitespace using the safe transformer.
    $testDir = 'var/test/Minifier.run.safe';
    $this->taskPhpMinify('test/fixtures')->mode(TransformMode::safe)->silent()->to($testDir)->run();

    $output = new \SplFileObject("$testDir/sample.php");
    assertThat($output->getPathname(), fileExists());
    assertThat((string) $output->fread($output->getSize()), logicalAnd(
      stringContains("<?= 'Hello World!' ?>"),
      stringContains('namespace dummy; class Dummy'),
      stringContains('$className = get_class($this); return $className;'),
      stringContains('__construct() { }')
    ));
  }
}
