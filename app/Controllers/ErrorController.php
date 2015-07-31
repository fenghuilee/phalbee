<?php

namespace App\Controllers;

use Phalbee\Base\Controller;

class ErrorController extends Controller
{

    public function indexAction()
    {
        //var_export($this->response->getContent());
        //echo (new \Phalcon\Debug\Dump())->variables($this->response);
        $this->smarty->assign(['Status' => $this->response->getStatusCode()]);
        $this->smarty->display('error.html');
    }
}
