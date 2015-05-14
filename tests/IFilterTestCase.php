<?php
namespace SquidDev\Tests;

use SquidDev\Deploy\Filters\IFilter;

abstract class IFilterTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * The cached filter to use
	 * @var IFilter
	 */
	private $filter = NULL;

	/**
	 * Get the filter for this test
	 * @return IFilter The filter
	 */
	public abstract function getFilter();

	public function getFilterCached() {
		if($this->filter == NULL) {
			$this->filter = $this->getFilter();
		}
		return $this->filter;
	}

	/**
	 * Assert a path passes
	 * @param string  $path   The path to validate
	 */
	public function assertPasses($path) {
		$this->assertTrue($this->getFilterCached()->passes($path), $path);
	}

	/**
	 * Assert a path fails
	 * @param string  $path   The path to validate
	 */
	public function assertFails($path) {
		$this->assertFalse($this->getFilterCached()->passes($path), $path);
	}
}
