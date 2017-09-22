<?php


namespace app\controllers;


use app\core\Controller;
use app\core\View;
use app\models\Model_posts;

class Controller_admin extends Controller
{

    public function index()
    {
        $db = new Model_posts();
        $posts = $db->getAllPosts();

        $view = new View();
        $view->generate_admin('admin_index.html.twig', $posts);
    }

    public function create()
    {
        $view = new View();
        $view->generate_admin('admin_create.html.twig', null);
    }

    public function store($formData)
    {
        //var_dump($formData);
        $db = new Model_posts();
        if ($db->store($formData)){
            $_SESSION['alert'] = 'Post saving successful';
            header('Location: /admin');
        }
    }

    public function delete($id)
    {
        $db = new Model_posts();
        if ($db->destroy($id)) {
            $_SESSION['alert'] = 'Post delete successful';
            header('Location: /admin');
        }
    }

    /**
     * Open form to edit post;
     * @param $id
     */
    public function edit($id)
    {
        $db = new Model_posts();
        $post = $db->getPost($id);

        $view = new View();
        $view->generate_admin('admin_edit.html.twig', $post);

    }

    /**
     * Saving changed post in table;
     * @param $post
     */
    public function saveChange($post)
    {
        $db = new Model_posts();
        if ($db->edit($post)) {
            $_SESSION['alert'] = 'Changes saving successful';
            header('Location: /admin');
        }
    }
}