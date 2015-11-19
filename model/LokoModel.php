<?php

class LokoModel extends BaseModel
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getData()
    {
        $result = false;
        w('select------------------------------');
        $sql = 'select * from user';
        $result = $this->select('id')->select('name')->from('user')-> orderBy('id, age')->where('id', 1)->where('name', '=', 'loko', 'age', 11, 'sex')->findAll($sql);
//        $data = $this->select('id')->select('name')->select('age')->from('user')-> orderBy(array('id'))->orderBy('age')->where('id', 1)->groupBy('id')->groupBy('name')->limit(1, 20)->findAll(1);
        w(count($result));
        w('select------------------------------end');
        //update
        $sql = 'UPDATE user SET ....... WHERE ?';
        w('update------------------------------');
        $result = $this->set('name', 'loko')->set('age', 23)->set('id', 789)->table('user')->where()->where(78)->update(1);
        
        w($result);
        w('update------------------------------');
        //insert
        w('insert------------------------------');
        $attr = array('name' => 'loko', 'age' => '23', 'sex' => 'm');
        $result = $this->attr($attr)->table('user')->insert();
        
        w($result);
        w('insert------------------------------');
        //delete
        w('delete------------------------------');
        $attr = array('name' => 'loko', 'age' => '23', 'sex' => 'm', 'id' => 82);
//        $result = $this->join('&')->delete($attr);
        $result = $this->table('user')->join('&')->delete('78',77);
        
        w($result);
        w('delete------------------------------');
        
        $this->clearQueryVariable();
    }
}