<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use PHPUnit\Framework\{Assert, TestCase};
use function PHPUnit\Framework\{assertThat, isFalse, isNull, isTrue, stringContains};

/** @testdox Robo\PhpMinify\FastTransformer */
class FastTransformerTest extends TestCase {

	/** @testdox ->close() */
	function testClose(): void {
		$transformer = new FastTransformer;

		// It should complete without any error.
		try {
			$transformer->listen();
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

	/** @testdox ->isListening() */
	function testIsListening(): void {
		$transformer = new FastTransformer;

		// It should return whether the server is listening.
		assertThat($transformer->isListening(), isFalse());

		$transformer->listen();
		assertThat($transformer->isListening(), isTrue());

		$transformer->close();
		assertThat($transformer->isListening(), isFalse());
	}

	/** @testdox ->listen() */
	function testListen(): void {
		$transformer = new FastTransformer;

		// It should complete without any error.
		try {
			$transformer->listen();
			assertThat(null, isNull());
		}

		catch (\Throwable $e) {
			Assert::fail($e->getMessage());
		}

		// It should be callable multiple times.
		try {
			$transformer->listen();
			$transformer->listen();
			assertThat(null, isNull());
		}

		catch (\Throwable $e) {
			Assert::fail($e->getMessage());
		}

		$transformer->close();
	}

	/** @testdox ->transform() */
	function testTransform(): void {
		$script = new \SplFileInfo("test/fixtures/sample.php");
		$transformer = new FastTransformer;

		// It should remove the inline comments.
		assertThat($transformer->transform($script), stringContains('<?= "Hello World!" ?>'));

		// It should remove the multi-line comments.
		assertThat($transformer->transform($script), stringContains("namespace dummy; class Dummy"));

		// It should remove the single-line comments.
		assertThat($transformer->transform($script), stringContains('$className = get_class($this); return $className;'));

		// It should remove the whitespace.
		assertThat($transformer->transform($script), stringContains("__construct() { }"));
		$transformer->close();
	}
}
