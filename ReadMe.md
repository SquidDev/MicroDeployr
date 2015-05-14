# MicroDeployr
A tiny way of uploading files to a website. I hacked this together in a
day because I needed something simple.

Spelling things like that is cool. Like bowties :bowtie:.

```php
$visitor = new FilesystemSource(__DIR__);
$visitor->accept(new FilteringVisitor(
	FTPVisitor::fromDetails('ftp.site.com', 'username', 'password', __DIR__),
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
