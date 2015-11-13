<?php

class Database extends Base
{
    private $db;
    
    public $sql;
    
    public $error;
    
    public function __construct() 
    {
        parent::__construct();
        
        $this->sql = '';
        $this->error = '';
        
        $this->db = $this->getConnect();
    }
    
    public function insert($sql)
    {
        
    }
    
    public function save($sql)
    {
        
    }
    
    public function update($sql)
    {
        
    }
    
    public function delete($sql)
    {
        
    }
    
    private function getConnect()
    {
        if ($this->params['dbconfig']['pdo']) {
            $db = new PDO($this->params['dbconfig']['type'] . ':host=' . $this->params['dbconfig']['host'] . ';dbname=' . $this->params['dbconfig']['name'], $this->params['dbconfig']['user'], $this->params['dbconfig']['pawd']);
        } else {
            $db = new mysqli($this->params['dbconfig']['host'], $this->params['dbconfig']['user'], $this->params['dbconfig']['pawd'], $this->params['dbconfig']['name']);
        }
        return $db;
    }
}