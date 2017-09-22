<?php


namespace app\models;
use app\core\Model;


class Model_posts extends Model
{
    public function getAllPosts()
    {
        try {
            //$connection = new \PDO('mysql:host=localhost; dbname=crud; charset=utf8','root','');
            $connection = new \PDO("$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset",
                                  "$this->db_username","$this->db_password");
            $sql = 'SELECT * FROM posts';
            $stmt = $connection->query($sql);
            $posts_array = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }

        return $posts_array;
    }

    /**
     * @param $id
     * @return array . One post by @param $id.
     */
    public function getPost($id)
    {
        try {
            $connection = new \PDO("$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset",
                                   "$this->db_username","$this->db_password");
            $sql = "SELECT * FROM posts WHERE id = $id";
            $stmt = $connection->query($sql);
            $post_array = $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
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
        try {
            $connection = new \PDO("$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset",
                                   "$this->db_username","$this->db_password");
            $sql = "INSERT INTO posts VALUES (null,$header, $text)";
            if ($connection->exec($sql)){
                return true;
            }
        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            $connection = new \PDO("$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset",
                                   "$this->db_username","$this->db_password");
            $sql = "DELETE FROM posts WHERE id = $id";
            if ($connection->exec($sql)){
                return true;
            }
        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
    }

    /**
     * Edit post by @param $post (with $id)
     * @param $post
     * @return bool
     */
    public function edit($post)
    {
        $id = '\'' . $post['id'] . '\'';
        $header = '\'' . $post['header'] .'\'';
        $text = '\'' . $post['text'] . '\'';

        try {
            $connection = new \PDO("$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset",
                                   "$this->db_username","$this->db_password");
            $sql = "UPDATE posts SET header = $header, text = $text WHERE id = $id";
            if ($connection->exec($sql)){
                return true;
            }
        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
    }

}