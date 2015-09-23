<?php
/**
 *
 */

namespace peerj\MarkdownBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarkdownConverterTest extends KernelTestCase
{
    /**
     * @var MarkdownConverter
     */
    private $converter;

    public function setUp()
    {
        self::bootKernel();

        $this->converter = static::$kernel->getContainer()->get('peerj_markdown.markdown_converter');
    }

    /**
     * Test that a block of Markdown renders as paragraphs
     */
    public function testRenderBlock()
    {
        $input = <<<END
This is a paragraph.

This is **another** paragraph.
END;

        $expected = <<<END
<p>This is a paragraph.</p>
<p>This is <strong>another</strong> paragraph.</p>

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
