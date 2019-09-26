<?php declare(strict_types=1);

use Robo\{Result, Tasks};
use Robo\PhpMinify\{TransformMode};

// Load the dependencies.
require_once __DIR__.'/vendor/autoload.php';

/** Provides tasks for the build system. */
class RoboFile extends Tasks {
  use \Robo\PhpMinify\Tasks;

  /** Creates a new task runner. */
  function __construct() {
    $path = (string) getenv('PATH');
    $vendor = (string) realpath('vendor/bin');
    if (mb_strpos($path, $vendor) === false) putenv("PATH=$vendor".PATH_SEPARATOR.$path);
    $this->stopOnFail();
  }

  /**
   * Builds the project.
   * @return Result The task result.
   */
  function build(): Result {
    return $this->taskPhpMinify('lib')
      ->mode(TransformMode::fast)
      ->to('build')
      ->run();
  }

  /**
   * Deletes all generated files and reset any saved state.
   * @return Result The task result.
   */
  function clean(): Result {
    return $this->collectionBuilder()
      ->addTask($this->taskCleanDir('var'))
      ->addTask($this->taskDeleteDir(['build', 'doc/api', 'web']))
      ->run();
  }

  /**
   * Uploads the results of the code coverage.
   * @return Result The task result.
   */
  function coverage(): Result {
    return $this->_exec('coveralls var/coverage.xml');
  }

  /**
   * Builds the documentation.
   * @return Result The task result.
   */
  function doc(): Result {
    return $this->collectionBuilder()
      ->addTask($this->taskFilesystemStack()
        ->copy('CHANGELOG.md', 'doc/about/changelog.md')
        ->copy('LICENSE.md', 'doc/about/license.md'))
      ->addTask($this->taskExec('mkdocs build --config-file=doc/mkdocs.yaml'))
      ->addTask($this->taskFilesystemStack()
        ->remove(['doc/about/changelog.md', 'doc/about/license.md', 'web/mkdocs.yaml']))
      ->run();
  }

  /**
   * Performs the static analysis of source code.
   * @return Result The task result.
   */
  function lint(): Result {
    return $this->taskExecStack()
      ->exec('php -l example/RoboFile.php')
      ->exec('phpstan analyse --configuration=etc/phpstan.neon')
      ->run();
  }

  /**
   * Starts the development server.
   * @return Result The task result.
   */
  function serve(): Result {
    return $this->taskServer(8000)->dir('lib')->run();
  }

  /**
   * Runs the test suites.
   * @return Result The task result.
   */
  function test(): Result {
    return $this->_exec('phpunit --configuration=etc/phpunit.xml --stderr');
  }

  /**
   * Upgrades the project to the latest revision.
   * @return Result The task result.
   */
  function upgrade(): Result {
    $composer = PHP_OS_FAMILY == 'Windows' ? 'php '.escapeshellarg('C:\Program Files\PHP\share\composer.phar') : 'composer';
    return $this->taskExecStack()
      ->exec('git reset --hard')
      ->exec('git fetch --all --prune')
      ->exec('git pull --rebase')
      ->exec("$composer update --no-interaction")
      ->run();
  }

  /**
   * Increments the version number of the package.
   * @param string $component The part in the version number to increment.
   * @return Result The task result.
   */
  function version(string $component = 'patch'): Result {
    return $this->taskSemVer('.semver')->increment($component)->run();
  }

  /**
   * Watches for file changes.
   * @return Result The task result.
   */
  function watch(): Result {
    return $this->taskWatch()->monitor('test', function() { $this->test(); })->run();
  }
}
