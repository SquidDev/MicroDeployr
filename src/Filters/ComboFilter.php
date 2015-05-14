<?php
namespace SquidDev\Deploy\Filters;

/**
 * A {@IFilter} which checks using a series of child filters
 */
class ComboFilter implements IFilter {
	protected $filters;

	/**
	 * Construct a new {@link ComboFilter}
	 * @param array $filters The child filters to use
	 */
	public function __construct(array $filters = array()) {
		$this->filters = $filters;
	}

	/**
	 * Add a filter
	 * @param IFilter $filter An additional filter to use
	 * @return this to allow chaining
	 */
	public function addFilter(IFilter $filter) {
		$this->filters[] = $filter;
		return $this;
	}

	/**
	 * Add a series of filters
	 * @param array $filters The filters to use
	 * @return this to allow chaining
	 */
	public function addFilters(array $filters) {
		foreach($filters as $filter) {
			$this->addFilter($filter);
		}
		return $this;
	}

	/**
	 * Filters as path
	 * @param  string $path The path of the file to filter on
	 * @return boolean If the path passes on all filters
	 */
	public function passes($path) {
		foreach($this->filters as $filter) {
			if(!$filter->passes($path)) {
				return false;
			}
		}

		return true;
	}
}
