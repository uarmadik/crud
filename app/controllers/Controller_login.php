<?php

namespace app\controllers;
use app\core\View;
use app\models\Model_user;

class Controller_login
{
    public function index()
    {
        $message = null;
        if ($_SESSION['alert_error']) {
            $message['alert_error'] = $_SESSION['alert_error'];
            unset($_SESSION['alert_error']);
        }
        if ($_SESSION['alert']) {
            $message['alert'] = $_SESSION['alert'];
            unset($_SESSION['alert']);
        }

        $view = new View();
        $view->generate('general', 'login.html.twig', ['message'=>$message]);
    }

    public function login()
    {
        if (!empty($_POST['login']) && !empty($_POST['password'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
        } else {
            $_SESSION['alert_error'] = 'Empty field login or password';
            header('Location: /login'); exit();
        }

        $db = new Model_user();
        $user_data = $db->get_user($login);
        if (!$user_data) {
            // User not exist!
            $_SESSION['alert_error'] = 'User do not exist!';
            header('Location: /login'); exit();
        }

        if (password_verify($password, $user_data['password'])) {
            // Ok. User exist. Password correct;
            $_SESSION['user_login'] = $login;
            $_SESSION['user_id'] = $user_data['id'];
            header('Location:/'); exit();
        } else {
            // password not correct
            $_SESSION['alert_error'] = 'Password are not correct!';
            header('Location: /login'); exit();
        }

    }

    public function logout()
    {
        unset($_SESSION['user_login']);
        unset($_SESSION['user_id']);
        header('Location:/'); exit();
    }
}