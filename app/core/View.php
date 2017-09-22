<?php


namespace app\core;


class View
{
    public function generate($name_of_view, $parameters=null)
    {
        $loader = new \Twig_Loader_Filesystem('../app/views');
        $twig = new \Twig_Environment($loader, array(
            'cache'=>'vendor/twig/twig/lib/Twig/Cache',
            'auto_reload' => true
        ));
        echo $twig->render($name_of_view, array('parameters' => $parameters));
    }

    public function generate_admin($name_of_view, $parameters=null)
    {
        $loader = new \Twig_Loader_Filesystem('../app/views/admin');
        $twig = new \Twig_Environment($loader, array(
            'cache'=>'vendor/twig/twig/lib/Twig/Cache',
            'auto_reload' => true
        ));
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render($name_of_view, array('parameters' => $parameters));
    }
}