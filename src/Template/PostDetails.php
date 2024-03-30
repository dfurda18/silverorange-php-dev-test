<?php

namespace silverorange\DevTest\Template;

use silverorange\DevTest\Context;

class PostDetails extends Layout
{
    protected function renderPage(Context $context): string
    {
        // @codingStandardsIgnoreStart
        return <<<HTML
        <div class="post_title_container">
            <h1 class="post_title">{$context->title}<span class="author"> by {$context->author_full_name}</span></h1>
            <p class="post_date">{$context->modified_at}</p>
        </div>
        <div>{$context->content}</div>
HTML;
        // @codingStandardsIgnoreEnd
    }
}
