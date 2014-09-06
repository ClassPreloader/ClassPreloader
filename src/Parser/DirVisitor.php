<?php

namespace ClassPreloader\Parser;

use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String;

/**
 * Finds all references to __DIR__ and replaces them with the actual directory
 */
class DirVisitor extends AbstractNodeVisitor
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Dir) {
            return new String($this->getDir());
        }
    }
}
