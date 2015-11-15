<?php

class LokoModel extends BaseModel
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getData()
    {
        $sql = 'select * from user';
//        $data = $this->select('id')->select('name')->from('user')-> orderBy('id, age')->where('id', 1)->where('name', '=', 'loko', 'age', 11, 'sex')->findAll($sql);
        $data = $this->select('id')->select('name')->from('user')-> orderBy(array('id, age'))->where('id', 1)->findAll(1);
        we($data);
    }
}