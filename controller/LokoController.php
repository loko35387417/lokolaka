<?php

class LokoController extends BaseController
{
    public function __construct($table = false) {
        parent::__construct($table);
    }
    
    public function index()
    {
        $data = $this->model->getData();
    }
}
