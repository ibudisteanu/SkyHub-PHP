<?php

/**
 * Created by PhpStorm.
 * User: BIT TECHNOLOGIES
 * Date: 10/11/2016
 * Time: 10:18 PM
 */

class Sitemap_controller extends  MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public  function index()
    {
        ini_set('max_execution_time', 0);
        $this->generateSitemap();
    }

    protected  function generateSitemap()
    {
        $this->load->library('../modules/widgets/sitemap/libraries/sitemap_library',base_url(""),'SiteMapLibrary');
        //$this->SiteMapLibrary->setPath('../modules/widgets/sitemap/libraries/xmls/');

        $this->SiteMapLibrary->OUTPUT_FILE = (__DIR__ . '/../libraries/xmls/sitemap.xml');
        $this->SiteMapLibrary->SITE = base_url("");
        $this->SiteMapLibrary->start();

    }


}