<div class="col-md-12 col-sm-12 col-xs-12 slider wow fadeInUp" style="background-image: url('<?= $forumCategoryObject->sCoverImage != '' ? $forumCategoryObject->sCoverImage  : $g_sThemeURL.'images/showcase-bg.jpg' ?>')  !important; "  data-wow-delay=".3s" style="padding=0 0 0 0; border=0px">
    <!-- slider section -->
    <div class="row">

        <div class="slider-wrap" >

            <div class="slider-text" style="padding-top: 110px; padding-left: 90px !important;">
                <h1><?=' <strong>'. $forumCategoryObject->sName?></strong></h1>
                <h2><?= $forumCategoryObject->sDescription?></h2>
            </div>
        </div> <!-- container end  -->
    </div>
    <!-- slider section end -->
</div>

<?php
    echo $HeaderToolBox->renderMenu('header','float:right;');
?>

<?php
/*
    $CategoryObject->find($CategoryObject->getParentMongoFieldAccessRequest());
    print_r($CategoryObject->getParentMongoFieldAccessRequest());
*/
?>