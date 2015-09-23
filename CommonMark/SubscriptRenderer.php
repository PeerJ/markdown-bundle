<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * Render subscript as <sub>…</sub>
 */
class SubscriptRenderer implements InlineRendererInterface
{
    /**
     * @param AbstractInline           $inline
     * @param ElementRendererInterface $elementRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $elementRenderer)
    {
        if (!($inline instanceof Subscript)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $content = $elementRenderer->renderInlines($inline->children());

        return new HtmlElement('sub', [], $content);
    }
}
