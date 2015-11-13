<?php

class PageController extends BaseController
{   
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        we($this->model);
    }
}
