<?php

namespace silverorange\DevTest\Template;

use silverorange\DevTest\Context;

class PostIndex extends Layout
{
    protected function renderPage(Context $context): string
    {
        // @codingStandardsIgnoreStart
        $content = <<<HTML
        <div class="post_title_container">
            <h1 class="post_title center">{$context->title}</h1>
        </div>
        <div class="posts_list_container">
HTML;
        foreach($context->model as $post)
        {
            $content = $content . <<<HTML
                <a href="{$post->id}" class="post_list_block"><div class="post_list_title">
HTML;
            $content = $content . $post->title;
            $content = $content . <<<HTML
                </div><div class="post_list_author">
HTML;
            $content = $content . "By " . $post->author->full_name;
            $content = $content . <<<HTML
                </div><div class="post_list_date">
HTML;
            $content = $content . explode(' ', $post->modified_at)[0];
            $content = $content . <<<HTML
                </div></a>
HTML;
        }
        return $content . <<<HTML
        </div>
HTML;
        // @codingStandardsIgnoreEnd
    }
}
