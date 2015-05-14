<?php

namespace SquidDev\Tests;

use SquidDev\Deploy\Visitors\DelegateVisitor;
use SquidDev\Deploy\Visitors\FilteringVisitor;
use SquidDev\Deploy\Visitors\ComboVisitor;
use SquidDev\Deploy\Filters\MatchFilter;

class VisitorTestCase extends \PHPUnit_Framework_TestCase {
	public function testDelegateVisitor() {

		$visitor = new CountingFileVisitor();
		$visitor->visitFile('path');
		$this->assertEquals(1, $visitor->getCount());

		$visitor->visitFile('another');
		$visitor->visitFile('file');

		$this->assertEquals(3, $visitor->getCount());
	}

	public function testFilterVisitor() {
		$counter = new CountingFileVisitor();

		$visitor = new FilteringVisitor($counter, new MatchFilter([
			'foo',
		]));

		$visitor->visitFile('path');
		$this->assertEquals(1, $counter->getCount());

		$visitor->visitFile('another');
		$this->assertEquals(2, $counter->getCount());

		$visitor->visitFile('foo');
		$visitor->visitFile('foo/bar');

		$this->assertEquals(2, $counter->getCount());

		$this->assertNull($visitor->visitDirectory('foo'));
	}

	public function testComboVisitor() {
		$counter = new CountingFileVisitor();
		$counterFiltered = new CountingFileVisitor();

		$visitor = new ComboVisitor([
			new FilteringVisitor($counterFiltered, new MatchFilter(['foo'])),
			$counter,
		]);

		$visitor->visitFile('path');
		$this->assertEquals(1, $counter->getCount());
		$this->assertEquals(1, $counterFiltered->getCount());

		$visitor->visitFile('another');
		$this->assertEquals(2, $counter->getCount());
		$this->assertEquals(2, $counterFiltered->getCount());

		$visitor->visitFile('foo');
		$visitor->visitFile('foo/bar');

		$this->assertEquals(4, $counter->getCount());
		$this->assertEquals(2, $counterFiltered->getCount());

		$childVisitor = $visitor->visitDirectory('foo');
		$childVisitor->visitFile('foo/something');

		$this->assertEquals(5, $counter->getCount());
		$this->assertEquals(2, $counterFiltered->getCount());
	}
}
