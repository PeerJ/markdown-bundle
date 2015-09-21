<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

class SubscriptRenderer implements InlineRendererInterface
{
    /**
     * @param AbstractInline        $inline
     * @param HtmlRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, HtmlRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Subscript)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        return new HtmlElement('sub', [], $htmlRenderer->renderInlines($inline->getChildren()));
    }
}
