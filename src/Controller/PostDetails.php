<?php

namespace silverorange\DevTest\Controller;

use Parsedown;
use silverorange\DevTest\Context;
use silverorange\DevTest\Template;
use silverorange\DevTest\Model;

class PostDetails extends Controller
{
    private ?Model\Post $post = null;

    public function getContext(): Context
    {
        $context = new Context();

        if ($this->post === null) {
            $context->title = 'Not Found';
            $context->content = "A post with id {$this->params[0]} was not found.";
        } else {
            $Parsedown = new Parsedown();
            $context->title = $this->post->title;
            $context->author_full_name = $this->post->author->full_name;
            $context->modified_at = explode(' ', $this->post->modified_at)[0];
            $context->content = $Parsedown->text($this->post->body);
        }

        return $context;
    }

    public function getTemplate(): Template\Template
    {
        if ($this->post === null) {
            return new Template\NotFound();
        }

        return new Template\PostDetails();
    }

    public function getStatus(): string
    {
        if ($this->post === null) {
            return $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found';
        }

        return $_SERVER['SERVER_PROTOCOL'] . ' 200 OK';
    }

    protected function loadData(): void
    {
        // TODO: Load post from database here. $this->params[0] is the post id.
        try
        {
            $this->post = Model\Post::find($this->db, $this->params[0]);
        } catch (\PDOException $e)
        {
            $this->post = null;
        }

    }
}
