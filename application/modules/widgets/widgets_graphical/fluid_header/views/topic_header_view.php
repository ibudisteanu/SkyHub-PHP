
<div class="col-md-12 col-sm-12 col-xs-12 slider wow fadeInUp" style="background-image: url('<?= $TopicObject->objImagesComponent->getCoverFirst() != null ? $TopicObject->objImagesComponent->getCoverFirst()['src'] : $g_sThemeURL.'images/showcase-bg.jpg' ?>')  !important; "  data-wow-delay=".3s" style="height:auto; padding=0 0 0 0; border=0px">
    <!-- slider section -->
    <div class="row">

    <?php
        if (isset($g_dtLoginBox))
            echo $g_dtLoginBox;
        ?>

    </div>

    <!-- slider section end -->
</div>

    <?php
        echo $HeaderToolBox->renderMenu('header','float:right;');
    ?>
