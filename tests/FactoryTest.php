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

use ClassPreloader\ClassPreloader;
use ClassPreloader\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new Factory();

        $this->assertInstanceOf(ClassPreloader::class, $factory->create());
    }
}
