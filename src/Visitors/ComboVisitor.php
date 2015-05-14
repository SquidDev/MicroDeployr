<?php
namespace SquidDev\Deploy\Visitors;

use SquidDev\Deploy\Visitors\IVisitor;

/**
 * A {@link IVisitor} that visits multiple children
 */
class ComboVisitor implements IVisitor {
	/**
	 * The visitor to visit if the Visitor passes
	 * @var IFileVisitor $childVisitor
	 */
	protected $visitors;

	/**
	 * Create a new {@link ComboVisitor}
	 * @param array $visitors The child visitors to visit
	 */
	public function __construct(array $visitors = array()) {
		$this->visitors = $visitors;
	}

	/**
	* Add a Visitor
	* @param IVisitor $visitor An additional Visitor to use
	* @return this to allow chaining
	*/
	public function addVisitor(IVisitor $visitor) {
		$this->visitors[] = $visitor;
		return $this;
	}

	/**
	* Add a series of Visitors
	* @param array $Visitors The Visitors to use
	* @return this to allow chaining
	*/
	public function addVisitors(array $visitors) {
		foreach($visitors as $visitor) {
			$this->addVisitor($visitor);
		}
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitDirectory($path) {
		$children = array();
		foreach($this->visitors as $visitor) {
			$child = $visitor->visitDirectory($path);
			if($child != NULL) $children[] = $child;
		}

		if(count($children) == 0) {
			return NULL;
		} else if($this->visitors == $children) {
			return $this;
		} else {
			return new ComboVisitor($children);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitFile($path) {
		foreach($this->visitors as $visitor) {
			$visitor->visitFile($path);
		}
	}
}
