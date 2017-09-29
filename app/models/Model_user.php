<?php

namespace app\models;


use app\core\Model;

class Model_user extends Model
{
    public function get_user($login)
    {
        //var_dump($user);
        $user_login    = $login;
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;
        $sql = "SELECT * FROM users WHERE login = ?";


        try{
            $connection = new \PDO($dsn,$user,$password);
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_login]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                // user not exist!
                return false;
            }
        } catch (\Exception $e) {
            //return $e->getMessage();
            exit($e->getMessage());
        }

        return $user;
    }

    public function save_user($login, $password)
    {
        $user_login    = $login;
        $user_password = $password;
        $dsn = "$this->db_driver:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset";
        $user = $this->db_username;
        $password = $this->db_password;

        try{

            $connection = new \PDO($dsn,$user,$password);
            $sql = "INSERT INTO users VALUES (NULL, ?, ?, NULL)";
            $stmt = $connection->prepare($sql);
            $result = $stmt->execute([$user_login, $user_password]);
            if (!$result) {
                return false;
            }

        } catch (\Exception $e) {
            exit($e->getMessage());
        }
        return true;
    }
}