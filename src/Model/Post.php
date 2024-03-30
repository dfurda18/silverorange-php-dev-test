<?php

namespace silverorange\DevTest\Model;

class Post
{
    private $insert = "INSERT INTO posts (id, title, body, created_at, modified_at, author) "
                        . "VALUES (:id, :title, :body, :created_at, :modified_at, :author) "
                        . "ON CONFLICT (id) "
                        . "DO UPDATE SET id = :id, title = :title, body = :body, created_at = :created_at, modified_at = :modified_at, author = :author";
    public string $id;
    public string $title;
    public string $body;
    public string $created_at;
    public string $modified_at;
    public string $author;

    public function save(\PDO $db)
    {
        $query = $this->insert;
        $params = [
            ':id' => $this->id,
            ':title' => $this->title,
            ':body' => $this->body,
            ':created_at' => $this->created_at,
            ':modified_at' => $this->modified_at,
            ':author' => $this->author];
        foreach($params as $key => $value) {
            $replacement = is_numeric($value) ? $value : "'{$value}'";
            $query = str_replace($key, $replacement, $query);
        }

        try {
            $insertQuery = $db->prepare($query);
            $insertQuery->execute();
        } catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }
}
