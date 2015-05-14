<?php
namespace SquidDev\Deploy\Filters;

/**
 * A filter that blocks a series of vendor packages
 */
class BlockedPackageFilter implements IFilter {
	/**
	 * An array of blocked packages
	 * @var blockedPackages
	 */
	protected $blockedPackages;

	/**
	 * The vendor that packages are installed in
	 * @var string
	 */
	protected $vendorDir;

	/**
	 * Construct a new {@link Filter} from an array of patterns
	 * @param array  $patterns The patterns to filter on
	 * @param string $vendor   The vendor directory to use
	 */
	public function __construct(array $blockedPackages, $vendor) {
		$this->blockedPackages = $blockedPackages;

		if(substr($vendor, -1) != '/' && $vendor != '') $vendor .= '/';
		$this->vendor = $vendor;
	}

	/**
	 * Filters as path
	 * @param  string $path The path of the file to filter on
	 * @return boolean If the path matches the filter
	 */
	public function passes($path) {
		$len = strlen($this->vendor);

		// If doesn't start with vendor
		if(substr($path, 0, $len) !== $this->vendor) return true;

		// We want to match the regex `vendor/([^/]+?/[^/]+?)/?`
		// So first find the first '/'. If it doesn't exist then we can pass
		$offset = strpos($path, '/', $len);
		if($offset == NULL) return true;

		// Then the second '/'. If it doesn't exist we just use the string length
		$offset = strpos($path, '/', $offset + 1);
		if($offset == NULL) $offset = strlen($path);

		return !isset($this->blockedPackages[substr($path, $len, $offset - $len)]);
	}

	/**
	 * Create a new {@link BlockedPackageFilter} from a composer.lock file and a vendor directory
	 * @param  string $lock The lock file
	 * @param  string $vendorDir The vendor directory
	 * @return BlockedPackageFilter The resulting filter
	 */
	public static function fromLock($lock, $vendorDir = 'vendor') {
		$blacklist = [];
		foreach(static::readJson($lock, true)['packages-dev'] as $package) {
			$blacklist[$package['name']] = true;
		}

		return new static($blacklist, $vendorDir);
	}

	/**
	 * Create a new {@link BlockedPackageFilter} from a composer.lock file and a vendor directory
	 * @param  string $root The root directory to find the lock file in
	 * @param  string $vendorDir The vendor directory
	 * @return BlockedPackageFilter The resulting filter
	 */
	public static function fromDir($root, $vendorDir = 'vendor') {
		return static::fromLock($root . '/composer.lock', $vendorDir);
	}

	/**
	 * Read a JSON file
	 * @param  string $path The path to read
	 * @return The resulting object
	 */
	protected static function readJson($path, $assoc = false) {
		$data = file_get_contents($path);
		if(!$data) throw new Exception('Cannot open ' . $path);

		$data = json_decode($data, $assoc);
		if(!$data) throw new Exception('Cannot read ' . $path);
		return $data;
	}
}
