<?php
namespace SquidDev\Deploy\Filters;

/**
 * Filters paths based on last modified time
 */
class MTimeFilter implements IFilter {
	/**
	 * Last time it was modified
	 * @var [type]
	 */
	protected $lastTime;

	/**
	 * Construct a new {@link MTimeFilter}
	 * @param int $lastTime The last time an upload occured
	 */
	public function __construct($lastTime = 0) {
		$this->lastTime = $lastTime;
	}

	/**
	 * Filters as path
	 * @param  string $path The path of the file to filter on
	 * @return boolean If the path matches the filter
	 */
	public function passes($path) {
		// We can't really validate using mtime on folders without iterating the folder.
		if(is_dir($path)) return true;

		$time = filemtime($path);

		// Play safe
		if(!$time) return true;

		return $time > $this->lastTime;
	}
}
