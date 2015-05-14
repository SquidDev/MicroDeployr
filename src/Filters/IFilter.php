<?php
namespace SquidDev\Deploy\Filters;

/**
 * Filters a path based on criteria
 */
interface IFilter {
	/**
	 * Filters as path
	 * @param  string $path The path of the file to filter on
	 * @return boolean If the path matches the filter
	 */
	public function passes($path);
}
