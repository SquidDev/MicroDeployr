<?php

namespace SquidDev\Deploy\Sources;

use SquidDev\Deploy\Visitors\IVisitor;

interface ISource {
	/**
	 * Browse the tree delegating to the visitor
	 * @param IVisitor $visitor The file visitor to delegate calls to
	 */
	function accept(IVisitor $visitor);
}
