<?php

namespace ClassPreloader\Parser;

use ClassPreloader\Exception\SkipFileException;
use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String;

/**
 * This is the directory node visitor class.
 *
 * This is used to replace all references to __DIR__ with the actual directory.
 */
class DirVisitor extends AbstractNodeVisitor
{
    protected $throwException = false;
    
    public function __construct($mode = false)
    {
        $this->throwException = $mode;
    }
    
    /**
     * Enter and modify the node.
     *
     * @param \PhpParser\Node $node
     *
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Dir) {
            if ($this->throwException === true) {
                throw new SkipFileException('__DIR__ constant found, skipping...');
            }
            
            return new String($this->getDir());
        }
    }
}
