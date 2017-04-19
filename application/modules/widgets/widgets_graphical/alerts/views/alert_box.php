<a href="<?=$sName?>"></a>
<div class="alert alert<?=$sTypeClass?> <?php ($bDismissible==true ? 'alert-dismissible' : '') ?>" style="margin-bottom: 10px; margin-top: 10px;">
    <?php if ($bDismissible==true) echo '<button type="button" class="close" data-dismiss="alert"  aria-hidden="true">×</button>'?>
    <h4><i class="icon <?=$sIcon?>"></i> <?=$sHeader?></h4>
    <?=$sContent?>
</div>