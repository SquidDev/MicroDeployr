<?php
namespace SquidDev\Deploy\Visitors;

/**
 * Defines a method of visiting files and directories
 */
interface IVisitor {
	/**
	 * Visit a directory
	 * @param  string The path of the directory
	 * @return IFileVisitor The visitor for the subdirectory
	 */
	public function visitDirectory($path);

	/**
	 * Visit a file.
	 * @param  string $path The path to upload
	 */
	public function visitFile($path);
}
