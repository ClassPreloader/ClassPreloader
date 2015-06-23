<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ClassPreloader\ClassLoader;

return ClassLoader::getIncludes(function (ClassLoader $loader) {
    $loader->register();
    new Foo();
});
