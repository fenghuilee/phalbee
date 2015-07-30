<?php

namespace App\Controllers;

use Phalbee\Base\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        $this->view->pick('index');
    }
}
