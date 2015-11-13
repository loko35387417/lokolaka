<?php

require_once getcwd() . '/init/autoload.php';

$loko = new LokoController('user');
$loko->index();