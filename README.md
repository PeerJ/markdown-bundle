# markdown-bundle

A Symfony bundle providing `markdown_block` and `markdown_inline` Twig filters, as well as a rich text editor for editing Markdown content.

The Markdown processor allows arbitrary HTML, so the output is sanitised using HTML Purifier.

CommonMark extensions provide support for superscript and subscript.

## Updating

The rich text editor uses third-party libraries for conversion between Markdown and HTML (in both directions). To update these, edit `bower.json` if necessary, then run `bower update`. Commit the updated files to this repository then create a new release.
