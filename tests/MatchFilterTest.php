<?php
namespace SquidDev\Tests;

use SquidDev\Deploy\Filters\MatchFilter;

class MatchFilterTest extends IFilterTestCase {

	public function testWhitelist() {
		$this->assertPasses('.htaccess');
		$this->assertPasses('.htaccess/something');

		$this->assertFails('.notAccess');

		// Contains a . but not at the beginning of the path
		$this->assertPasses('file.txt');

		$this->assertPasses('sub/.htaccess');
		$this->assertFails('sub/.notAccess');
	}

	public function testWildcards() {
		$this->assertFails('wildcards');
		$this->assertFails('wildcards/subdir');
		$this->assertFails('sub/wildcards');
		$this->assertFails('sub/wildcards/subdir');

		$this->assertFails('wildcardsAndThings');
		$this->assertFails('wildcardsAndThings/subdirectory');
		$this->assertFails('sub/wildcardsAndThings');
		$this->assertFails('sub/wildcardsAndThings/subdirectory');

		$this->assertPasses('sub-wildcards');
	}

	public function testRoot() {
		$this->assertPasses('sub/root/directory');
		$this->assertPasses('sub/root/directory/sub');

		$this->assertFails('root/directory');
		$this->assertFails('root/directory/sub');
	}

	public function getFilter() {
		return new MatchFilter([
			'!.htaccess',
			'.*',
			'wildcards*',
			'/root/directory',
		], "vendor");
	}
}
