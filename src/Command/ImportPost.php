<?php

namespace silverorange\DevTest\Command;

require_once 'src/Model/Post.php';
require_once 'src/Config.php';
require_once 'src/Database.php';

use Exception;
use silverorange\DevTest\Model\Post;
use silverorange\DevTest\Config;
use silverorange\DevTest\Database;

class ImportPost
{
    /**
     * The content
     */
    private $content;
    /**
     * The importing messages
     */
    private $messages;
    /**
     * The optional columns
     */
    private $optional = ['title', 'body', 'created_at', 'modified_at', 'author'];
    /**
     * The required columns
     */
    private $required = ['id'];

    /**
     * Sets the path
     */
    public function __construct()
    {
    }

    /**
     * Sets the importe's path
     */
    public function import(string $path)
    {
        if (is_dir($path)) {
            if (substr($path, -1) != '/') {
                $path = $path . '/';
            }
            $files = glob($path . "*.json");
            $this->content = [];
            foreach ($files as $file) {
                $this->addContent($file);
            }
        } elseif (file_exists($path)) {
            $this->addContent($path);
        } else {
            $this->addRaw('input', $path);
        }
        $this->save();
        return $this->messages;
        //throw new Exception('The path: ' . $path . ' is not valid');
    }

    /**
     * Adds raw content
     */
    private function addRaw($file, $json)
    {
        $missing_required = [];
        $missing_optional = [];
        foreach ($this->required as $required_field) {
            if (!isset($json->{$required_field})) {
                array_push($missing_required, $required_field);
            }
        }
        if (sizeof($missing_required) == 0) {
            foreach ($this->optional as $optional_field) {
                if (!isset($json->{$optional_field})) {
                    array_push($missing_optional, $required_field);
                }
            }
            if (sizeof($missing_required) == 0) {
                $this->messages[$file] = "Was successfuly added to the database.";
            } else {
                $this->messages[$file] = "Doesn't have "
                                        . implode(', ', $missing_optional)
                                        . ", although, it was successfuly added to the database.";
            }
            $post = new Post();
            $post->id = $json->id;
            $post->title = $json->title ?? '';
            $post->body = $json->body ?? '';
            $post->created_at = $json->created_at ?? '';
            $post->modified_at = $json->modified_at ?? '';
            $post->author_id = $json->author ?? '';
            $this->content[$file] = $post;
        } else {
            $this->messages[$file] = "Doesn't have " . implode(', ', $missing_required) . ", it could not be imported.";
        }
    }

    /**
     * Adds content from a file
     */
    private function addContent(string $file)
    {
        $json = json_decode(file_get_contents($file));
        $this->addRaw($file, $json);
    }

    /**
     * Saves the content
     */
    public function save()
    {
        if (sizeof($this->content) > 0) {
            $config = new Config();
            $db = (new Database($config->dsn))->getConnection();
            foreach ($this->content as $file => $post) {
                try {
                    $post->save($db);
                } catch (Exception $e) {
                    $this->messages[$file] = $e->getMessage();
                }
            }
        }
    }
}
