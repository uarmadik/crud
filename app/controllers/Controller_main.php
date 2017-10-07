<?php


namespace app\controllers;


use app\core\Controller;
use app\core\View;
use app\models;

class Controller_main extends Controller
{
    /**
     * Return view with all posts from db;
     */
    public function index($current_page = 1)
    {
        if ($_SESSION['user_login'] && $_SESSION['user_id']) {
            $user['login'] = $_SESSION['user_login'];
            $user['id'] = $_SESSION['user_id'];
        }

        $post_from = $current_page * $this->per_page - $this->per_page;
        $limit     = $this->per_page;

        $db = new models\Model_posts();
        $rows = $db->get_quantity_rows();
        $posts = $db->getAllPosts($post_from, $limit, $this->order_by);
        $pages = ceil($rows/$this->per_page);

        $view = new View();
        $view->generate('general','main_view.html.twig', ['posts'=>$posts,
                                                                                   'sort'=>$this->order_by,
                                                                                   'user'=>$user,
                                                                                   'pages'=>$pages]);
    }

    public function order($order_param)
    {
        if (!empty($order_param)) {
            $_SESSION['order_by'] = $order_param;
        }

        header('Location:/'); exit();

    }

}