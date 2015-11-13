<?php

$attr = array(
    'pdo' => true,
    'type' => 'mysql',
    'name' => 'test',
    //sub add _ character
    'table_prefix' => '',
);

$dbconfig = array(
    'host' => 'localhost',
    'user' => 'root',
    'pawd' => 'f3xkv76Vgyhvbfh',
);

if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
    $dbconfig = array(
        'host' =>  'localhost',
        'user' => 'root',
        'pawd' => '123456',
        'port' => 3306,
    );
    
    if ($attr['type'] === 'pgsql') {
        $dbconfig['user'] = 'postgres';
        $dbconfig['port'] = 5432;
    }
}
$dbconfig = array('dbconfig' => array_merge($dbconfig, $attr));

return array_merge(
    $dbconfig,
    require dirname(__FILE__) . '/env.php',
    require dirname(__FILE__) . '/params.php'
);