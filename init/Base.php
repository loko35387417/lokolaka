<?php

class Base
{
    public $params;
    
    public function __construct() {
        $this->params = $this->initConfig();
    }
    
    public function initConfig()
    {
        return require dirname(__DIR__) . '/config/db.php';
    }
    
}