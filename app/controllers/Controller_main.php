<?php


namespace app\controllers;


use app\core\View;
use app\models;

class Controller_main
{
    /**
     * Return view with all posts from db;
     */
    public function index()
    {
        $db = new models\Model_posts();
        $posts = $db->getAllPosts();

        $view = new View();
        $view->generate('main_view.html.twig', $posts);
    }

}