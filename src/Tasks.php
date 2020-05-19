<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use Robo\Collection\{CollectionBuilder};

/** Provides a set of [Robo](https://robo.li) tasks. */
trait Tasks {

	/**
	 * Creates a task minifying a set of PHP scripts.
	 * @param string|string[] $patterns The file patterns corresponding to the input scripts.
	 * @return CollectionBuilder|Minifier The newly created task.
	 */
	protected function taskPhpMinify($patterns) {
		assert(is_string($patterns) || is_array($patterns));
		return $this->task(Minifier::class, $patterns);
	}
}
