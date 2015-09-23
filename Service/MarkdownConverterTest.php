<?php

namespace peerj\MarkdownBundle\Service;

/**
 * Test Markdown conversion to HTML
 */
class MarkdownConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarkdownConverter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new MarkdownConverter();
    }

    /**
     * Test that a block of Markdown renders as paragraphs
     */
    public function testRenderBlock()
    {
        $input = <<<END
This is a^2^ paragraph.

This ~is~ **another** paragraph.
END;

        $expected = <<<END
<p>This is a<sup>2</sup> paragraph.</p>
<p>This <sub>is</sub> <strong>another</strong> paragraph.</p>

END;

        $output = $this->converter->renderBlock($input);

        $this->assertEquals($expected, $output);

    }

    /**
     * Test that a line of Markdown renders without paragraphs
     */
    public function testRenderInline()
    {
        $input = <<<END
This is a
*single* line
END;

        $expected = 'This is a <em>single</em> line';

        $output = $this->converter->renderInline($input);

        $this->assertEquals($expected, $output);
    }
}
