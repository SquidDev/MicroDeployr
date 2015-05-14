<?php
namespace SquidDev\Deploy\Sources;

use SquidDev\Deploy\Visitors\IVisitor;

/**
 * The main visitor that uses the file system.
 */
class FilesystemSource implements ISource {
	/**
	 * The main base local directory
	 * @var string $root
	 */
	protected $root;

	public function __construct($root) {
		if(substr($root, -1) != '/') $root .= '/';
		$this->root = $root;
	}

	public function accept(IVisitor $visitor) {
		$this->visitDirectory('', $visitor);
	}

	protected function visitDirectory($path, $visitor) {
		// Append path
		if($path != '' && substr($path, -1) != '/') $path .= '/';

		$handle = dir($this->root . $path);
		while (($entry = $handle->read()) !== false) {
			if($entry == '.' || $entry == '..') continue;

			$entry = $path . $entry;

			if(is_dir($this->root . $entry)) {
				$child = $visitor->visitDirectory($entry);
				if($child != NULL) $this->visitDirectory($entry, $child);
			} else {
				$visitor->visitFile($entry);
			}
		}
		$handle->close();
	}
}
