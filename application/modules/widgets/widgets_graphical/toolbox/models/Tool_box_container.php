<?php

require_once APPPATH.'modules/widgets/widgets_graphical/toolbox/models/Tool_box_element.php';

class Tool_box_container extends MY_Model
{
    public $arrContainer = [];

    public function addElement($sGroupName, $sName, $sText, $sImage, $sURL='', $sColor='', $bVisible=true, $sObjectID='',$sOnClick='')
    {
        $object = new Tool_box_element($sGroupName,$sName, $sText, $sImage, $sURL, $sColor, $bVisible, $sObjectID, $sOnClick);

        array_push($this->arrContainer, $object);

        return $object;
    }

}