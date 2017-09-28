<?php


namespace app\controllers;


use app\core\View;
use app\models;

class Controller_main
{
    /**
     * Return view with all posts from db;
     */
    public function index($order_by = 'date_asc')
    {
        $db = new models\Model_posts();
        $posts = $db->getAllPosts($order_by);

        $view = new View();
        $view->generate('general','main_view.html.twig', ['posts'=>$posts, 'sort'=>$order_by]);
    }

}