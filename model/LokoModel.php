<?php

class LokoModel extends BaseModel
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getData()
    {
        $result = false;
        $sql = 'select * from user';
//        $data = $this->select('id')->select('name')->from('user')-> orderBy('id, age')->where('id', 1)->where('name', '=', 'loko', 'age', 11, 'sex')->findAll($sql);
//        $data = $this->select('id')->select('name')->select('age')->from('user')-> orderBy(array('id'))->orderBy('age')->where('id', 1)->groupBy('id')->groupBy('name')->limit(1, 20)->findAll(1);
//        we($data);
        //update
        $sql = 'UPDATE user SET ....... WHERE ?';
//        $result = $this->set('name1', 'loko1', 'name2', 'loko2', 'name3', 'loko3', 'name4', 'loko4')->set()->where()->where()->update(1);
//        w($result);
        //insert
        $attr = array('name' => 'loko', 'age' => '23', 'sex' => 'm');
        $result = $this->attr($attr)->table('user')->insert();
        w($result);
        //delete
        
    }
}