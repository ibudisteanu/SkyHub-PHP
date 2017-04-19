<h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;"><?=$dtEmail->arrProperties['Title']?> from <strong><?=$dtEmail->arrProperties['SourceUser']->getFullName()?></strong></h3>
<div mc:edit="body" style="padding-top:10px; text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
    <p>Dear <strong><?=$dtEmail->Destination->getFullName()?></strong> - <?=$dtEmail->getDestinationEmail()?></strong> , <br/> <br/>
    </p>

    <p style="padding-left: 20px">
        <?php
            $this->ViewAvatarController = modules::load('user/View_avatar');
            $this->ViewAvatarController->showEmailNotificationPreviewAvatar($dtEmail->arrProperties['SourceUserId'])
        ?>
        </br>
        <strong>Message</strong>:<br/>
        <?=$dtEmail->arrProperties['Text']?>
        <br/> <br/>
        Date: <strong><?=$dtEmail->getCreationDateString()?></strong> <br/>
    </p> <br/><br/>

    <p>Kind Regards, <?=WEBSITE_NAME?> team </p>
</div>
