<?php

class Database extends Base
{
    private $action = false;
    
    private $limit = '';
    
    public $db;
    
    public $sql = false;
    
    public $error = '';
    
    public $stmt;
    
    public $from = 'from';
    
    public $table = fasle;
    
    public $totalRow = 0;
    
    public $select = false;
    
    public $result = array();
    
    private $tmpTable;

    private $where = false;
    
    public $groupBy = false;
    
    public $orderBy = false;
    
    public function __construct() 
    {
        parent::__construct();
        $this->db = $this->getConnect();
    }
    
    public function sqlAction()
    {
        
    }
    
    public function select($excerpt)
    {
        $this->action = __FUNCTION__;
        if ($this->select === false) {
            $this->select = $this->resolve(__FUNCTION__);
        }

        if($this->select !== 'SELECT ') {
            $this->select .= ', ';
        }
        
        $this->select .= $excerpt;
        
        return $this;
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
                $this->orderBy .= $str;
            }
            
            if (is_array($str)) { 
                $str = array_unique($str);
                $str = implode(',', $str);
                $this->orderBy .= $str;
            }
        }
        
        return $this;
    }
    
    public function where($key = false)
    {
        $operate = array('>', '=', '<', '!=');
        if ($this->where === false) {
            $this->where = $this->resolve(__FUNCTION__);
        }
        
        if ($this->where !== 'WHERE ') {
            $this->where .= ' AND ';
        }

        try{
            if (is_scalar($key)) {
                $list = func_get_args();
                $i = 0;
                while(isset($list[$i])) {
                    if (in_array($list[$i], $operate)) {
                        throw new Exception('Invalid arguments for where sub statement.');
                    }
                    
                    if (isset($list[$i + 1])) {
                        if (in_array($list[$i + 1], $operate)) {
                            if (isset($list[$i + 2])) {
                                if (in_array($list[$i + 2], $operate)) {
                                    throw new Exception('Invalid value for this where sub statement');
                                } else {
                                    $this->where .= "`" . $list[$i] . "`" . $list[$i + 1] . "'" . $list[$i + 2] . "'";
                                    $i += 3;
                                }
                            } else {
                                if ($list[$i +1] === '=' || $list[$i +1] === '!=') {
                                    $this->where .= "`" . $list[$i] . "`" . $list[$i + 1] . "' '";
                                }
                                
                                if ($list[$i +1] === '>' || $list[$i +1] === '<') {
                                    $this->where .= "`" . $list[$i] . "`" . $list[$i + 1] . "0";
                                }
                                $i += 2;
                            }
                        } else {
                             $this->where .= "`" . $list[$i] . "`='" . $list[$i + 1] . "'";
                             $i += 2;
                        }
                    } else {
                        $this->where .= '`' . $list[$i] . "`!=' '";
                        $i += 2;
                    }
                    $this->where .= ' AND ';
                }
                $this->where = rtrim($this->where, 'AND ');
            }
        } catch (Exception $e) {
            $this->where = false;
            exit($e->getMessage());
        }
      
        return $this;
    }
    
    private function limit($start = 0, $length = 20)
    {
        $condition = '';
        if ($start > 0 && $length > 0) {
            $condition = ' LIMIT ' . $length . ' OFFSET ' . $start;
        }
        if ($start <= 0 && $length > 0) {
            $condition = ' LIMIT ' . $length;
        }
        
        return $condition;
    }
    
    public function from($table = false)
    {
        if ($table) {
            $this->tmpTable = $table;
        } else {
            $this->tmpTable = $this->table;
        }
        $this->from = ' ' . $this->from . ' ' . $this->tmpTable . ' ';
        
        return $this;
    }
    
    public function findOne($val1 = false, $val2 = false)
    {   
        $data = $this->findAll($val1, $val2);
        return array_shift($data);
    }
    
    public function findAll($val1 = false, $val2 = false)
    {   
        $fetchType;
        if ($val1 !== false && $val2 !== false) {
            if (is_numeric($val1)) {
                if ($val1) {
                    $fetchType = true;
                } else {
                    $fetchType = false;
                }
                $this->sql = $val2;
            }
            
            if (is_numeric($val2)) {
                if ($val2) {
                    $fetchType = true;
                } else {
                    $fetchType = false;
                }
                $this->sql = $val1;
            }
            $this->stmt = $this->db->prepare($this->sql);
        } else {           
            if ($val1 !== false) {
                if (is_numeric($val1)) {
                    $this->sql = $this->getSql();
                    $fetchType = $val1 ? true : false;
                } else {
                    $this->sql = $val1;
                    $fetchType = $val2;
                }
            }
            
            if ($val2 !== false) {
                if (is_numeric($val2)) {
                    $this->sql = $this->getSql();
                    $fetchType = $val2 ? true : false;
                } else {
                    $this->sql = $val2;
                    $fetchType = $val1;
                }
            }
            
            $this->stmt = $this->db->prepare($this->sql);
        }

        $this->stmt->execute();

        if ($this->stmt->rowCount()) {
            $this->totalRow = $this->stmt->rowCount() ? $this->stmt->rowCount() : 0;
            $this->result = $this->stmt->fetchAll($fetchType ? PDO::FETCH_NUM : PDO::FETCH_ASSOC);
        } else {
            //statement
        }
        $this->clearQueryVariable();
        
        return $this->result;
    }
    
    private function clearQueryVariable()
    {
        $this->tmpTable = false;
    }
    
    private function resolve($str)
    {
        $str = strtoupper(preg_replace('/^(.*)([A-Z].*)$/', '$1 $2', $str));
        return str_pad($str, strlen($str) + 1, ' ', STR_PAD_RIGHT);
    }
    
    private function getSql()
    {
        switch ($this->action) {
            case 'update' :
                break;
            case 'delete' :
                break;
            case 'insert' :
                break;
            default :
                $this->sql = $this->{$this->action} . $this->from . $this->where . $this->orderBy . $this->groupBy . $this->limit;
                
                break;
        }
        
        return $this->sql;
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