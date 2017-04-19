<h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Confirmation of Email sent to <?=WEBSITE_NAME?></h3>
<div mc:edit="body" style="padding-top:30px; text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
    <p>Dear <strong><?=$dtEmail->arrProperties['FullName']?></strong> - <?=$dtEmail->getDestinationEmail()?>, <br/> <br/>
    </p>

    <p style="padding-left: 20px">
        Your email has been successfully sent to <?=WEBSITE_NAME?>. Shortly, you will receive an answer from us! <br/><br/><br/>
        <strong>Message</strong>:<br/>
    </p>

    <p style="padding-left: 20px">
        <?=$dtEmail->arrProperties['Message']?>
        <br/> <br/>
        Date: <strong><?=$dtEmail->getCreationDateString()?></strong> <br/>
    </p> <br/><br/>

    <p>Kind Regards, <?=WEBSITE_NAME?> team </p>
</div>