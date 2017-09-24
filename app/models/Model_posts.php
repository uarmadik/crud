<?php


namespace app\models;
use app\core\Model;


class Model_posts extends Model
{
    public function getAllPosts($order_by = null)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        switch ($order_by){
            case 'date_asc':
                $sql = 'SELECT * FROM posts ORDER BY created_at ASC';
                break;
            case 'date_desc':
                $sql = 'SELECT * FROM posts ORDER BY created_at DESC';
                break;
            case 'header_asc':
                $sql = 'SELECT * FROM posts ORDER BY header ASC';
                break;
            case 'header_desc':
                $sql = 'SELECT * FROM posts ORDER BY header DESC';
                break;
            default :
                $sql = 'SELECT * FROM posts';
        }

        try {
            $connection = new \PDO($dsn,$user,$password);
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
        $header = $data['header'];
        $text = $data['text'];

        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;


        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "INSERT INTO posts VALUES (null,:header, :text, now(), NULL)";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['header'=>$header,
                            'text'=>$text]);

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
        $datetime = date("Y-m-d H:i");

        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "UPDATE posts SET header = :header, text = :text, edited_at = now() WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['header'=>$header, 'text'=>$text, 'id'=>$id]);

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }
}