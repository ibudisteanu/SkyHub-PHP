<?php

class ToDoList extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->ContentContainer->addObject($this->renderModuleView('todolist',$this->data, TRUE),'<section class="col-lg-7 connectedSortable ui-sortable">');
    }

}