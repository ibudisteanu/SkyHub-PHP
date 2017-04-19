<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * HMVC CodeIgniter https://www.youtube.com/watch?v=zCfm2SIQ5XI
 */

class Pages extends  MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function awesome()
    {
        echo 'Awesome';
    }

    function index ($sPage = 'home', $sParam1='', $sParam2='')
    {
        echo 'OLD '.$sPage, ' param1==',$sParam1,'  param2==',$sParam2;
    }

}