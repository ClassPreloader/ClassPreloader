Class Preloader for PHP
=======================

This tool is used to generate a single PHP script containing all of the classes
required for a specific use case. Using a single compiled PHP script instead of relying on autoloading can help to improve the performance of specific use cases. For example, if your application executes the same bootstrap code on every request, then you could generate a preloader (the compiled output of this tool) to reduce the cost of autoloading the required classes over and over.

![Banner](https://user-images.githubusercontent.com/2829600/71563674-7467c580-2a8b-11ea-8776-4bb143a03e47.png)


What it actually does
---------------------

This tool listens for each file that is autoloaded, creates a list of files, traverses the parsed PHP file using [PHP Parser](https://github.com/nikic/PHP-Parser) and any visitors of a Config object, wraps the code of each file in a namespace block if necessary, and writes the contents of every autoloaded file (in order) to a single PHP file.

Notice
------

This tool should only be used for specific use cases. There is a tradeoff between preloading classes and autoloading classes. The point at which it is no longer beneficial to generate a preloader is application specific. You'll need to perform your own benchmarks to determine if this tool will speed up your application.

Installation
------------

Add ClassPreloader as a dependency to your composer.json file by adding `"classpreloader/classpreloader": "^4.1"` to your require block. Note that if you want to use the cli tool, then you need to also add `"classpreloader/console": "^3.1"` to the require block.

Using the tool
--------------

You use the `./vendor/bin/classpreloader` compile command with a few command line flags to generate a preloader.

`--config`: A CSV containing a list of files to combine into a classmap, or the full path to a PHP script that returns an array of classes or a `ClassPreloader\ClassLoader\Config` object.

`--output`: The path to the file to store the compiled PHP code. If the directory does not exist, the tool will attempt to create it.

`--skip_dir_file`: (no value) Skip files with `__DIR__` or `__FILE__` to make the cache portable.

`--fix_dir`: (defaults to 1) Set to 0 to not replace `__DIR__` constants with the actual directory of the original file.

`--fix_file`: (defaults to 1) Set to 0 to not replace `__FILE__` constants with the actual location of the original file.

`--strict_types`: (defaults to 0) Set to 1 to enable strict types mode.

`--strip_comments`: (defaults to 0) Set to 1 to strip comments from each source file.

Writing a config file
---------------------

Creating a PHP based configuration file is fairly simple. Just include the `vendor/classpreloader/classpreloader/src/ClassLoader.php` file and call the `ClassPreloader\ClassLoader::getIncludes()` method, passing a function as the only  argument. This function should accept a `ClassPreloader\ClassLoader` object and register the passed in object's autoloader using `$loader->register()`. It is important to register the `ClassPreloader\ClassLoader` autoloader after all other autoloaders are registered.

An array or `ClassPreloader\ClassLoader\Config` must be returned from the config file. You can attach custom node visitors if you need to perform any sort of translation on each matching file before writing it to the output.

```php
<?php

// Here's an example of creating a preloader for using the
// Amazon DynamoDB and the AWS SDK for PHP 2.

require __DIR__.'/src/Config.php';
require __DIR__.'/src/ClassNode.php';
require __DIR__.'/src/ClassList.php';
require __DIR__.'/src/ClassLoader.php';

use ClassPreloader\ClassLoader;

$config = ClassLoader::getIncludes(function (ClassLoader $loader) {
    require __DIR__.'/vendor/autoload.php';
    $loader->register();
    $aws = Aws\Common\Aws::factory([
        'key'    => '***',
        'secret' => '***',
        'region' => 'us-east-1'
    ]);
    $client = $aws->get('dynamodb');
    $client->listTables()->getAll();
});

// Add a regex filter that requires all classes to match the regex.
// $config->addInclusiveFilter('/Foo/');

// Add a regex filter that requires that a class does not match the filter.
// $config->addExclusiveFilter('/Foo/');

return $config;
```

You would then run the classpreloader script and pass in the full path to the above PHP script.

`./vendor/bin/classpreloader compile --config="/path/to/the_example.php" --output="/tmp/preloader.php"`

The above command will create a file in /tmp/preloader.php that contains every file that was autoloaded while running the snippet of code in the anonymous function. You would generate this file and include it in your production script.

Automating the process with Composer
------------------------------------

You can automate the process of creating preloaders using Composer's script functionality. For example, if you wanted to automatically create a preloader each time the AWS SDK for PHP is installed, you could define a script like the following in your composer.json file:

```json
{
    "require": {
        "classpreloader/console": "^3.1"
    },
    "scripts": {
        "post-autoload-dump": "@php vendor/bin/classpreloader compile --config=/path/to/the_example.php --output=/path/to/preload.php"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

Using the above composer.json file, each time the project's autoloader is recreated using the install or update command, the classpreloader.php file will be executed. This script would generate a preload.php containing the classes required to run the previously demonstrated "the_example.php" configuration file.

Security
--------

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/ClassPreloader/ClassPreloader/security/policy).

License
-------

Class Preloader is licensed under [The MIT License (MIT)](LICENSE).

For Enterprise
--------------

Available as part of the Tidelift Subscription

The maintainers of `classpreloader/classpreloader` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-classpreloader-classpreloader?utm_source=packagist-classpreloader-classpreloader&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
