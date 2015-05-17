<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return ClassPreloader\ClassLoader::getIncludes(function (ClassPreloader\ClassLoader $loader) {
    $loader->register();
    new Foo();
});
