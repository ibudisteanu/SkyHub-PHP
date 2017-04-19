<!-- timeline item -->
<a class="anchor" id="<?=$dtReply->sID?>"></a>
<li id="replyId<?=$dtReply->sID?>" style="overflow: hidden;">

    <!--<div class="verticalBar"></div> -->

    <div class="timeline-item" style="<?=$dtReply->getNestedLevelBackgroundColor()?> ">
        <span class="time" data-toggle="tooltip" data-placement="left"  title="<?=$dtReply->getCreationDateString()?>"><i class="fa fa-clock-o"></i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($dtReply->getCreationDate())?></span>

        <?php
        $User = $this->UsersMinimal->userByMongoId($dtReply->sAuthorId);
        ?>
        <h3 class="timeline-header">

            <div style="display: inline;">
                <?=$this->ViewAvatarController->showReplyAvatar($dtReply->sAuthorId)?>
            </div>

            <a href="<?=base_url('profile/'.$User->getUserLink())?>"><?=$User->sName?></a>

            <div id="replyTitle<?=$dtReply->sID?>" style="display: inline;">
                <?=$dtReply->sTitle?>
            </div>
            <br/>
        </h3>
        <?= isset($User->sBiography) ? '<h4 class="timeline-sub-header">'.$User->sBiography.'</h4>' :'' ?>

        <div class="formHeadLine"> </div>

        <div style="display: flex">
            <div>
                <?= $this->VotingController->renderVoting($dtReply->objVote,true,'voting-reply'); ?>
            </div>

            <div id="replyBody<?=$dtReply->sID?>" class="timeline-body">
                <?=$dtReply->getMessageCodeRendered()?>
            </div>
        </div>

        <?php
        if (count($dtReply->arrChildren) > 0) $bHaveRepliesChildren=true;
        else $bHaveRepliesChildren=false;
        ?>

        <div class="timeline-footer" style="overflow: hidden; <?=!$bHaveRepliesChildren ? "padding-bottom:10px" : '' ?> ">
            <?php
            echo $this->AddReplyInlineController->renderReplyAddButton($dtReply->sID, $dtReply->getTitleForIdName(), $dtReply->getAttachedGrandParentId());
            if ($bReplyOwner)
            {
                echo $this->AddReplyInlineController->renderReplyEditButton($dtReply->sID, $dtReply->sTitle, $dtReply->getAttachedGrandParentId());
                echo $this->AddReplyInlineController->renderReplyDeleteButton($dtReply->sID, $dtReply->sTitle, $dtReply->getAttachedGrandParentId());
            }
            ?>
            <div id="addReplyBox<?=$dtReply->sID?>" style="overflow: hidden"></div>
            <div id="commentStatus<?=$dtReply->sID?>"  style="overflow: hidden"></div>
        </div>

        <?php
        if ($DisplayAdsAlgorithmController != null)
            $Ads = $DisplayAdsAlgorithmController->renderCommentAds();
        ?>

        <div id = "timelineSubReplies<?=$dtReply->sID?>" class="timeline-footer" style="<?= ((($bHaveRepliesChildren==false) && ((!isset($Ads)) || ($Ads==null)||($Ads==''))) ? "display:none;" : 0) ?> overflow: hidden; padding-bottom:0px; ">
            <div class="timeline-SubReplies" >
                <!-- The time line -->
                <ul class="timeline" <?= ($bDrawMinimalReplies==true) ? 'style=margin-bottom:5px' : '' ?> >

                    <?php
                    foreach ($dtReply->arrChildren as $Reply)
                        echo $this->ViewReplyController->renderReply($Reply, $DisplayAdsAlgorithmController, true, $bDrawMinimalReplies);

                    ?>

                    <li id="repliesNewContainer<?=$dtReply->sID?>" style="overflow: hidden; margin-bottom:0px"> </li>

                    <?php
                    if ((isset($Ads))&&($Ads != ''))
                    {
                        echo '<li id="endIcon" style="padding-bottom: 20px;">
                                        <i class="fa fa-commenting"></i>
                                 </li>';
                        echo '</li>';
                        echo $Ads;
                    }
                    ?>


                </ul>
            </div>

        </div>



    </div>
</li>
<!-- END timeline item -->