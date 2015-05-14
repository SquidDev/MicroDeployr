<?php
namespace SquidDev\Deploy\Visitors;

/**
 * A {@link IVisitor} that calls a function when a file is visited
 */
class DelegateVisitor implements IVisitor {
	/**
	 * The function called when visiting a file
	 * @var \Closure $logger
	 */
	protected $logger;

	/**
	 * Create a new {@link DelegateVisitor}
	 * @param Closure $logger The function to call when a file is visited
	 */
	public function __construct(\Closure $logger) {
		$this->logger = $logger;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitDirectory($path) {
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitFile($path) {
		$this->logger->__invoke($path);
	}
}
