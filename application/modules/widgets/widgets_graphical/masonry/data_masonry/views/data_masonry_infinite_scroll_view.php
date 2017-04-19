<div id="masonryId<?=$sDisplayName?>" class="" style="margin: 0; left:0; padding: 0; ">

    <div id="infiniteScrollDisplayContent_<?=$sDisplayName?>" class="col-md-12 col-sm-12 col-xs-12  row masonry-container" style="margin: 0; left:0; padding: 0; ">
        <?=$sContentData?>
    </div>

    <?php if ($bHasNext) : ?>
        <div id="infiniteScrollLoadingRefreshSpin_<?=$sDisplayName?>" class="overlay" style="text-align: center; font-size: 50px;">
            <i class="fa fa-refresh fa-spin" style="padding-bottom:40px; height: 50px;"></i>
        </div>
    <?php endif;?>

</div> <!--/.masonry-container  -->

<div style="display: inline-block" >
    <div id="infiniteScrollLoadingPosition_<?=$sDisplayName?>"> </div>
</div>

