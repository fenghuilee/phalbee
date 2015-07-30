<?php

return [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => 'password',
    'dbname'   => 'phalbee',
    'prefix'   => 'pl_',
    'charset'  => 'utf8mb4',
    "options"  => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'UTF8MB4\'",
        PDO::ATTR_CASE => PDO::CASE_LOWER,
    ],
];
