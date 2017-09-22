<?php


namespace app\controllers;


use app\core\View;
use app\models;

class Controller_main
{
    protected $data = [
        ['id'=>'1','header'=>'header 1', 'text'=>'text 1'],
        ['id'=>'2','header'=>'header 2', 'text'=>'text 2'],
        ['id'=>'3','header'=>'header 3', 'text'=>'text 3'],
        ['id'=>'4','header'=>'header 4', 'text'=>'text 4'],
        ['id'=>'5','header'=>'header 5', 'text'=>'text 5'],
        ['id'=>'6','header'=>'header 6', 'text'=>'text 6'],
    ];


    public function __construct()
    {
        //echo 'I am Controller_main! =)';
    }
    public function index()
    {
        //echo 'controller main - index';
        $db = new models\Model_posts();
        $posts = $db->getAllPosts();
        //var_dump($posts);
        //die();
        $view = new View();
        $view->generate('main_view.html.twig', $posts);
    }

}