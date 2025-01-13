<?php

declare(strict_types=1);

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GrahamCampbell\Analyzer\AnalysisTrait;
use PHPUnit\Framework\TestCase;

var_dump((new ReflectionClass(AnalysisTrait::class))->getMethod('getPaths')->isStatic());

if ((new ReflectionClass(AnalysisTrait::class))->getMethod('getPaths')->isStatic()) {
    class AnalysisTest extends TestCase
    {
        use AnalysisTrait;

        /**
         * Get the code paths to analyze.
         *
         * @return string[]
         */
        protected static function getPaths(): array
        {
            return [
                realpath(__DIR__.'/../src'),
                realpath(__DIR__),
            ];
        }
    }
} else {
    class AnalysisTest extends TestCase
    {
        use AnalysisTrait;

        /**
         * Get the code paths to analyze.
         *
         * @return string[]
         */
        protected function getPaths()
        {
            return [
                realpath(__DIR__.'/../src'),
                realpath(__DIR__),
            ];
        }
    }
}
