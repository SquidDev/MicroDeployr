<?php
namespace SquidDev\Deploy\Visitors;

use SquidDev\Deploy\Filters\IFilter;

/**
 * A {@link IVisitor} that simply passes info down to the child
 */
abstract class ChildVisitor implements IVisitor {
	/**
	 * The visitor to visit if the filter passes
	 * @var IFileVisitor $childVisitor
	 */
	protected $childVisitor;

	/**
	 * Create a new {@link ChildVisitor}
	 * @param IVisitor $childVisitor The child visitor
	 */
	public function __construct(IVisitor $childVisitor = NULL) {
		$this->childVisitor = $childVisitor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitDirectory($path) {
		if($this->childVisitor != NULL) {
			if($child = $this->childVisitor->visitDirectory($path)) {
				if($child == $this) return $this;
				return $this->create($child);
			}

			return NULL;
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitFile($path) {
		if($this->childVisitor != NULL) $this->childVisitor->visitFile($path);
	}

	/**
	 * Create a visitor for a child
	 * @param  $child $child The child visitor
	 * @return IFileVisitor The created visitor
	 */
	protected function create($child) {
		return new static($child);
	}
}
