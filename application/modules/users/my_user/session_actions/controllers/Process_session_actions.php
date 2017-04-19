<?php

class Process_session_actions extends MX_Controller
{
    public function __construct()
    {
        $this->load->model('session_actions/Session_actions','SessionActions');
    }

    public function index()
    {

    }

    public function solveSessionActions($bRedirectPage=false)
    {
        $this->SessionActions->solveSessionActions($bRedirectPage);
    }
}