<?php

class BaseModel extends Database
{
    public $table;

    public function __construct() 
    {
        parent::__construct();
    }
    
    public function validUser()
    {
        return 'valid user';
    }
}