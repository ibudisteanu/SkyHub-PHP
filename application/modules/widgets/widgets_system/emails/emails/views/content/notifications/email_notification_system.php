<h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">New Notification <?=$dtEmail->arrProperties['Title']?></h3>
<div mc:edit="body" style="padding-top:30px; text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
    <p>Dear - <strong><?=$dtEmail->getDestinationEmail()?></strong>, <br/> <br/>
    </p>

    <p style="padding-left: 20px">
        A new notification on <?=WEBSITE_NAME?> <br/><br/><br/>
        <strong>Message</strong>:<br/>
    </p>

    <p style="padding-left: 20px">
        <?=($dtEmail->arrProperties['Icon'] != '' ? '<i class="'.$dtEmail->arrProperties['Icon'].'" style="font-size: 33px;"></i>' : '')?> </br>
        <?=$dtEmail->arrProperties['Text']?>
        <br/> <br/>
        Date: <strong><?=$dtEmail->getCreationDateString()?></strong> <br/>
    </p> <br/><br/>

    <p>Kind Regards, <?=WEBSITE_NAME?> team </p>
</div>