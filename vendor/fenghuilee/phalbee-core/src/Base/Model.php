<?php

namespace Phalbee\Base;

use Phalcon\Mvc\Model as BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setConnectionService('db');
        $db = include(ROOT_DIR . "/app/configs/db.php");
        if (array_key_exists('prefix', $db))
            $this->setSource($db['prefix'].strtolower(substr(strrchr(get_class($this), "\\"), 1)));
        else
            $this->setSource(strtolower(substr(strrchr(get_class($this), "\\"), 1)));
    }
}
