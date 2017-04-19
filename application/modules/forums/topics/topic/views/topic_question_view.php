<div id="TopicHeader_<?=$dtTopic->sID?>" class="anchor col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 col-xxs-12 col-xxs-offset-0 col-tn-12 col-tn-offset-0" style="padding-left: 40px; padding-bottom: 20px">

    <?=$this->ViewAvatarController->showTopicAvatar($dtTopic->sAuthorId)?>

    <?=$this->VotingController->renderVoting($dtTopic->objVote,true,'voting-topic-question')?>

    <div class="topic-question" style="overflow: hidden; ">

        <time class="date information" datetime="<?=$dtTopic->getCreationDateString()?>" data-toggle="tooltip" data-placement="left"  title="<?=$dtTopic->getCreationDateString()?>"><i class="fa fa-clock-o"></i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($dtTopic->getCreationDate())?></time>
        <span class="views information" data-toggle="tooltip" data-placement="left"  title="Views <?=$dtTopic->objVisitorsStatistics->getNumberViews()?>"><i class="fa fa-eye"></i> <?=$dtTopic->objVisitorsStatistics->getNumberViews()?></span>
        <span class="unique-views information" data-toggle="tooltip" data-placement="left"  title="Unique Views <?=$dtTopic->objVisitorsStatistics->getNumberUniqueVisitors()?>"><i class="fa fa-eye-slash"></i> <?=$dtTopic->objVisitorsStatistics->getNumberUniqueVisitors()?></span>

        <?php
            $User = $this->UsersMinimal->userByMongoId($dtTopic->sAuthorId);
        ?>
        <a class="topic-question-header author" href="<?=base_url('profile/'.$User->getUserLink())?>"> <?=$User->sName?></a>
        <h1>
            <?=$dtTopic->sTitle?>
        </h1>

        <?= isset($User->sBiography) ? '<h4 class="topic-question-sub-header">'.$User->sBiography.'</h4>' :'' ?>
        <div class="formHeadLine"> </div>

        <div id="TopicSubmissionForm_<?=$dtTopic->sID?>" class="anchor" > </div>

        <div id="TopicBody_<?=$dtTopic->sID?>" class="articleContent anchor">
            <?=$this->load->view('topic_question_body_view')?>
        </div>

        <?php
            if ((isset($dtRepliesContainer))&&($dtRepliesContainer != null)&&(isset($dtRepliesContainer->arrChildren))&&(count($dtRepliesContainer->arrChildren) > 0)) $bHaveRepliesChildren=true;
            else $bHaveRepliesChildren=false;
        ?>

        <div class="topic-question-footer">
            <div class="col-xs-12 col-sm-5 topic-question-footer-buttons" style="overflow: hidden; <?=!$bHaveRepliesChildren ? "padding-bottom:10px" : '' ?> ">
                <?php
                    echo $this->AddReplyInlineController->renderReplyAddButton($dtTopic->sID, $dtTopic->getTitleForIdName(), $dtTopic->sID);
                    if ($bReplyOwner)
                    {
                        echo $this->AddTopicInlineController->renderTopicEditButton($dtTopic->sParentId, $dtTopic->sID, $dtTopic->sTitle, 'topic-preview');
                        echo $this->AddTopicInlineController->renderTopicDeleteButton($dtTopic->sID, $dtTopic->sTitle);
                    }
                ?>
            </div>

            <?php if ($dtTopic->getLastChangeDateExistence()): ?>
                <div class="col-xs-12 col-sm-7 topic-question-footer-later-edit" style="text-align: right;">
                    <time class="date information"  datetime="<?=$dtTopic->getCreationDateString()?>" data-toggle="tooltip" data-placement="left"  title="<?=$dtTopic->getLastChangeDateString()?>"><i class="fa fa-clock-o"></i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($dtTopic->getLastChangeDateString())?></time>
                    <?php
                        if ($dtTopic->sLastChangeUserId != '') {
                            echo 'by <i class="glyphicon glyphicon-user"></i>';
                            $User = $this->UsersMinimal->userByMongoId($dtTopic->sLastChangeUserId);
                            echo ' <span> <a href='.base_url('profile\\'.$User->getUserLink()).'">'.$User->sName.'</a> </span>';
                        }
                    ?>
                </div>
            <?php endif ; ?>
            </div>

            <?= modules::load('tags/view_tags')->renderTags($dtTopic->getTags());  ?>

            <div class="col-xs-12 replies">
                <div id="commentStatus<?=$dtTopic->sID?>"  style="overflow: hidden"></div>

                <div id="addReplyBox<?=$dtTopic->sID?>" style="overflow: hidden"></div>
            </div>
    </div>
    <!-- /.box -->

</div>
<!-- /.col -->