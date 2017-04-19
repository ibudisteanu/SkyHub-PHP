<div class="form-group">
    <label class="col-lg-4 control-label">Images:</label>

    <div class="col-lg-8">

        <form action="<?=base_url('api/file-upload/upload/')?>" id="myForm" name="frmupload" method="post" enctype="multipart/form-data">

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                <input class="form-control" type="text" id="<?=$sFileUploadFormId?>" name="<?=$sFileUploadFormId?>" value="<?=isset($_POST[$sFileUploadFormId])?$_POST[$sFileUploadFormId]:''?>" placeholder="http://picture.jpg            OR                fa-battery-empty">
            </div>

            <h6><strong>OR </strong> Upload a different photo...</h6>

            <div class="row input-group" style="display: inline-flex;">
                <input type="file" id="upload_file" name="upload_file" />
                <button type="submit" class="btn btn-primary" name='submit_image' value="Submit Comment" onclick='upload_image();'>
                    <i class="fa fa-upload"> Upload</i>
                </button>
            </div>


        </form>

        <div class='progress' id="progress_div">
            <div class='bar' id='bar1'></div>
            <div class='percent' id='percent1'>0%</div>
        </div>

        <div id='output_image'>

    </div>
</div>

<script>
    $('#upload_file').filestyle({ buttonName: 'btn-success',  buttonText: ' Upload Cover',  iconName: 'glyphicon glyphicon-folder-open' });
</script>