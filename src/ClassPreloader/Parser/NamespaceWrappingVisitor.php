<?php

namespace ClassPreloader\Parser;

/**
 * Ensures all code is wrapped in a namespace.
 */
class NamespaceWrappingVisitor extends AbstractNodeVisitor
{
    /**
     * @var boolean
     */
    protected $namespaceDepth = 0;

    /**
     * {@inheritdoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            ++$this->namespaceDepth;
        }

        // Wrap code in empty namespace
        if ($this->namespaceDepth === 0) {
            return new \PHPParser_Node_Stmt_Namespace(
                new \PHPParser_Node_Name(""),
                array($node)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            --$this->inNamespace;
        }
    }
}
