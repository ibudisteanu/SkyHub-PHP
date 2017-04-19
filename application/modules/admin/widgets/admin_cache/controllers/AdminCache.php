<?php

class AdminCache extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index($sParam1='', $sParam2='', $sParam3='')
    {

        if (($sParam1=='apps')&&($sParam2 == 'clear-cache'))
        {
            $this->AdvancedCache->clean();
            $this->data['sText'] = 'Cached successfully cleaned';
        }

        if (($sParam1=='apps')&&($sParam2 == 'clean-cache'))
        {
            if ((isset($_POST))&&(isset($_POST['cleanCacheActionName'])))
            {
                $this->AdvancedCache->clearCacheContainsWildcard($_POST['cleanCacheActionName']);
                $this->data['sText'] = 'Cache <b>'.$_POST['cleanCacheActionName'].'</b> has been cleaned successfully';
            } else
                $this->data['sText'] = 'Error <b>no cache specified</b>';
        }

        $sContent = $this->renderModuleView('admin_cache_view',$this->data, TRUE);

        $this->ContentContainer->addObject($sContent,'<section class="col-lg-5 connectedSortable">');
    }

}