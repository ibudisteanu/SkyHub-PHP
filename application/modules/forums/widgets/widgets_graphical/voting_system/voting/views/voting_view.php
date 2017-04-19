<div id="Vote<?=$sVoteIdName?>"class="upvote <?=$sVotingStyleClass?>" >
    <a class="upvote <?=((isset($Vote)&&($Vote->iUserVoteStatus==1)) ?'upvote-on' : '')?> upvote-enabled" title="This is good stuff. Vote it up! (Click again to undo)"></a>
    <span class="count" title="Total number of votes"><?=(isset($Vote) ? $Vote->iVoteCountValue : 333)?></span>
    <a class="downvote <?=((isset($Vote)&&($Vote->iUserVoteStatus==-1)) ?'downvote-on' : '')?> upvote-enabled" title="This is not useful. Vote it down. (Click again to undo)"></a>
    <a class="star <?=(isset($Vote) ? ($Vote->bUserVoteStarMarked?'star-on ':'') : '')?> upvote-enabled" title="Mark as favorite. (Click again to undo)"></a>
</div>

<?php
    //$script = "<script>$('#Vote".$sVoteIdName."').upvote({id: '$sVoteIdName', ".( (isset($Vote) && $Vote->sGrandParentObjectId) != '' ? "grandParentId:'".$Vote->sGrandParentObjectId."'," : "" )." callback: processingVoteCallback});</script>";
    $script = "<script>$('#Vote".$sVoteIdName."').upvote({id : '$sVoteIdName', parentObjectId : '".(isset($Vote) ? $Vote->sParentObjectId : '')."', grandParentObjectId : '".(isset($Vote) ? $Vote->sGrandParentObjectId : '') ."', callback: processingVoteCallback});</script>";

    $this->BottomScriptsContainer->addScript($script,false,'voteActivation');
?>
<?php //$this->BottomScriptsContainer->addScript("<script>$('div#".$sVoteIdName.".upvote"."').upvote('upvote');</script>")?>
