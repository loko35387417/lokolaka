<?php

require_once getcwd() . '/init/autoload.php';
$orderByKey = array('id', 'age');
$selectList = array('id', 'name');

$a = array(1,2,3, 2);
$loko = new LokoController();
$loko->index();