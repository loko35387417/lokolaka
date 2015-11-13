<?php

class BaseModel extends Database
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function validUser()
    {
        return 'valid user';
    }
}