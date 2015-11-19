<?php

class Database extends Base
{
    public $action = false;
    
    public $limit = '';
    
    public $token;
    
    public $db;
    
    public $sql = false;
    
    public $error = '';
    
    public $stmt;
    
    public $from = 'from';
    
    public $insertField = '';
    
    public $join = array();
    
    public $table = fasle;
    
    public $totalRow = 0;
    
    public $select = false;
    
    public $set = false;
    
    public $result = array();
    
    public $tmpTable;

    public $where = false;
    
    public $groupBy = false;
    
    public $orderBy = false;
    
    public $lastInsertId = false;
    
    public function __construct() 
    {
        parent::__construct();
        $this->token = time();
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

        if(trim($this->select) !== 'SELECT') {
            $this->select .= ', ';
        }
        
        $this->select .= $excerpt;
        
        return $this;
    }
    
    public function insert($sql = false)
    {
        if ($sql) {
            $this->setSql($sql);
        } else {
            $this->insertBuild();
        }
        
        return $this->query();
        
    }
    
    public function beforeQuery()
    {
        $this->stmt = $this->db->prepare($this->sql);
    }
    
    private function query()
    {
        $this->stmt = $this->db->prepare($this->sql);
        $this->stmt->execute();
        return $this->afterQuery();
    }
    
    private function afterQuery($fetchType = true)
    {
        switch (strtolower(trim($this->action))) {
            case 'insert' :
                $this->result = $this->db->lastInsertId();
                $this->lastInsertId = $this->db->lastInsertId();;
                break;
            case 'update' :
                $this->result = $this->stmt->rowCount() ? $this->stmt->rowCount() : 0;
                break;
            case 'delete' :
                $this->result = $this->stmt->rowCount() ? $this->stmt->rowCount() : 0;
                break;
            case 'select' :
                if ($this->stmt->rowCount()) {
                    $this->totalRow = $this->stmt->rowCount() ? $this->stmt->rowCount() : 0;
                    $this->result = $this->stmt->fetchAll($fetchType ? PDO::FETCH_NUM : PDO::FETCH_ASSOC);
                } else {
                    //statement
                }
                break;
            default :
                break;
        }
        
        return $this->result;
    }
    
    public function attr($arr = array())
    {
        if ($this->insertField === '') {
            $field = '';
            $val = '';
        } else {
            list($field, $val) = explode(') VALUES (', ltrim(rtrim($this->insertField, ')'), '('));
        }
        
        if (!empty($arr)) {
            try {
                foreach ($arr as $key => $v) {
                    if (is_numeric($key)) {
                        throw new Exception('This is not a valid field name : ' . $key);
                    } else {
                        $field .= '`' . $key . '`, ';
                        $val .= "'" . $v . "', ";
                    }
                }
            } catch (Exception $e) {
                exit($e->getMessage());
            }
        }
        $this->insertField = '(' . rtrim(rtrim($field, ', '), ',') . ') VALUES (' . rtrim(rtrim($val, ', '), ',') . ')';
        
        return $this;
    }
    
    public function save()
    {
        echo 'save sql';
    }
    
    public function update($sql = false)
    {
        if (empty($this->tmpTable)) {
            $this->tmpTable = $this->table;
        }

        if ($sql) {
            $this->updateBuild();
        } else {
            $this->sql = $sql;
        }
        we($this->sql);
        $this->updateBuild();
        
        return $this->query();
    }
    
    public function delete()
    {
        $list = func_get_args();
        $this->action = $this->resolve(__FUNCTION__);
        if (count($list) === 1 && !is_numeric($list[0])) {
            $list = array_pop($list);
        }
        
        $this->where($list);
        $this->deleteBuild();
        return $this->query();
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
    }
    
    public function groupBy($str = false)
    {
        $str = trim($str, ',');
        if ($this->groupBy === false) {
            $this->groupBy = $this->resolve(__FUNCTION__);
        }
        
        if (trim($this->groupBy) !== 'GROUP BY') {
            $this->groupBy .= ', ';
        } 
        
        if (is_string($str)) {
            $this->groupBy .= $str;
        }

        if (is_array($str)) {
            $this->groupBy .= implode(',', $str);
        }
        
        return $this;
    }
    
    public function orderBy($str = false)
    { 
        if ($str) {
            if ($this->orderBy === false) {
                $this->orderBy = $this->resolve(__FUNCTION__);
            }
            
            if (trim($this->orderBy) !== 'ORDER BY') {
                $this->orderBy .= ', ';
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
        
        if (trim($this->where) !== 'WHERE') {
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
            } elseif (is_array($key)) {
                if ($this->isIndexArray($key)) {
                    foreach ($key as $val) {
                        $this->where .= "id='" . $val . "', AND ";
                    }
                } else {
                    foreach ($key as $k => $v) {
                        $this->where .= '`' . $key . "`='" . $val . "', AND ";
                    }
                }
                
                $this->where = rtrim(trim(rtrim(rtrim($this->where), 'AND')), ',');
                
            } else {
               
            }
        } catch (Exception $e) {
            $this->where = false;
            exit($e->getMessage());
        }
      
        return $this;
    }
    
    /**
     * This may be pass many arguments or maybe an array using the key and value correspond such as $key => $val
     * 
     * @param string $str
     * 
     * @return \Database
     * 
     * @throws Exception
     */
    public function set($str = false)
    {
        if ($this->set === false) {
            $this->set = $this->resolve(__FUNCTION__);
        }
        
        if (trim($this->set) !== 'SET') {
            $this->set .= ',';
        }
        
        $list = func_get_args();
        try {
            if (is_array($str)) {
            
            } elseif (count($list) > 1) {
                for ($i = 0; $i < count($list); $i = $i + 2) {
                    $this->set .= "`" . $list[$i] . "`='" . (isset($list[$i + 1]) ? $list[$i + 1] : '?') . "'" . ($i=== count($list) - 2 ? '' : ',');
                }
            
                if (count($list) % 2 > 0) {
                    throw new Exception('The key and val is not correspond with "' . $this->set . '"');
                }
            } else {
                throw new Exception('Incorrect arguments list, please check.');
            }
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        
        return $this;
    }
    
    public function limit($start = 0, $length = 20)
    {
        $condition = '';
        if ($start > 0 && $length > 0) {
            $condition = ' LIMIT ' . $length . ' OFFSET ' . $start;
        }
        if ($start <= 0 && $length > 0) {
            $condition = ' LIMIT ' . $length;
        }
        $this->limit .= $condition;
        
        return $this;
    }
    
    public function table($table = false) 
    {
        return $this->from($table);
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
//            $this->stmt = $this->db->prepare($this->sql);
        } else {           
            if ($val1 !== false) {
                if (is_numeric($val1)) {
                    $this->sql = $this->$this->selectBuild();
                    $fetchType = $val1 ? true : false;
                } else {
                    $this->sql = $val1;
                    $fetchType = $val2;
                }
            }
            
            if ($val2 !== false) {
                if (is_numeric($val2)) {
                    $this->sql = $this->$this->selectBuild();
                    $fetchType = $val2 ? true : false;
                } else {
                    $this->sql = $val2;
                    $fetchType = $val1;
                }
            }
        }

        $this->query($fetchType);
        $this->clearQueryVariable();
        
        return $this->result;
    }
    
    private function resolve($str)
    {
        $str = strtoupper(preg_replace('/^(.*)([A-Z].*)$/', '$1 $2', $str));
        return str_pad($str, strlen($str) + 2, ' ', STR_PAD_BOTH);
    }
    
    private function selectBuild()
    {
        $selectList = explode(',', trim(trim(trim($this->select), 'SELECT'), ' '));
        
        $notExistsKey = false;
        $invalid = false;
        try {
            if ($this->orderBy !== false) {
                $orderByKey = explode(',', trim(trim($this->orderBy, 'ORDER BY'), ' '));
                $notExistsOrderKey = false;
                
                array_map(function($val) use (&$invalid, &$notExistsKey, $selectList){

                    if (!in_array($val, $selectList)) {
                        $notExistsKey = true;
                        $invalid = $val;
                    }
                }, $orderByKey);
            }

            if ($notExistsKey) {
                throw new Exception('The key "' . $invalid . '" in order by not appear in select columns');
            }
            
            $notExistsKey = false;
            $invalid = false;
            if ($this->groupBy !== false) {
                $groupByKey = explode(',', trim(trim($this->groupBy, 'GROUP BY'), ' '));
                $notExistsOrderKey = false;
                
                array_map(function($val) use (&$invalid, &$notExistsKey, $selectList){
                    
                    if (!in_array($val, $selectList)) {
                        $notExistsKey = true;
                        $invalid = $val;
                    }
                }, $groupByKey);
            }
            
            if ($notExistsKey) {
                throw new Exception('The key "' . $invalid . '" in group by not appear in select columns');
            }
            
            return $this->select . $this->from . $this->where . $this->groupBy . $this->orderBy . $this->limit;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }
    
    private function updateBuild()
    {
        $this->sql = 'UPDATE ' . $this->tmpTable . $this->set . $this->where;
    }
    
    private function insertBuild()
    {
        if ($this->from === 'from') {
            $table = $this->table;
        } else {
            $table = trim(str_replace('from', '', $this->from));
        }
        $this->action = preg_replace('/(.*)([A-Z].*)/', '$1', __FUNCTION__);
        
        $this->sql = 'INSERT INTO `' . $table . '`' . $this->insertField;
        
        return $this->sql;
    }

    public function deleteBuild()
    {
        $this->sql = 'DELETE FROM ' . str_pad($this->tmpTable, strlen($this->tmpTable) + 2, ' ', STR_PAD_BOTH) . $this->where;
    }
    
    public function join($char = '&')
    {
        switch (strtolower($char)) {
            case '&' :
            case 'and' :
                $this->join = str_pad($char, strlen($char) + 2, ' ', STR_PAD_BOTH);
                break;
            case '' :
                break;
            case '' :
                break;
            case '' :
                break;
            default :
                break;
        }
        
        return $this;
    }
    
    /**
     * Judge a array whether the array is a index arrar or a associative array
     * 
     * Index array like array(1, 2, 3)
     * 
     * Associative array liek array('id' => 1, 'name' => 'someone')
     * 
     * @param array $arr
     * 
     * @return boolean
     */
    public function isIndexArray($arr = array())
    {
        return array_keys($arr) == range(0, count($arr) - 1) ? true : false;
    }
        
    /**
     * Clear all the query variable for the next query statement
     * 
     * Remain the result collection and  the database connection information
     */
    public function clearQueryVariable()
    {
        $reflect = new ReflectionClass($this); 
        $attr = $reflect->getDefaultProperties();
        foreach ($attr as $key => $val) {
            if ($key !== 'db' && $key !== 'result'){
                $this->$key = $val;
            }
        }
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