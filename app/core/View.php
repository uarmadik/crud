<?php


namespace app\core;


class View
{
    /**
     * @param $category_view - divides views on admin view or general view
     * @param $name_of_view
     * @param null $parameters
     */
    public function generate($category_view,$name_of_view, $parameters=null)
    {
        switch ($category_view) {
            case 'general':
                $path_to_templates = '../app/views';
                break;
            case 'admin':
                $path_to_templates = '../app/views/admin';
                break;
        }

        $loader = new \Twig_Loader_Filesystem($path_to_templates);
        $twig = new \Twig_Environment($loader, array(
            'cache'=>'vendor/twig/twig/lib/Twig/Cache',
            'auto_reload' => true
        ));
        echo $twig->render($name_of_view, array('parameters' => $parameters));
    }

}