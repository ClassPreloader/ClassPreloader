<?php

namespace ClassPreloader\Parser;

use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\File;
use PhpParser\Node\Scalar\String;

/**
 * This is the file node visitor class.
 *
 * This is used to replace all references to __FILE__ with the actual file.
 */
class FileVisitor extends AbstractNodeVisitor
{
    /**
     * Enter and modify the node.
     *
     * @param \PhpParser\Node $node
     *
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof File) {
            return new String($this->getFilename());
        }
    }
}
