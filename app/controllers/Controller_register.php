<?php


namespace app\controllers;


use app\core\View;
use app\models\Model_user;

class Controller_register
{
    public function index()
    {
        $message = null;
        if ($_SESSION['alert_error']) {
            $message['alert_error'] = $_SESSION['alert_error'];
            unset($_SESSION['alert_error']);
        }

        $view = new View();
        $view->generate('general', 'register.html.twig',['message'=>$message]);
    }

    public function registration()
    {
        if ($_POST['password'] != $_POST['re_password']) {
            // Password did not confirm
            $_SESSION['alert_error'] = 'Password did not confirm!';
            header('Location: /register'); exit();
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if (!empty($_POST['login'])) {
            $login = $_POST['login'];
        } else {
            $_SESSION['alert_error'] = 'Empty login field!';
            header('Location: /register'); exit();
        }

        $db = new Model_user();
        $user = $db->get_user($login, $password);
        if ($user) {
            $_SESSION['alert_error'] = 'User already exist!';
            header('Location: /register'); exit();
        }

        if ($db->save_user($login, $password)) {
            $_SESSION['alert'] = 'Successful registration!';
            header('Location: /login'); exit();
        }


    }
}