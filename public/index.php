<?php
//ini_set('session.gc_maxlifetime', 2);
//session_set_cookie_params(2);

session_start();

require_once '../vendor/autoload.php';

use app\core;

core\Route::start();