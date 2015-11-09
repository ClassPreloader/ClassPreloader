<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ClassPreloader\ClassLoader;
use ClassPreloader\Factory;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new Factory();

        $this->assertInstanceOf(ClassPreloader::class, $factory->create());
    }
}
