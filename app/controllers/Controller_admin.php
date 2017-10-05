<?php


namespace app\controllers;


use app\core\Controller;
use app\core\View;
use app\models\Model_posts;

class Controller_admin extends Controller
{
    protected $user_id;
    protected $role_super_admin;

    public function __construct()
    {
        if (empty($_SESSION['user_login']) && empty($_SESSION['user_id'])) {
            // Error! Немає доступу.
            $_SESSION['alert_error'] = 'You are not logged!';
            header('Location:/login'); exit();
        } else {
            $this->user_id = $_SESSION['user_id'];
            $this->role_super_admin = ($_SESSION['user_login'] == 'super-admin') ? true : false;
        }

    }

    public function index($current_page = 1)
    {
        $user['login'] = $_SESSION['user_login'];

        if (!empty($_SESSION['alert'])) {
            $message['alert'] = $_SESSION['alert'];
            unset($_SESSION['alert']);
        }
        if (!empty($_SESSION['alert_error'])) {
            $message['alert_error'] = $_SESSION['alert_error'];
            unset($_SESSION['alert_error']);
        }

        $post_from = $current_page * $this->per_page - $this->per_page;
        $limit     = $this->per_page;

        $db = new Model_posts();

        if ($this->role_super_admin){

            $rows = $db->get_quantity_rows();
            $posts = $db->getAllPosts($post_from, $limit);
            $pages = ceil($rows/$this->per_page);

        } else {

            $rows = $db->get_quantity_rows_by_user($this->user_id);
            $posts = $db->getAllPostsByUser($post_from, $limit, $this->user_id);
            $pages = ceil($rows/$this->per_page);

        }

        if (gettype($posts) != 'array') {
            $_SESSION['alert_error'] = 'Something wrong!';
            $view = new View();
            $view->generate('admin','admin_index.html.twig', null);
        } else {
            $view = new View();
            $view->generate('admin','admin_index.html.twig', ['post'=>$posts,
                                                                                      'message'=>$message,
                                                                                      'user'=>$user,
                                                                                      'pages'=>$pages]);
        }
    }


    /**
     * Open form create post.
     */
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
        $formData['author_id'] = $_SESSION['user_id'];

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

                if ($db->edit($formData, $this->user_id, $this->role_super_admin)) {
                    $_SESSION['alert'] = 'Changes saving successful';
                    header('Location: /admin');
                } else {
                    $_SESSION['alert_error'] = 'Something wrong';
                    header('Location: /admin');
                }
                break;

        }
    }


    public function delete($post_id)
    {
        $db = new Model_posts();
        if ($db->destroy($post_id, $this->user_id, $this->role_super_admin)) {
            $_SESSION['alert'] = 'Post delete successful';
            header('Location: /admin');
        } else{
            $_SESSION['alert_error'] = 'You do not have access';
            header('Location: /admin'); exit();
        }
    }

    /**
     * Open form to edit post;
     * @param $id
     */
    public function edit($post_id)
    {
        $db = new Model_posts();
        $post = $db->getPost($post_id, $this->user_id,$this->role_super_admin);

        if (empty($post)) {
            $_SESSION['alert_error'] = 'You do not have access';
            header('Location: /admin'); exit();
        }

        $view = new View();
        $view->generate('admin','admin_edit.html.twig', $post);

    }
}