<?php

namespace silverorange\DevTest\Model;

class Post
{
    public const SAVE_QUERY = "INSERT INTO posts (id, title, body, created_at, modified_at, author) "
        . "VALUES (:id, :title, :body, :created_at, :modified_at, :author) "
        . "ON CONFLICT (id) "
        . "DO UPDATE SET id = :id, title = :title, body = :body, "
        . "created_at = :created_at, modified_at = :modified_at, author = :author";
    public const FIND_QUERY = "SELECT * FROM posts WHERE id = :id";
    public const ALL_QUERY = "SELECT * FROM posts";
    public string $id;
    public string $title;
    public string $body;
    public string $created_at;
    public string $modified_at;
    public string $author_id;
    public Author $author;

    public function save(\PDO $db)
    {
        $params = [
            ':id' => $this->id,
            ':title' => $this->title,
            ':body' => $this->body,
            ':created_at' => $this->created_at,
            ':modified_at' => $this->modified_at,
            ':author' => $this->author_id];
        try {
            $insertQuery = $db->prepare(self::replaceValuesInQuery(self::SAVE_QUERY, $params));
            $insertQuery->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function find(\PDO $db, string $id)
    {
        try {
            $findQuery = $db->prepare(Post::replaceValuesInQuery(Post::FIND_QUERY, [':id' => $id]));
            $findQuery->execute();
            $post_details = $findQuery->fetch();
            return Post::load($db, $post_details);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        }
    }

    public static function all(\PDO $db)
    {
        $posts = [];
        try {
            $allQuery = $db->prepare(Post::replaceValuesInQuery(Post::ALL_QUERY, []));
            $allQuery->execute();

            while ($post_details = $allQuery->fetch()) {
                array_push($posts, Post::load($db, $post_details));
            }
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        }
        return $posts;
    }

    private static function replaceValuesInQuery(string $query, array $values)
    {
        foreach ($values as $key => $value) {
            $replacement = is_numeric($value) ? $value : "'{$value}'";
            $query = str_replace($key, $replacement, $query);
        }
        return $query;
    }

    private static function load($db, $post_details)
    {
        $post = new Post();
        $post->id = $post_details['id'];
        $post->title = $post_details['title'] ?? '';
        $post->body = $post_details['body'] ?? '';
        $post->created_at = $post_details['created_at'] ?? '';
        $post->modified_at = $post_details['modified_at'] ?? '';
        $post->author_id = $post_details['author'] ?? '';
        if (!empty($post->author_id)) {
            try {
                $post->author = Author::find($db, $post->author_id);
            } catch (\PDOException $e) {
                $post->author = null;
            }
        }
        return $post;
    }
}
