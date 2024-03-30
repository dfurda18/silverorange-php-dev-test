<?php

namespace silverorange\DevTest\Model;

class Author
{
    const FIND_QUERY = "SELECT * FROM authors WHERE id = :id";
    public string $id;
    public string $full_name;
    public string $created_at;
    public string $modified_at;

    public static function find(\PDO $db, string $id)
    {
        try {
            $findQuery = $db->prepare(Author::replaceValuesInQuery(Author::FIND_QUERY, [':id' => $id]));
            $findQuery->execute();
            $author_details = $findQuery->fetch();
            $author = new Author();
            $author->id = $author_details['id'];
            $author->full_name = $author_details['full_name'] ?? '';
            $author->created_at = $author_details['created_at'] ?? '';
            $author->modified_at = $author_details['modified_at'] ?? '';
            return $author;
        } catch (\PDOException $e)
        {
            throw new \PDOException($e->getMessage());
        }

    }

    private static function replaceValuesInQuery(string $query, array $values)
    {
        foreach($values as $key => $value) {
            $replacement = is_numeric($value) ? $value : "'{$value}'";
            $query = str_replace($key, $replacement, $query);
        }
        return $query;
    }
}
