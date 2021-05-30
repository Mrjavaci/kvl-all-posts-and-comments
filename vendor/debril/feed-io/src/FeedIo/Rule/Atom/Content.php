<?php declare(strict_types=1);


namespace FeedIo\Rule\Atom;

use DomDocument;
use DOMElement;
use FeedIo\Feed\ItemInterface;
use FeedIo\Feed\NodeInterface;
use FeedIo\Rule\TextAbstract;

class Content extends TextAbstract
{
    const NODE_NAME = 'content';

    public function setProperty(NodeInterface $node, DOMElement $element): void
    {
        if ($node instanceof ItemInterface) {
            $node->setDescription(
                $this->getProcessedContent($element, $node)
            );
        }
    }

    protected function addElement(DomDocument $document, DOMElement $rootElement, NodeInterface $node) : void
    {
        if ($node instanceof ItemInterface) {
            $rootElement->appendChild(
                $this->generateElement($document, $node->getContent())
            );
        }
    }

    protected function hasValue(NodeInterface $node): bool
    {
        return !! $node->getDescription();
    }
}
