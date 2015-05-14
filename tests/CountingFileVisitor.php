<?php
namespace SquidDev\Tests;

use SquidDev\Deploy\Visitors\IVisitor;

class CountingFileVisitor implements IVisitor {

	/**
	 * Number of files visited
	 * @var int $count
	 */
	private $count = NULL;

	public function visitFile($path) {
		$this->count++;
	}

	public function visitDirectory($path) {
		return $this;
	}

	public function getCount() {
		return $this->count;
	}
}
