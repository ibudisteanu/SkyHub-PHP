<a class="anchor" id="AvatarUpload"></a>

<div class="box box-info">

    <div class="box-header">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">Edit your avatar</h3>
    </div>

    <?php
        $this->AlertsContainer->renderViewByName('g_msgAvatarUploadSuccess');
        $this->AlertsContainer->renderViewByName('g_msgAvatarUploadError');
    ?>

    <form class="form-horizontal" action="<?= base_url('/profile/edit');?>" role="form" method="post" enctype= "multipart/form-data">

        <input type="hidden" name="val" value="upload_avatar">

        <div class="text-center" style="padding-top:5px">
            <img align="left" class="fb-image-profile thumbnail img-responsive" src="<?= $g_User->sAvatarPicture?>" alt="<?=$g_User->getFullName()?>" style="margin-left:1px; width:initial"/>
            <h6>Upload a different photo...</h6>

            <input type="file" id="AvatarImageFileUpload" name="avatarUpload-AvatarImageFile" >
        </div> <br/>
        <div class="form-group">
            <label class="col-lg-4 control-label"></label>
            <input type="submit" class="btn btn-primary" value="Upload" >
        </div> <br/>

    </form>
</div>


<script>
    $('#AvatarImageFileUpload').filestyle({
        buttonName : 'btn-success',
        buttonText : ' Upload Avatar',
        iconName : 'glyphicon glyphicon-folder-close'
    });
</script>