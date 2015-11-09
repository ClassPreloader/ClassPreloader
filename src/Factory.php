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

namespace ClassPreloader;

use ClassPreloader\Parser\DirVisitor;
use ClassPreloader\Parser\FileVisitor;
use ClassPreloader\Parser\NodeTraverser;
use ClassPreloader\Parser\StrictTypesVisitor;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

/**
 * This is the class preloader factory class.
 *
 * This class is a simple way to create a class preloader instance.
 */
class Factory
{
    /**
     * Create a new class preloader instance.
     *
     * @param bool[] $options
     *
     * @return \ClassPreloader\ClassPreloader
     */
    public function create(array $options = [])
    {
        $printer = new PrettyPrinter();

        $parser = $this->getParser();

        $options = array_merge(['dir' => true, 'file' => true, 'skip' => false, 'strict' => false], $options);

        $traverser = $this->getTraverser($options['dir'], $options['file'], $options['skip'], $options['strict']);

        $preloader = new ClassPreloader($printer, $parser, $traverser);
    }

    /**
     * Get the parser to use.
     *
     * @return \PhpParser\Parser
     */
    protected function getParser()
    {
        if (class_exists(ParserFactory::class)) {
            return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        }

        return new Parser(new Lexer());
    }

    /**
     * Get the node traverser to use.
     *
     * @param bool $dir
     * @param bool $file
     * @param bool $skip
     * @param bool $strict
     *
     * @return \ClassPreloader\Parser\NodeTraverser
     */
    protected function getTraverser($dir, $file, $skip, $strict)
    {
        $options = array_merge(['dir' => true, 'file' => true, 'skip' => false, 'strict' => false], $options);

        $traverser = new NodeTraverser();

        $skip = $input->getOption('skip_dir_file');

        if ($options['dir']) {
            $traverser->addVisitor(new DirVisitor($options['skip']));
        }

        if ($options['file']) {
            $traverser->addVisitor(new FileVisitor($options['skip']));
        }

        if ($options['strict']) {
            $traverser->addVisitor(new StrictTypesVisitor());
        }

        return $traverser;
    }
}
