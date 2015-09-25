<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Inline\Element\AbstractInlineContainer;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;

/**
 *
 */
abstract class AbstractSubSupProcessor implements InlineProcessorInterface
{
    /**
     * @return array
     */
    abstract protected function getCharacters();

    /**
     * @return AbstractInlineContainer
     */
    abstract protected function createElement();

    /**
     * @param DelimiterStack $delimiterStack
     * @param Delimiter      $stackBottom
     *
     * @return void
     */
    public function processInlines(DelimiterStack $delimiterStack, Delimiter $stackBottom = null)
    {
        $callback = function (Delimiter $opener, Delimiter $closer, DelimiterStack $stack) {
            /** @var Text $openerInline */
            $openerInline = $opener->getInlineNode();
            /** @var Text $closerInline */
            $closerInline = $closer->getInlineNode();

            // Remove used delimiters from stack elts and inlines
            $opener->setNumDelims($opener->getNumDelims() - 1);
            $closer->setNumDelims($closer->getNumDelims() - 1);

            $openerInline->setContent(substr($openerInline->getContent(), 0, -1));
            $closerInline->setContent(substr($closerInline->getContent(), 0, -1));

            // Build contents for new element
            $el = $this->createElement();

            $openerInline->insertAfter($el);
            while (($node = $el->next()) !== $closerInline) {
                $el->appendChild($node);
            }

            // Remove elts btw opener and closer in delimiters stack
            $tempStack = $closer->getPrevious();
            while ($tempStack !== null && $tempStack !== $opener) {
                $nextStack = $tempStack->getPrevious();
                $stack->removeDelimiter($tempStack);
                $tempStack = $nextStack;
            }
            // If opener has 0 delims, remove it and the inline
            if ($opener->getNumDelims() === 0) {
                $openerInline->detach();
                $stack->removeDelimiter($opener);
            }
            if ($closer->getNumDelims() === 0) {
                $closerInline->detach();
                $tempStack = $closer->getNext();
                $stack->removeDelimiter($closer);

                return $tempStack;
            }

            return $closer;
        };

        // Process the subscript characters
        $delimiterStack->iterateByCharacters($this->getCharacters(), $callback, $stackBottom);
    }
}
