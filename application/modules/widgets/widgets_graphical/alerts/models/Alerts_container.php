<?php

require_once APPPATH.'modules/widgets/widgets_graphical/alerts/models/Alert_object.php';

//Container that contains all Alerts

class Alerts_container extends MY_Model
{
    public $arrContent=[];

    function __construct()
    {
        parent::__construct('');
    }

    function addAlert($sAlertName, $sType, $sText, $sHeader='default', $bDismissible=true, $sIcon='default')
    {
        $AlertObject = new Alert_object($sAlertName, $sType, $sText, $sHeader, $bDismissible, $sIcon);
        array_push($this->arrContent,$AlertObject);
    }

    function renderViewByName($sAlertName, $sStyle='none', $bHide=false, $bJustMessage=false, $bIncludeShownMessagesBefore=false)
    {
        $sContent = '';
        foreach ($this->arrContent as $it)
        {
            if (($it->sName == $sAlertName) || (($bIncludeShownMessagesBefore)&&($it->sNameInitial == $sAlertName)))
            {
                $data['bDismissible']=$it->bDismissible;
                $data['sHeader']=$it->sHeader;
                $data['sContent']=$it->sText;
                $data['sTypeClass']=$it->sTypeClass;
                $data['sName'] = $it->sName;

                $data['sIcon'] = $it->sIcon;

                if (!$bJustMessage) {
                    if ($sStyle == 'center') $sContent .= $this->load->view('alerts/alert_box_center', $data, $bHide);
                    else $sContent .= $this->load->view('alerts/alert_box', $data, $bHide);
                } else
                    $sContent .= $it->sText;

                $it->sName = 'Verified Already';

            }
        }

        if (!$bHide)
            $sContent='';

        return $sContent;
    }

}