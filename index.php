<?php

//echo getcwd();

require_once getcwd() . '/class/baseinit.php';

//$db = new mysqli('localhost', 'root', '123456', 'test');
$db = new mysqli('localhost', 'root', 'f3xkv76Vgyhvbfh', 'test');
w($db);

//$db = new PDO('mysql:dbhost=localhost;dbname=test', 'root', '123456');
$db = new PDO('mysql:dbhost=localhost;dbname=test', 'root', 'f3xkv76Vgyhvbfh');
w($db);
