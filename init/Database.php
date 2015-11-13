<?php

class Database extends Base
{
    public $db;
    
    public $sql = false;
    
    public $error = '';
    
    public $stmt;
    
    public $totalRow = 0;
    
    public $select = false;
    
    public $result = array();
    
    private $limitTable;

    public $groupBy = false;
    
    public $orderBy = false;
    
    public function __construct() 
    {
        parent::__construct();
        $this->db = $this->getConnect();
    }
    
    public function insert()
    {
        
    }
    
    public function save()
    {
        echo 'save sql';
    }
    
    public function update()
    {
        
    }
    
    public function delete()
    {
        
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
    }
    
    public function groupBy($str = false)
    {
        
        
    }
    
    public function orderBy($str = false)
    {
        if ($str) {
            
            if ($this->orderBy === false) {
                $this->orderBy = $this->resolve(__FUNCTION__);
            }
            
            if (is_string($str)) {
                
            }
            
            if (is_array($str)) {
                
            }
        }
        
        return $this;
    }
    
    public function select($excerpt)
    {
        if ($this->select === false) {
            $this->select = $this->resolve(__FUNCTION__);
        }

        if($this->select !== 'SELECT ') {
            $this->select .= ', ';
        }
        $this->select .= $excerpt;
        
        return $this;
    }
    
    public function from($table = false)
    {
        if ($table) {
            $this->limitTable = $table;
        }
        
        return $this;
    }
    
    public function findOne()
    {
        
    }
    
    public function findAll()
    {
        if ($sql) {
            $this->stmt = $this->db->prepare($sql);
        } else {
            $this->stmt = $this->db->prepare($this->sql);
        }
        
        $this->stmt->execute();

        if ($this->stmt->rowCount()) {
            $this->totalRow = $this->stmt->rowCount() ? $this->stmt->rowCount() : 0;
            $this->error = '';
        } else {
            $this->error = $this->db->errorInfo();
            $this->totalRow = 0;
        }
        
        
        if ($this->stmt->rowCount()) {
            $this->result = $this->stmt->fetchAll();
        }
        
        return $this;
    }
    
    private function resolve($str)
    {
        $str = strtoupper(preg_replace('/^(.*)([A-Z].*)$/', '$1 $2', $str));
        return str_pad($str, strlen($str) + 2, ' ', STR_PAD_BOTH);
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