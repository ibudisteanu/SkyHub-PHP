
<div <?=isset($boxSize) ? 'class="'.$boxSize.'"' : ''?> <?= isset($boxStyle) ? 'style="'.$boxStyle.'"' : '' ?>>
    <div class="box box-widget widget-user-2" style="margin:0; text-align:left">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <a href="<?=$dtForumPreview->getFullURL()?>">
            <div class="widget-user-header bg-yellow" <?php if (isset($dtForumPreview->sCoverImage)) echo 'style="background: url('.$dtForumPreview->sCoverImage.') no-repeat center center scroll; background-size: cover"'?>>
                <div class="widget-user-image">
                    <img class="img" src="<?=$dtForumPreview->sImage?>" alt="<?=$dtForumPreview->sName?>">
                </div>
                <!-- /.widget-user-image -->
                <h3 class="widget-user-username"><?=$dtForumPreview->sName?></h3>
                <h5 class="widget-user-desc"><?=$dtForumPreview->sDescription?></h5>
            </div>
        </a>

        <div class="box-footer no-padding">

            <?php if (isset($dtForumContent)) : ?>
            <div class="col-md-12" style="padding:1px">
                <?=$dtForumContent?>
            </div>
            <?php endif;?>

        </div>
    </div>
</div>