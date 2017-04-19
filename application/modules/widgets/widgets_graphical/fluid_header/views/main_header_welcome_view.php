<div class="col-md-6 col-md-offset-1 col-sm-7 col-sm-offset-0 col-xs-6 col-xs-offset-0 col-xxs-12 col-xs-offset-0 col-tn-12 col-tn-offset-0 " style="padding-bottom: 20px; padding-right: 0px; padding-left: 0px; overflow: hidden; display:table; ">
    <?php
    //echo '<img src="'.$g_sThemeURL.'/images/SkyHub-logo.png'.'" alt=>';
    //echo '<div class="logo-slider" style="background-image:url('.$g_sThemeURL.'/images/SkyHub-logo.png'.')"></div>';
    ?>

        <div style="vertical-align:middle;">
            <div style="text-align:center; display:block; overflow: hidden;">
                <!--<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url(<?=$g_sThemeURL.'images/SkyHub-logo.png'?>); background-repeat: no-repeat; background-size: contain;"></div> -->
                <img style="max-width:100%; max-height:100%;" src="<?=$g_sThemeURL.'images/SkyHub-logo.png'?>" alt="<?=WEBSITE_NAME?> Social Platform" title="<?=WEBSITE_NAME?> Social Platform" >-->
                <h1><?=WEBSITE_NAME?></h1>
                <h2>Connect, discover & change the world</h2>
                <ul class="top-link list-inline" style="margin-top: 25px;">
                    <li>
                        <a href="<?=base_url('#Registration')?>"><i class="fa fa-key"></i> Register</a>
                    </li>
                </ul></div>
        </div>


</div>



<?php
    if (isset($g_dtLoginBox))
        echo $g_dtLoginBox;
