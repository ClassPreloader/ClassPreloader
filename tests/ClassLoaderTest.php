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

use ClassPreloader\ClassLoader;

class ClassLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testGetSingleInclude()
    {
        $config = ClassLoader::getIncludes(function (ClassLoader $loader) {
            $loader->register();
            new Bar();
        });

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Bar.php',
        );

        $this->assertInstanceOf('ClassPreloader\Config', $config);
        $this->assertSame($expected, $config->getFilenames());
    }

    /**
     * @depends testGetSingleInclude
     */
    public function testGetManyIncludes()
    {
        $config = ClassLoader::getIncludes(function (ClassLoader $loader) {
            $loader->register();
            new Foo();
        });

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Bar.php',
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Foo.php',
        );

        $this->assertInstanceOf('ClassPreloader\Config', $config);
        $this->assertSame($expected, $config->getFilenames());
    }

    public function testGetIncludesFailed()
    {
        $config = ClassLoader::getIncludes(function (ClassLoader $loader) {
            // forget to call register
            new Foo();
        });

        $this->assertEmpty($config->getFilenames());
    }

    public function testLoadOneClass()
    {
        $loader = new ClassLoader();
        $loader->register();

        $loader->loadClass('Bar');

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Bar.php',
        );

        $this->assertSame($expected, $loader->getFilenames());
    }

    /**
     * @depends testLoadOneClass
     */
    public function testLoadManyClasses()
    {
        $loader = new ClassLoader();
        $loader->register();

        $loader->loadClass('Foo');

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Bar.php',
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Foo.php',
        );

        $this->assertSame($expected, $loader->getFilenames());

        // now we've loaded phpunit classes, and we haven't unregistered yet

        $files = $loader->getFilenames();

        $this->assertContains($expected[0], $files);
        $this->assertContains($expected[1], $files);
        $this->assertNotSame($expected, $loader->getFilenames());
    }

    public function testLoadManyClassesWithoutLoader()
    {
        $loader = new ClassLoader();

        $loader->loadClass('Foo');

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Foo.php',
        );

        $this->assertSame($expected, $loader->getFilenames());
    }

    /**
     * @depends testLoadManyClasses
     * @depends testLoadManyClassesWithoutLoader
     */
    public function testUnregister()
    {
        $loader = new ClassLoader();
        $loader->register();

        $loader->loadClass('Foo');

        $loader->unregister();

        $this->assertTrue(true); // force load in phpunit classes

        $expected = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Bar.php',
            __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Foo.php',
        );

        $this->assertSame($expected, $loader->getFilenames()); // phpunit classes not present
    }
}
