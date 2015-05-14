<?php
namespace SquidDev\Deploy\Visitors;

use SquidDev\Deploy\Filters\IFilter;

/**
 * A {@link IVisitor} that filters items
 */
class FilteringVisitor extends ChildVisitor {
	/**
	 * The filter to check paths against
	 * @var IFilter $filter
	 */
	protected $filter;

	/**
	 * Create a new {@link FilteringVisitor}
	 * @param IVisitor $childVisitor The visitor to use if filtering passed
	 * @param IFilter The filter to use
	 */
	public function __construct(IVisitor $childVisitor, IFilter $filter) {
		parent::__construct($childVisitor);
		$this->filter = $filter;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitDirectory($path) {
		if($this->filter->passes($path)) {
			return parent::visitDirectory($path);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function create($child) {
		return new FilteringVisitor($child, $this->filter);
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitFile($path) {
		if($this->filter->passes($path)) {
			parent::visitFile($path);
		}
	}
}
