<?php
namespace SquidDev\Deploy\Filters;

// If fnmatch doesn't exist then we will add some
// constants
if (!function_exists('fnmatch')) {
	define('FNM_PATHNAME', 1);
	define('FNM_NOESCAPE', 2);
	define('FNM_PERIOD', 4);
	define('FNM_CASEFOLD', 16);
}

/**
 * Filters paths based on basic patterns
 * Uses {@link fnmatch}
 */
class MatchFilter implements IFilter {
	/**
	 * Patterns that force the item to be blacklisted
	 * @var array $patterns
	 */
	protected $blacklist;

	/**
	 * Patterns that force the item to be contained
	 * @var array $whitelist
	 */
	protected $whitelist;

	/**
	 * Construct a new {@link MatchFilter} from an array of patterns
	 * @param array $patterns The patterns to filter on
	 * @param int   $flags    The flags to pass to {@link fnmatch}
	 */
	public function __construct(array $patterns, $flags = 0) {
		$white = [];
		$black = [];

		foreach($patterns as $pattern) {
			if(substr($pattern, 0, 1) == '!') {
				$white[] = $this->generateRegex(substr($pattern, 1), $flags);
			} else {
				$black[] = $this->generateRegex($pattern, $flags);
			}
		}


		$this->whitelist = $white;
		$this->blacklist = $black;
	}

	/**
	 * Create a new {@link Filter} from a series of filters
	 * @param  string $filter
	 * @param  int    $flags  The flags to pass to {@link fnmatch}
	 * @return Filter The created filter
	 */
	public static function fromString($filter, $flags = 0) {
		return new static(explode(' ', $filter), $flags);
	}

	/**
	 * Filters as path
	 * @param  string $path The path of the file to filter on
	 * @return boolean If the path matches the filter
	 */
	public function passes($path) {
		foreach($this->whitelist as $pattern) {
			if(preg_match($pattern, $path)) {
				return true;
			}
		}

		foreach($this->blacklist as $pattern) {
			if(preg_match($pattern, $path)) {
				return false;
			}
		}

		return true;
	}

	public function generateRegex($pattern, $flags = 0) {
		// http://php.net/manual/en/function.fnmatch.php#100207
		$modifiers = 'S'; // Cache it for performance
		$transforms = array(
			'\*'   => '.*',
			'\?'   => '.',
			'\[\!' => '[^',
			'\['   => '[',
			'\]'   => ']',
			'\.'   => '\.',
			'\\'   => '\\\\'
		);

		// Forward slash in string must be in pattern:
		if ($flags & FNM_PATHNAME) {
			$transforms['\*'] = '[^/]*';
		}

		// Back slash should not be escaped:
		if ($flags & FNM_NOESCAPE) {
			unset($transforms['\\']);
		}

		// Perform case insensitive match:
		if ($flags & FNM_CASEFOLD) {
			$modifiers .= 'i';
		}

		// Period at start must be the same as pattern:
		if ($flags & FNM_PERIOD) {
			if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false;
		}

		// If we have a '/' then constrain it to the root directory
		$first = '(?:^|\/)';
		if(substr($pattern, 0, 1) == '/') {
			$first = '^';

			// And trim the prefix /
			$pattern = substr($pattern, 1);
		}

		return '#' . $first . strtr(preg_quote($pattern, '#'), $transforms) . '(?:$|\/)#' . $modifiers;
	}
}
