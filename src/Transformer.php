<?php declare(strict_types=1);
namespace Robo\PhpMinify;

/** Removes comments and whitespace from a PHP script. */
interface Transformer {

	/** Closes this transformer and releases any resources associated with it. */
	function close(): void;

	/**
	 * Processes a PHP script.
	 * @param \SplFileInfo $script The path to the PHP script.
	 * @return string The transformed script.
	 */
	function transform(\SplFileInfo $script): string;
}
