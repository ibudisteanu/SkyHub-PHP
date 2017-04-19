<a <?=isset($ToolBox->sObjectID) ? 'id="'.$ToolBox->sObjectID.'"' : '' ?>  class="btn btn-app" <?=$ToolBox->getColor() != '' ? 'style="background-color: '.$ToolBox->getColor().'"' : ''?> <?= ( $ToolBox->sURL != '' ) ? 'href="'.$ToolBox->sURL.'"' : '' ?> <?=isset($ToolBox->sOnClick) ? 'onClick="'.$ToolBox->sOnClick.'"'  : '' ?> >

        <i class="<?=$ToolBox->sImage?>" ></i>
        <?=$ToolBox->sText?>

</a>