# MicroDeployr
A tiny way of uploading files to a website, because spelling
things like that is cool.

```php
$visitor = new FilesystemSource(__DIR__);
$visitor->accept(new FilteringVisitor(
	new ComboFilter([
		BlockedPackageFilter::fromDir(__DIR__), // Exclude test packages
		new MatchFilter([
            '.*', // Exclude .* (.git, .editorconfig, etc...)
            '!.htaccess' // But still upload .htaccess files
        ]),
        new MTimeFilter($time), // Only upload modified files based on MTime
	])
));
```

Not really intended for public use, but you can if you really want.
I tend to plug it into artisan to allow me to deploy changes.
