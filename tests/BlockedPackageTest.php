<?php
namespace SquidDev\Tests;

use SquidDev\Deploy\Filters\BlockedPackageFilter;

class BlockedPackageTest extends IFilterTestCase {
	public function testPasses() {
		$this->assertPasses('foo');

		$this->assertPasses('random');
		$this->assertPasses('random/package');
		$this->assertPasses('random/package/notInVendor');

		$this->assertPasses('vendor');
		$this->assertPasses('vendor/random');
		$this->assertPasses('vendor/random/package-not');
	}

	public function testFails() {
		$this->assertFails('vendor/random/package');
		$this->assertFails('vendor/random/package/sub-dir');
	}

	public function getFilter() {
		return new BlockedPackageFilter([
			"random/package" => true,
		], "vendor");
	}
}
