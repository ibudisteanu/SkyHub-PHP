
<h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Your account has been created</h3>
<div mc:edit="body" style="padding-top:30px; text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
    <p>Here are the <strong>details</strong> of your <strong>SkyHub Account</strong>: <br/></p>
    <p>

        <?= (($dtEmail->getDestinationEmail() != null) ? 'Email Address: <strong>'.$dtEmail->getDestinationEmail().' </strong><br/>' : '') ; ?>
        Name: <strong><?=is_object($dtEmail->Destination) ? $dtEmail->Destination->getFullName() : 'no full name'?> </strong><br/>
        Username: <strong><?=is_object($dtEmail->Destination) ? $dtEmail->Destination->sUserName : 'no username'?></strong> <br/> <br/>

        Join date: <strong><?=is_object($dtEmail->Destination) ? $dtEmail->Destination->getCreationDateString() : 'no date'?></strong> <br/>
    </p>
</div>
