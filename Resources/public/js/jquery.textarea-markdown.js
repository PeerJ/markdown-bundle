(function($) {
    'use strict';

    var toMarkdownOptions = {
        gfm: true,
        converters: [
            {
                filter: 'sup',
                replacement: function (content) {
                    return '^' + content + '^';
                }
            },
            {
                filter: 'sub',
                replacement: function (content) {
                    return '~' + content + '~';
                }
            }
        ]
    };

    var md = window.markdownit({
        html: false,
        breaks: true,
        linkify: true,
        typographer: true
    });

    md.use(window.markdownitSub);
    md.use(window.markdownitSup);
    md.use(window.markdownitSanitizer);

    $.fn.markdown = function() {
        return this.each(function () {
            var input = $(this);
            var parent = input.closest('.textarea-container');
            var preview = parent.find('.textarea-preview');
            var inputNode = input.get(0);
            var inputHeight = input.height();
            var singleLine = !!input.data('single-line');

            var updateInput = function () {
                var scrollHeight;
                var html = preview.html();
                var markdown = window.toMarkdown(html, toMarkdownOptions);
                markdown = markdown.replace(/(<\/?[a-z]([^>]+)>)/ig, ''); // no markup
                if (singleLine) {
                    markdown = markdown.replace(/^(\s|#)+/, ''); // no heading
                }
                input.val(markdown);

                inputNode.checkValidity();

                if (inputNode.validity.valid) {
                    preview.removeClass('textarea-preview-invalid');
                } else {
                    preview.addClass('textarea-preview-invalid');
                }

                scrollHeight = input.prop('scrollHeight');
                input.scrollTop(scrollHeight);
            };

            var updatePreview = function () {
                var scrollHeight,
                    markdown = input.val(),
                    html = markdown ? md.render(markdown) : '';

                preview.html(html);
                scrollHeight = preview.prop('scrollHeight');
                preview.scrollTop(scrollHeight);
            };

            var handlePaste = function (event) {
                var html,
                    text,
                    markdown,
                    actualEvent = (event.originalEvent || event);

                html = actualEvent.clipboardData.getData('text/html') || window.prompt('Paste something..');

                // TODO: use CSS styles

                // convert to Markdown
                markdown = window.toMarkdown(html, toMarkdownOptions);
                markdown = markdown.replace(/(<\/?[a-z]([^>]+)>)/ig, '');

                // convert back again
                html = md.render(markdown);
                //html = html.replace(/(<\/?[a-z]([^>]+)>)/ig, '');

                //noinspection UnusedCatchParameterJS
                try {
                    document.execCommand('insertHTML', false, html);
                } catch (e) {
                    // IE doesn't support insertHTML - use Range?
                    text = actualEvent.clipboardData.getData('text/plain') || window.prompt('Paste something..');

                    document.execCommand('insertText', false, text);
                }

                return false;
            };

            parent.find('.nav-tabs').removeAttr('hidden').find('[role=tab]').first().click();

            preview.css('min-height', inputHeight);

            updatePreview();
            input.on('blur', updatePreview);

            preview.on('blur', updateInput).on('paste', handlePaste);

            parent.find('.command-button').on('click', function() {
                var command = $(this).data('command');
                var argument = null;

                var parts = command.split(/_/);

                if (parts.length > 1) {
                    command = parts[0];
                    argument = parts[1];
                }

                document.execCommand(command, false, argument);
            });

            // tooltips
            parent.find('[data-toggle=tooltip]').tooltip({
                container: parent
            });
        });
    };
})(jQuery);
