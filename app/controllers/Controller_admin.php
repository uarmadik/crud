<?php


namespace app\controllers;


use app\core\Controller;
use app\core\View;
use app\models\Model_posts;

class Controller_admin extends Controller
{

    public function index()
    {
        var_dump($_SESSION);

        if (!empty($_SESSION['alert'])) {
            $message['alert'] = $_SESSION['alert'];
            unset($_SESSION['alert']);
        }
        if ($_SESSION['alert_error']) {
            $message['alert_error'] = $_SESSION['alert_error'];
            unset($_SESSION['alert_error']);
        }

        $db = new Model_posts();
        $posts = $db->getAllPosts();

        if (gettype($posts) != 'array') {
            $_SESSION['alert_error'] = 'Something wrong!';
            $view = new View();
            $view->generate('admin','admin_index.html.twig', null);
        } else {
            $view = new View();
            $view->generate('admin','admin_index.html.twig', ['post'=>$posts, 'message'=>$message]);
        }
    }

    public function create()
    {
        $view = new View();
        $view->generate('admin','admin_create.html.twig', null);
    }

    /**
     * To save new post or save changing existing in DB;
     * @var $action gets value from hidden input in form
     */
    public function store()
    {
        $action = $_POST['action'];
        $formData['header'] = $_POST['header'];
        $formData['text']   = $_POST['text'];
        $formData['id']     = $_POST['id'];

        $db = new Model_posts();

        switch ($action) {
            case 'create':
                if ($db->store($formData)) {
                    $_SESSION['alert'] = 'Post saving successful';
                    header('Location: /admin');
                } else {
                    $_SESSION['alert_error'] = 'Something wrong';
                    header('Location: /admin');
                }
                break;
            case 'edit':
                if ($db->edit($formData)) {
                    $_SESSION['alert'] = 'Changes saving successful';
                    header('Location: /admin');
                } else {
                    $_SESSION['alert_error'] = 'Something wrong';
                    header('Location: /admin');
                }
                break;

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
        $view->generate('admin','admin_edit.html.twig', $post);

    }
}