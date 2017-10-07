<?php


namespace app\core;


class Controller
{
    protected $data = null;

    /**
     * @var int
     * Quantity posts on page;
     */
    protected $per_page = 3;
    protected $order_by = 'date_ask';

    public function __construct()
    {
        if (!empty($_SESSION['order_by'])) {
            $this->order_by = $_SESSION['order_by'];
        }
    }
}