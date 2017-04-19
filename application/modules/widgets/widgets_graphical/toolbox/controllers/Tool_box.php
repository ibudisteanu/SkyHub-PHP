<?php

class Tool_box extends MY_Controller
{
    public $sName = 'Top ToolBox';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('toolbox/tool_box_container','ToolBoxContainer');
    }

    public function renderMenu($groupName='', $style='')
    {

        $this->data['arrToolBoxContainer']=$this->ToolBoxContainer->arrContainer;
        $this->data['sToolBoxGroupName']=$groupName;
        $this->data['sStyle'] = $style;

        echo $this->renderModuleView('toolbox_container_view',$this->data,true);
        //$this->ContentContainer->addObject($this->renderModuleView('profileheader',$this->data,TRUE));
    }

}