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

namespace ClassPreloader;

use ClassPreloader\Commands\PreCompileCommand;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * This is the application class.
 *
 * This is sets everything up for the CLI.
 */
class Application extends BaseApplication
{
    /**
     * Create a new application.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Class Preloader', '2.0');

        $this->add(new PreCompileCommand());
    }
}
