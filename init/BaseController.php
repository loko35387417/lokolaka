<?php

class BaseController extends Base
{   
    protected $model;
    
    public function __construct($table) {
        parent::__construct();
        $this->model = $this->initModel($table);
    }
    
    public function initModel($table)
    {
        $model = false;
        $referenceClass = debug_backtrace();
        $referenceClass = array_pop($referenceClass)['class'];
//        $referenceClass = $referenceClass['class'];
        $modelName = preg_replace('/(.*)([A-Z].*)/', '$1', $referenceClass);
        $modelClass = $modelName . 'Model'; 
        try {
            if (class_exists($modelClass)) {
                $model = new $modelClass;
                if ($this->params['dbconfig']['table_prefix']) {
                    $tableName = $this->params['dbconfig']['table_prefix'] . '_' . $modelName;
                } else {
                    $tableName = $modelName;
                }
                
                $model->table = $table === false ? strtolower($modelName) : $table;
            } else {
                throw new Exception('class \'' . $modelClass . '\' not exists.');
            }
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        
        return $model;
    }
}