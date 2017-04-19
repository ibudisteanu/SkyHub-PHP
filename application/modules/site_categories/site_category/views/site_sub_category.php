    <!-- /.col -->
    <?php /*col-md-2 col-sm-3 col-xs-3*/ ?>
    <div class="col-xs-3 sub-category-div" >
        <div class="sub-category-box">

            <?php
                $imageBackground='';
                $icon='';
                if (($this->StringsAdvanced->startsWith($siteCategory->sImage,"fa fa"))||($this->StringsAdvanced->startsWith($siteCategory->sImage,"glyphicon glyphicon-")))
                    $icon= '<i class="'.$siteCategory->sImage.'"></i>';
                else
                    $imageBackground = 'style="background: no-repeat center center scroll; background-image: url('.$siteCategory->sImage.'); "';
            ?>

            <a href="<?=$siteCategory->getFullURL()?>">
                <span class="info-box-icon bg-white" <?=$imageBackground?>>
                    <?php
                        echo $icon;
                        if (!$siteCategory->bHideNameIconImage)
                            echo '<span class="sub-category-box-text">'.$siteCategory->sName.'</span>';
                    ?>
                </span>
            </a>

            <div class="sub-category-box-content">

                <?php

                    //if ($imageBackground!='')
                    {
                        //echo '<span class="sub-category-box-number">'.$siteCategory->sName.'</span>';
                    }

                    /*<span class="sub-category-box-number"><?= $siteCategory->sName?></span>
                     * <span class="sub-category-box-text"><?= $siteCategory->sDescription ?></span>
                    */
                ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->


<?php
/*<a href="<?=$siteCategory->getSubCategoryURL()?>">

    <div class="col-md-3 col-sm-3 col-xs-4 feature-bg wow fadeInUp" data-wow-delay=".3s">
        <div class="feature-inner text-center">
            <div class="icon-box">
                <i class="<?= $siteCategory->sImage?>"></i>
            </div>

            <div class="feature-content">
                <h3><?= $siteCategory->sName?></h3>
                <p><?= $siteCategory->sDescription ?></p>
            </div>
        </div>
    </div> <!-- item1 end -->
</a>*/