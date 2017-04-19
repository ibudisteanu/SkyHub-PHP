<li><!-- start message -->
    <a href="<?=($sLink != '' ? $sLink : '#')?>" <?=($sLink != 'target="_blank' ? $sLink : '')?>>
        <div class="pull-left">
            <?php if (isset($arrData['icon'])) : ?>
                <i class="<?=$arrData['icon']?>" style="font-size: 33px;"></i>
            <?php endif; ?>
        </div>
        <h4 class="wrapword">
            <?=$sTitle?>

            <small><span class="time" data-toggle="tooltip" data-placement="left"  title="<?=$dtCreationDateFullDateTime?>"><i class="fa fa-clock-o"></i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($dtCreationDate)?></span></small>
        </h4>
        <p class="wrapword"><?=$this->StringsAdvanced->closeTags(substr($sText,0,250))?></p>
    </a>
</li>