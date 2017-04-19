<?php

require_once APPPATH.'third_party/MX/Controller.php';
require_once APPPATH.'core/controllers/URI_Processing_Library.php';

class MY_Controller extends MX_Controller
{
    protected $data = array();

    public $sModuleRelativePath;
    public $Template;
    public $URI;

    function __construct($bDisableTemplate=false)
    {
        parent::__construct();

        $this->calculateModuleRelativePath();

        $this->URI = new URI_Processing_Library($this);

        $this->data['g_sLanguage'] = "en";

        if ((get_class($this) != 'Template')&&(!$bDisableTemplate))
            $this->Template = modules::load('template/template');

        $this->initiateCache();
    }

    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) return true;

        return (substr($haystack, -$length) === $needle);
    }
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function renderModuleView($path,$data=NULL,$bDisplay=false)
    {
        $sModuleDirectoryPath = ltrim(ltrim($this->sModuleRelativePath,'/'),'.');

        if ($this->startsWith($sModuleDirectoryPath,'modules')) $sModuleDirectoryPath = substr($sModuleDirectoryPath, 7);

        $sModuleDirectoryPath = ltrim($sModuleDirectoryPath,'/');

        $sModuleDirectoryPath = rtrim($sModuleDirectoryPath,'/');
        if ($this->endsWith($sModuleDirectoryPath, 'views')) $sModuleDirectoryPath = substr($sModuleDirectoryPath, 0, strlen($sModuleDirectoryPath)-4 );

        $sModuleDirectoryPath = rtrim($sModuleDirectoryPath,'/');

        return $this->load->view('../modules/'.$sModuleDirectoryPath.'/views/'.$path,$data, $bDisplay);
    }

    public function calculateModuleRelativePath()
    {
        $reflector = new ReflectionClass(get_class($this));
        $fileLocation = $reflector->getFileName();
        $fileLocation = substr($fileLocation  , 0, strrpos($fileLocation , "controllers"));

        $arrPaths = explode('application',$fileLocation );

        $sPath = str_replace('\\', '/', $arrPaths[1]);

        $sPath = ltrim($sPath ,'/');

        $this->sModuleRelativePath = $sPath;
    }

    private function initiateCache()
    {
        if (defined('WEBSITE_OFFLINE')) $this->load->driver('cache', array('adapter' => 'file'));
        else $this->load->driver('cache', array('adapter' => 'apc')); //array('adapter' => 'apc', 'backup' => 'file'));
    }

    public function checkValidAction()
    {
        if (count($this->URI->arrFormParam) > 0)
            $string = $this->URI->arrFormParam[count($this->URI->arrFormParam)-1];

        if ($string == 'add-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam);
    }


    protected function processRoutingInputParameters($iStartingURIIndex=0)
    {
        try {

            $this->URI->processRoutingURISegments($this->uri->segment_array(), $iStartingURIIndex);

        } catch (Exception $ex)
        {
            $this->showErrorPage('Error getting the page index','error','Forum Category','Forum Category');
        }
    }

    protected function includeWebPageLibraries($sWebLibrary='')
    {
        $this->BottomScriptsContainer->addWebLibrary($sWebLibrary);
    }

    protected function showErrorPage($sError, $sErrorType='error',$sPageName='home',$sPageTitle=WEBSITE_TITLE)
    {
        modules::load('pages/ErrorPage')->displayErrorPage($sError, $sErrorType, $sPageName, $sPageTitle);
    }



}