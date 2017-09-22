<?php


namespace app\controllers;

use app\core\View;

class Controller_404
{
    public function __construct()
    {
        $view = new View();
        $view->generate('404.html.twig', null);
    }
}