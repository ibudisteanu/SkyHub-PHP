<tr>
    <td id="TopicTable_<?=$dtTopicPreview->sID?>" class="anchor">


        <div id="TopicSubmissionForm_<?=$dtTopicPreview->sID?>" class="anchor" > </div>


        <?=$this->VotingController->renderVoting($dtTopicPreview->objVote,true,'voting-topic-preview-question')?>

        <div id="TopicBody_<?=$dtTopicPreview->sID?>" class="anchor" style="padding-left:42px">
            <?=$this->load->view('forum_topic_preview_body_view')?>
        </div>

        <?php  $User = $this->UsersMinimal->userByMongoId($dtTopicPreview->sAuthorId);?>

        <?php if($User != null) : ?>
                <div style="display: inline">
                    <?= $this->ViewAvatarController->showTopicPreviewAvatar($dtTopicPreview->sAuthorId) ?>
                    <h5 class="avatar-topic-preview-author-name"><a href="<?=base_url('profile/'.$User->getUserLink())?>"><?=$User->sName?></a></h5>
                </div>
        <?php endif; ?>

        <span class="time" data-toggle="tooltip" data-placement="right"  title="<?=$dtTopicPreview->getCreationDateString()?>"><i class="fa fa-clock-o"></i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($dtTopicPreview->getCreationDate())?></span>
        <?=($this->TimeLibrary->getTimeDifferenceDateAndNowInDays($dtTopicPreview->getCreationDate()) < 3 ? '<span class="label label-danger">New</span>' : '' ) ?>
        <br/>
        <div class="topic-question-footer">

            <?php
                echo $this->AddReplyInlineController->renderReplyAddButton($dtTopicPreview->sID, $dtTopicPreview->getTitleForIdName(), $dtTopicPreview->sID);
                if ($bReplyOwner)
                {
                    echo $this->AddTopicInlineController->renderTopicEditButton($dtTopicPreview->sParentId, $dtTopicPreview->sID, $dtTopicPreview->sTitle,'topic-preview-table-body');
                    echo $this->AddTopicInlineController->renderTopicDeleteButton($dtTopicPreview->sID, $dtTopicPreview->sTitle);
                }
            ?>
            <div id="addReplyBox<?=$dtTopicPreview->sID?>" style="overflow: hidden"></div>
            <div id="commentStatus<?=$dtTopicPreview->sID?>"  style="overflow: hidden"></div>

            <div id="addReplyBox<?=$dtTopicPreview->sID?>" style="overflow: hidden"></div>

        </div>

        <?=$dtTopicPreview->objRepliesComponent->showInterestingComments()?>

    </td>
    <td><?=$dtTopicPreview->objRepliesComponent->iNoReplies?><br/>/<br/><?=$dtTopicPreview->objRepliesComponent->iNoUsersReplies?>
        <?php
            $data = $dtTopicPreview->objRepliesComponent->newCommentsForUser();
            $iNewCommentsForUser = $data['replies'];

            if (($data['visited']) || (!$this->MyUser->bLogged))
            {
                if ($iNewCommentsForUser >= 4) echo '<span class="label pull-right bg-red">' . $iNewCommentsForUser . '</span>'; else
                if ($iNewCommentsForUser >= 1) echo '<span class="label pull-right bg-yellow">' . $iNewCommentsForUser . '</span>'; else
                if ($iNewCommentsForUser == -1) echo '<span class="label label-red"> no date' . $iNewCommentsForUser . '</span>';
            }

            if (defined('WEBSITE_OFFLINE_DEBUG'))
                echo '<br/><span class="label label-success">'.count($dtTopicPreview->objRepliesComponent->arrLastReplies).':'.count($dtTopicPreview->objRepliesComponent->arrTopReplies).'</span>';

        ?>
    </td>
    <td><?=$dtTopicPreview->objVisitorsStatistics->getNumberViews()?><br/>/<br/><?=$dtTopicPreview->objVisitorsStatistics->getNumberUniqueVisitors()?></td>
</tr>