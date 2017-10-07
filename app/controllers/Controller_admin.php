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
            header('Location:/'); exit();
        } else {
            $view = new View();
            $view->generate('admin','admin_index.html.twig', ['posts'=>$posts,
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
        $action                 = $_POST['action'];
        $formData['header']     = $_POST['header'];
        $formData['text']       = $_POST['text'];
        $formData['id']         = $_POST['id'];
        $formData['author_id']  = $_SESSION['user_id'];


        if(!empty($_FILES['upload_file']['name'])) {

            $allowed =  array('gif','png' ,'jpeg');

            $filename = $_FILES['upload_file']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            if(!in_array($ext,$allowed)) {
                // Error! Incorrect extension!
                $_SESSION['alert_error'] = 'Error! Incorrect extension!';
                header('Location: /'); exit();
            }

            if ($_FILES['upload_file']['size'] > 2000000) {
                // Error. to large file!
                $_SESSION['alert_error'] = 'Error! To large file!';
                header('Location: /'); exit();
            }
            $file_name = $_FILES['upload_file']['name'];
            $type = $_FILES['upload_file']['type'];
            $name = $_FILES['upload_file']['tmp_name'];

            if (!$this->resize($type, $name)){
                // Error! File did not upload!
                $_SESSION['alert_error'] = 'Error! File did not upload!';
                header('Location: /'); exit();
            } else {
                $formData['img_name'] = $_FILES['upload_file']['name'];
            }
        }

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


    /**
     *
     * Resize image width to 320px or image height to 240px
     * and move file to folder.
     *
     * @param $type
     * @param $name
     * @return bool
     */
    public function resize($type, $name)
    {
        switch ($type){
            case 'image/jpeg':
                $img = imagecreatefromjpeg($name);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($name);
                break;
            case 'image/png':
                $img = imagecreatefrompng($name);
                break;
        }

        $img_width = imagesx($img);
        $img_height = imagesy($img);

        $width = 320;
        $height = 240;

        $koef_width = round($img_width/$width,3);
        $koef_height = round($img_height/$height,3);

        if (($img_width/$img_height) > 1) {

            $new_height = $img_height/$koef_width;
            $new_width = $img_width/$koef_width;

        } else {

            $new_width = $img_width/$koef_height;
            $new_height = $img_height/$koef_height;
        }


        $new_img = imagecreatetruecolor($new_width, $new_height);

        $res = imagecopyresampled($new_img, $img, 0,0,0,0, $new_width, $new_height, $img_width,$img_height);
        $path_to_file = '../public/img/uploaded_files/'.$_FILES['upload_file']['name'];

        switch ($type){
            case 'image/jpeg':
                $result = imagejpeg($new_img, $path_to_file);
                break;
            case 'image/gif':
                $result = imagegif($new_img, $path_to_file);
                break;
            case 'image/png':
                $result = imagepng($new_img, $path_to_file);
                break;
        }

        imagedestroy($new_img);
        imagedestroy($img);

        return $result;

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