<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use PHPUnit\Framework\{Assert, TestCase};
use function PHPUnit\Framework\{assertThat, isInstanceOf, logicalAnd, stringContains};

/** @testdox Robo\PhpMinify\Server */
class ServerTest extends TestCase {

	/** @var \ReflectionClass<Server> The object used to change the visibility of inaccessible class members. */
	private static \ReflectionClass $reflection;

	/** @beforeClass This method is called before the first test of this test class is run. */
	static function setUpBeforeClass(): void {
		self::$reflection = new \ReflectionClass(Server::class);
	}

	/** @testdox ->processRequest() */
	function testProcessRequest(): void {
		$method = self::$reflection->getMethod("processRequest");
		$method->setAccessible(true);

		// It should throw an exception if the input request is invalid.
		try {
			$method->invoke(new Server, []);
			Assert::fail("Exception not thrown");
		}

		catch (\Throwable $e) {
			assertThat($e, isInstanceOf(\LogicException::class));
		}

		// It should throw an exception if the requested file does not exist.
		try {
			$method->invoke(new Server, ["file" => "dummy.txt"]);
			Assert::fail("Exception not thrown");
		}

		catch (\Throwable $e) {
			assertThat($e, isInstanceOf(\RuntimeException::class));
		}

		// It should remove the comments and whitespace of the requested file.
		$output = $method->invoke(new Server, ["file" => "test/fixtures/sample.php"]);
		assertThat($output, logicalAnd(
			stringContains('<?= "Hello World!" ?>'),
			stringContains("namespace dummy; class Dummy"),
			stringContains('$className = get_class($this); return $className;'),
			stringContains("__construct() { }")
		));
	}
}
