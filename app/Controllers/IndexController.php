<?php

namespace App\Controllers;

use Phalbee\Base\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->smarty->display('index.html');
    }

    public function testAction()
    {
        $this->smarty->display('error.html');
    }
}
