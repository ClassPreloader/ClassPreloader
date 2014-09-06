<?php

namespace ClassPreloader\Parser;

use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\File;
use PhpParser\Node\Scalar\String;

/**
 * Finds all references to __FILE__ and replaces them with the actual file path
 */
class FileVisitor extends AbstractNodeVisitor
{
    public function enterNode(Node $node)
    {
        if ($node instanceof File) {
            return new String($this->getFilename());
        }
    }
}
