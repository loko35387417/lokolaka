<?php

class LokoModel extends BaseModel
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getData()
    {
        $data = $this->select('id')->select('name')->select('name')->from('user')-> orderBy('id, age');
        we($data);
    }
}