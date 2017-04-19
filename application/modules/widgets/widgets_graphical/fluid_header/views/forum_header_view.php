<div class="col-md-12 col-sm-12 col-xs-12 slider wow fadeInUp" style="background-image: url('<?= $ForumObject->sCoverImage != '' ? $ForumObject->sCoverImage  : $g_sThemeURL.'images/showcase-bg.jpg' ?>')  !important; "  data-wow-delay=".3s" style="padding=0 0 0 0; border=0px">
    <!-- slider section -->
    <div class="row">

        <?php
        if (isset($g_dtLoginBox))
            echo $g_dtLoginBox;
        ?>

        <div class="slider-wrap" >
        </div> <!-- container end  -->


        <div class="slider-text">
            <h1><?=' <strong>'. $ForumObject->sName?></strong></h1>
            <h2><?= $ForumObject->sDescription?></h2>
        </div>
    </div>
    <!-- slider section end -->
</div>

<?php
    echo $HeaderToolBox->renderMenu('header','float:right;');
?>