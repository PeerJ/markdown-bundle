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

![This is an image](http://example.com/image.jpg)

```
This is some <b>HTML</b> code.
```

<a href="http://example.com/">This link is allowed</a>

<a href="https://example.com/">This link is secure</a>

<a href="javascript:alert('XSS!')">This link is not allowed</a>


<!-- A comment, to say that the form below is not allowed -->
<form>
<input value="Not allowed">
</form>
END;

        $expected = <<<END
<p>This is a<sup>2</sup> paragraph.</p>
<p>This <sub>is</sub> <strong>another</strong> paragraph.</p>
<p><img src="http://example.com/image.jpg" alt="This is an image"></p>
<pre><code>This is some &lt;b&gt;HTML&lt;/b&gt; code.
</code></pre>
<p><a href="http://example.com/">This link is allowed</a></p>
<p><a href="https://example.com/">This link is secure</a></p>
<p><a>This link is not allowed</a></p>
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
