<?php

namespace ClassPreloader\Parser;

use PhpParser\NodeTraverser as BaseTraverser;

/**
 * Allows a filename to be set when visiting
 */
class NodeTraverser extends BaseTraverser
{
    public function traverseFile(array $nodes, $filename)
    {
        // Set the correct state on each visitor
        foreach ($this->visitors as $visitor) {
            if ($visitor instanceof AbstractNodeVisitor) {
                $visitor->setFilename($filename);
            }
        }

        return $this->traverse($nodes);
    }
}
