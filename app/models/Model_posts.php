<?php


namespace app\models;
use app\core\Model;


class Model_posts extends Model
{
    public function getAllPosts()
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = 'SELECT * FROM posts';
            $stmt = $connection->query($sql);
            $posts_array = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $posts_array;
    }

    /**
     * @param $id
     * @return array . One post by @param $id.
     */
    public function getPost($id)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "SELECT * FROM posts WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);
            $post_array = $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $post_array;
    }

    /**
     * Save new post into table;
     * @param $data. Data from form 'Create new post';
     * @return bool
     */
    public function store($data)
    {
        $header = '\'' . $data['header'] . '\'';
        $text = '\'' . $data['text'] . '\'';

        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;


        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "INSERT INTO posts VALUES (null,:header, :text)";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['header'=>$header, 'text'=>$text]);

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "DELETE FROM posts WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Edit post by @param $post (with $id)
     * @param $post
     * @return bool
     */
    public function edit($post)
    {
        $id = $post['id'];
        $header = $post['header'];
        $text = $post['text'];

        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "UPDATE posts SET header = :header, text = :text WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['header'=>$header, 'text'=>$text, 'id'=>$id]);

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }
}