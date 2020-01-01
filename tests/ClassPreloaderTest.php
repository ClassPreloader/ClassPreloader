<?php

declare(strict_types=1);

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassPreloader\Tests;

use ClassPreloader\CodeGenerator;
use ClassPreloader\Parser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use PHPUnit\Framework\TestCase;

class ClassPreloaderTest extends TestCase
{
    /**
     * This tests the correct detection and stripping of strict_types declarations while preloading files.
     */
    public function testStripStrictTypeDeclaration()
    {
        $printer = $this->getMockBuilder(PrettyPrinter::class)
            ->disableOriginalConstructor()
            ->setMethods(['prettyPrint']);
        $parser = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->setMethods(['parse', 'getErrors']);
        $traverser = $this->getMockBuilder(NodeTraverser::class)
            ->disableOriginalConstructor()
            ->setMethods(['traverseFile']);

        $parserMock = $parser->getMock();
        $parserMock->expects($this->once())
            ->method('parse')
            ->willReturn([]);
        $traverserMock = $traverser->getMock();
        $traverserMock->expects($this->once())
            ->method('traverseFile')
            ->willReturn([]);
        $printerMock = $printer->getMock();
        $printerMock->expects($this->once())
            ->method('prettyPrint')
            ->willReturn(
                file_get_contents(__DIR__.'/stubs/StrictClassWithComments.php')
            );

        $classPreloader = new CodeGenerator(
            $printerMock,
            $parserMock,
            $traverserMock
        );

        $code = $classPreloader->getCode(__DIR__.'/stubs/StrictClassWithComments.php');

        // $code should not have 'declare(strict_types=1)' declarations.
        $this->assertNotRegExp(
            '/(.*?)declare\s*\(strict_types\s*=\s*1\)(.*?)/mi',
            $code,
            'Generated ClassPreloader output should correctly detect and strip strict_type declare statements.'
        );
    }
}
