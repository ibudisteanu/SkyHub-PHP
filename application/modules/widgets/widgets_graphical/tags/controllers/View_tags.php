<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//JQUERY TAGS http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/

class View_tags extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function renderTags($arrTags, $bHidden=false)
    {
        if (!((is_array($arrTags)) && (count($arrTags) > 0))) return '';

        $this->data['arrTags'] = $arrTags;
        $sContent = $this->renderModuleView('tags_container_view',$this->data,$bHidden);
        return $sContent ;
    }

    public function getContainerObject($arrTags,$index=6666)
    {
        $this->ContentContainer->addObject($this->renderTags($arrTags,true),'',$index);
    }

}