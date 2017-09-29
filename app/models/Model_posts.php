<?php


namespace app\models;
use app\core\Model;


class Model_posts extends Model
{
    public function getAllPosts($order_by=null)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

            switch ($order_by) {
                case 'date_asc':
                    //$sql = 'SELECT * FROM posts ORDER BY created_at ASC';
                    $sql = 'SELECT posts.id, posts.header, posts.text, posts.created_at, posts.edited_at, users.login 
                        FROM posts INNER JOIN users ON posts.author_id=users.id';
                    break;
                case 'date_desc':
                    $sql = 'SELECT posts.id, posts.header, posts.text, posts.created_at, posts.edited_at, users.login 
                        FROM posts INNER JOIN users ON posts.author_id=users.id ORDER BY posts.created_at DESC';
                    break;
                case 'header_asc':
                    $sql = 'SELECT posts.id, posts.header, posts.text, posts.created_at, posts.edited_at, users.login 
                        FROM posts INNER JOIN users ON posts.author_id=users.id ORDER BY posts.header ASC';
                    break;
                case 'header_desc':
                    $sql = 'SELECT posts.id, posts.header, posts.text, posts.created_at, posts.edited_at, users.login 
                        FROM posts INNER JOIN users ON posts.author_id=users.id ORDER BY posts.header DESC';
                    break;
                default :
                    //$sql = 'SELECT * FROM posts';
                    $sql = 'SELECT posts.id, posts.header, posts.text, posts.created_at, posts.edited_at, users.login 
                        FROM posts INNER JOIN users ON posts.author_id=users.id';
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

    public function getAllPostsByUser($user_id, $order_by=null)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        switch ($order_by){
            case 'date_asc':
                $sql = 'SELECT * FROM posts WHERE author_id = ? ORDER BY created_at ASC';
                break;
            case 'date_desc':
                $sql = 'SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC';
                break;
            case 'header_asc':
                $sql = 'SELECT * FROM posts WHERE author_id = ? ORDER BY header ASC';
                break;
            case 'header_desc':
                $sql = 'SELECT * FROM posts WHERE author_id = ? ORDER BY header DESC';
                break;
            default :
                $sql = 'SELECT * FROM posts WHERE author_id = ? ORDER BY created_at ASC';
                break;
        }

        try {
            $connection = new \PDO($dsn,$user,$password);
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_id]);
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
    public function getPost($post_id, $user_id, $role_super_admin=null)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            if ($role_super_admin){
                $sql = "SELECT * FROM posts WHERE id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$post_id]);
                $post_array = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (empty($post_array)) {
                    return false;
                }
            } else {

                $sql = "SELECT * FROM posts WHERE id = ? AND author_id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$post_id, $user_id]);
                $post_array = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (empty($post_array)) {
                    return false;
                }
            }

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
        $author_id = $data['author_id'];

        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;


        try {
            $connection = new \PDO($dsn,$user,$password);
            $sql = "INSERT INTO posts VALUES (null,:header, :text, now(), NULL, :author_id)";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['header'    =>$header,
                            'text'      =>$text,
                            'author_id' =>$author_id]);

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($post_id, $user_id, $role_super_admin=null)
    {
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try {
            $connection = new \PDO($dsn,$user,$password);
            if ($role_super_admin) {

                $sql = "DELETE FROM posts WHERE id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$post_id]);
                $result = $stmt->rowCount();
                if ($result == 0) {
                    return false;
                }
            } else {
                $sql = "DELETE FROM posts WHERE id = ? AND author_id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$post_id, $user_id]);
                $result = $stmt->rowCount();
                if ($result == 0) {
                    return false;
                }
            }
            return true;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Edit post by @param $post (with $id)
     * @param $post
     * @return bool
     */
    public function edit($post, $user_id, $role_super_admin=false)
    {
        $post_id = $post['id'];
        $header = $post['header'];
        $text = $post['text'];

        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;
        $datetime = date("Y-m-d H:i");

        try {
            $connection = new \PDO($dsn,$user,$password);
            if ($role_super_admin) {
                $sql = "UPDATE posts SET header = :header, text = :text, edited_at = now() WHERE id = :post_id";
                $stmt = $connection->prepare($sql);
                $stmt->execute(['header'=>$header,
                                'text'=>$text,
                                'post_id'=>$post_id]);
                $result = $stmt->rowCount();
                if ($result == 0) {
                    return false;
                }
            } else {
                $sql = "UPDATE posts SET header = :header, text = :text, edited_at = now() WHERE id = :post_id AND author_id= :author_id";
                $stmt = $connection->prepare($sql);
                $stmt->execute(['header'=>$header,
                    'text'=>$text,
                    'post_id'=>$post_id,
                    'author_id'=>$user_id]);
                $result = $stmt->rowCount();
                if ($result == 0) {
                    return false;
                }
            }

            return true;

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }
}