<div id="infiniteScrollDisplayContent_<?=$sInfiniteScrollContainerName?>" style="display: inline-block; width:100%">

    <div id="display-data-col-<?=$sInfiniteScrollContainerName?>" class="<?=$bEnableMasonry ? 'col-md-6 col-sm-6 col-xs-6 ' : 'col-md-12 col-sm-12 col-xs-12 ' ?>col-xxs-12 col-tn-12" style="padding: 0">
        <?=$dtContent?>
    </div>

</div>

<div id="infiniteScrollLoadingPosition_<?=$sInfiniteScrollContainerName?>"> </div>

<?php if ($bHasNext) : ?>
    <div id="infiniteScrollLoadingRefreshSpin_<?=$sInfiniteScrollContainerName?>" class="overlay" style="text-align: center; height: 60px; font-size: 50px;">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
<?php endif ?>