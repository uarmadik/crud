<?php


namespace app\controllers;

use app\core\View;

class Controller_404
{
    public function index()
    {
        $view = new View();
        $view->generate('404.html.twig', null);
    }
}