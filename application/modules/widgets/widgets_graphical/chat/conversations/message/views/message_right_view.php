<div id="ChatMessage_<?=$Message->sID?>" class="direct-chat-msg right">
    <div class="direct-chat-info clearfix">
        <span class="direct-chat-name pull-right"><?=$User->getFullName()?></span>
        <span class="direct-chat-timestamp pull-left">
            <span class="time" data-toggle="tooltip" data-placement="right"  title="<?=$Message->getCreationDateString()?>"><i class="fa fa-clock-o"> </i> <?=$this->TimeLibrary->getTimeDifferenceDateAndNowString($Message->getCreationDate())?></span>
        </span>
    </div>
    <!-- /.direct-chat-info -->
    <img class="direct-chat-img" src="<?=$User->sAvatarPicture?>" alt="<?=$User->getFullName()?>"><!-- /.direct-chat-img -->
    <div class="direct-chat-text">
        <?=$Message->sBody?>
    </div>
    <!-- /.direct-chat-text -->
</div>
