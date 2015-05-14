<?php

$root = __DIR__ . '/..';

// Basic loading code
$loader = require $root.'/vendor/autoload.php';
$loader->addPsr4('SquidDev\\Tests\\', $root.'/tests');

use SquidDev\Deploy\Sources\FilesystemSource;
use SquidDev\Deploy\Visitors\DelegateVisitor;
use SquidDev\Deploy\Visitors\FilteringVisitor;
use SquidDev\Deploy\Filters\BlockedPackageFilter;
use SquidDev\Deploy\Filters\ComboFilter;
use SquidDev\Deploy\Filters\MatchFilter;

$visitor = new FilesystemSource($root);
$visitor->accept(new FilteringVisitor(
	new DelegateVisitor(function($path) {
		echo " - $path\n";
	}),
	new ComboFilter([
		BlockedPackageFilter::fromDir($root), // Exclude test packages
		new MatchFilter(['.*']) // Exclude .* (.git, .editorconfig, etc...)
	])
));
