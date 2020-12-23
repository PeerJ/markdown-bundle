<?php

namespace peerj\MarkdownBundle\CommonMark;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Inline\Element\AbstractStringContainer;
use League\CommonMark\Inline\Element\Text;

/**
 *
 */
abstract class AbstractSubSupProcessor implements DelimiterProcessorInterface
{
    /**
     * Determine how many (if any) of the delimiter characters should be used.
     *
     * This allows implementations to decide how many characters to be used
     * based on the properties of the delimiter runs. An implementation can also
     * return 0 when it doesn't want to allow this particular combination of
     * delimiter runs.
     *
     * @param DelimiterInterface $opener The opening delimiter run
     * @param DelimiterInterface $closer The closing delimiter run
     *
     * @return int
     */
    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        return 1;
    }

    /**
     * Process the matched delimiters, e.g. by wrapping the nodes between opener
     * and closer in a new node, or appending a new node after the opener.
     *
     * Note that removal of the delimiter from the delimiter nodes and detaching
     * them is done by the caller.
     *
     * @param AbstractStringContainer $opener       The node that contained the opening delimiter
     * @param AbstractStringContainer $closer       The node that contained the closing delimiter
     * @param int                     $delimiterUse The number of delimiters that were used
     *
     * @return void
     */
    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse)
    {
        $el = $this->createElement();

        $next = $opener->next();
        while ($next !== null && $next !== $closer) {
            $tmp = $next->next();
            $el->appendChild($next);
            $next = $tmp;
        }

        $opener->insertAfter($el);
    }

    /**
     * @deprecated
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
