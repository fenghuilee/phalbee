<?php

namespace App\Controllers;

use Phalbee\Base\Controller;

class ErrorController extends Controller
{

    public function error404Action()
    {
        echo "404 Not Page Found!";
    }
}
