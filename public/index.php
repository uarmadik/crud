<?php
// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 2);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(2);
session_start();

require_once '../vendor/autoload.php';

use app\core;



if(!empty($_POST)){
//    var_dump($_POST);
//    die();
    $post =['header' => $_POST['header'],
            'text'   => $_POST['text'],
            'id'     => $_POST['id'] ];

    switch ($_POST['action']){

        case 'create':
            $init = new \app\controllers\Controller_admin();
            $init->store($post);
            break;

        case 'edit':
            $init = new \app\controllers\Controller_admin();
            $init->saveChange($post);
            break;

        default:
            core\Route::ErrorPage404();
    }

} else {

    core\Route::start();
}