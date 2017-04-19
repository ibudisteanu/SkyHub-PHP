    <div class="slider-toolbox" <?= (isset($sStyle)) ? 'Style="'.$sStyle.'"' : '' ?>>
<?php

    foreach ($arrToolBoxContainer as $ToolBoxObject)
        if (($sToolBoxGroupName=='')||($sToolBoxGroupName=='any')||($ToolBoxObject->sGroupName == $sToolBoxGroupName))
        {
            $data['ToolBox']=$ToolBoxObject;
            $this->load->view('toolbox_element_view',$data);
        }
?>
    </div>