<div class="col-md-12 col-sm-12 col-xs-12 slider wow fadeInUp" style="background-url: url("<?= $this->MyUser->sBackgroundImageLink != '' ? $this->MyUser->sBackgroundImageLink  : $g_sThemeURL.'images/showcase-bg.jpg' ?>") data-wow-delay=".3s" style="padding=0 0 0 0; border=0px">
    <!-- slider section -->
    <div class="row">
            <div class="slider-wrap">
                <div class="texture-overlay"></div>


                        <div class="fb-profile">
                            <div class="fb-profile-text" style="padding-left 0; padding-top: 3%;   ">
                                <h1><?= $g_User->sFirstName . ' <strong>'. $g_User->sLastName ?></strong></h1>
                            </div>
                            <img align="left" class="fb-image-profile thumbnail" src="<?= $g_User->sAvatarPicture?>" alt="<?=$g_User->getFullName()?>" style="margin: -20px 15px 0px 35px"/>
                            <div class="fb-profile-text">
                                <h2 style="padding-top:0"><?= '['.$g_User->sUserName.']'?></h2>
                                <?= (strlen($g_User->sBiography)) > 0 ? '<p>'.$g_User->sBiography.'</p>':''?>
                                <?= (strlen($g_User->sWebsite)) > 0 ? '<p><a href="'.$g_User->sWebsite.'">'.$g_User->sWebsite.'</p></a>':''?>
                            </div>
                        </div>

                    </div>
                </div> <!-- row end  -->

    </div>
    <!-- slider section end -->
</div>


<?php
    if (isset($g_dtLoginBoxHeader))
        echo $g_dtLoginBoxHeader;

    echo $HeaderToolBox->renderMenu('header','float:right;');
?>
