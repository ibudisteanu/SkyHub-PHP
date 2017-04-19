<a class="anchor" id="AddForumCategory"></a>
<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center; padding-top:20px">
    <div class="box box-primary">
        <div class="box-header">
            <i class="ion ion-clipboard"></i>

            <?php
                //Title
                switch ($sActionName)
                {
                    case 'add-forum-category':
                        echo '<h3 class="box-title">Add <strong>Forum Category</strong> in <strong>'.(isset($dtCurrentForum) ? $dtCurrentForum->sName : '') .'</strong></h3>';
                        break;
                    case 'edit-forum-category':
                        echo '<h3 class="box-title">Edit <strong>'.$dtCurrentForumCategory->sName.'</strong> from <strong>'.$_POST['addForumCategory-parentCategoryName'].'</strong> </h3>';
                        break;
                }
            ?>


        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <form class="form-horizontal" action="<?= base_url($sFormAction);?>" role="form" method="post" enctype= "multipart/form-data">

                <?php
                    $this->AlertsContainer->renderViewByName('g_msgAddForumCategorySuccess');
                    $this->AlertsContainer->renderViewByName('g_msgAddForumCategoryError');
                ?>


                <?php
                //for the EDIT option
                if (isset($dtCurrentForumCategory)&&($dtCurrentForumCategory!=null)) {
                    echo '<input type="hidden" name="forumCategoryId" value="'.$dtCurrentForumCategory->sID.'">';
                }
                ?>


                <div class="col-md-6 col-sm-6 col-xs-6 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">



                    <div class="form-group">
                        <label class="col-lg-4 control-label">Category Name:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                                <input class="form-control" name="addForumCategory-name" type="text" value="<?=isset($_POST['addForumCategory-name'])?$_POST['addForumCategory-name']:''?>" placeholder="Forum Name" required>
                            </div>

                        </div>
                    </div>

                    <?php if (TUserRole::checkUserRights(TUserRole::Admin)) : ?>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Importance Factor:</label>
                        <div class="col-lg-3">
                            <div  class="input-group">
                                <span class="input-group-addon"><i class="fa fa-thumbs-o-up"></i></span>
                                <input class="form-control" name="addForumCategory-importance" type="text" value="<?=(isset($_POST['addForumCategory-importance'])?$_POST['addForumCategory-importance']:'0') ?>" placeholder="Importance" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">URL Link:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                                <input class="form-control" name="addForum-urlName" type="text" value="<?=isset($_POST['addForum-urlName'])?$_POST['addForum-urlName']:''?>" placeholder="URL Name">
                            </div>

                        </div>
                    </div>

                    <?php endif; ?>

                </div>

                <div class="col-md-6 col-sm-6 col-xs-6 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Category Image / Icon:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                <input class="form-control" type="text" name="addForumCategory-imageIcon" value="<?=isset($_POST['addForumCategory-imageIcon'])?$_POST['addForumCategory-imageIcon']:''?>" placeholder="fa-battery-empty">
                            </div>

                            <h6><strong>OR </strong> Upload a different photo...</h6>

                            <input type="file" id="addForumCategoryImageUpload" name="addForumCategory-imageUpload" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Cover Image:</label>
                        <div class="col-lg-8">
                            <div  class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                <input class="form-control" type="text" name="addForumCategory-coverImage" value="<?=isset($_POST['addForumCategory-coverImage'])?$_POST['addForumCategory-coverImage']:''?>" placeholder="http://" >
                            </div>

                            <h6><strong>OR </strong> Upload a cover photo ...</h6>

                            <input type="file" id="addForumCategoryCoverImageUpload" name="addForumCategory-coverImageUpload" >
                        </div>
                    </div>

                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Description:</label>
                        <div class="col-lg-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                                <textarea id="addForumCategory-description" name="addForumCategory-description" class="form-control" rows="4" placeholder="Short Description" ><?=isset($_POST['addForumCategory-description'])?$_POST['addForumCategory-description']:''?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Keywords:</label>
                        <div class="col-lg-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-bold"></i></span>
                                <input class="form-control" type="text" name="addForumCategory-inputKeywords" value="<?=isset($_POST['addForumCategory-inputKeywords'])?$_POST['addForumCategory-inputKeywords']:''?>" placeholder="Keywords">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 center-block text-center">

                            <?php

                            switch ($sActionName)
                            {
                                case 'add-forum-category':
                                    echo '<input type="hidden" name="val" value="addForumCategory">';
                                    echo '<input type="submit" class="btn btn-primary" value="Add Forum Category">';
                                    break;
                                case 'edit-forum-category':
                                    echo '<input type="hidden" name="val" value="editForumCategory">';
                                    echo '<input type="submit" class="btn btn-primary" value="Save '.$dtCurrentForumCategory->sName.' changes">';
                                    break;
                            }?>
                            <span></span>
                            <input type="reset"  value="Reset" class="btn btn-warning" ">
                        </div>
                    </div>
                </div>


            </form>


        </div>
        <!-- /.box -->
    </div>
</div>

    <script>
        $('#addForumCategoryImageUpload').filestyle({
            buttonName : 'btn-success',
            buttonText : ' Upload PNG/Icon',
            iconName : 'glyphicon glyphicon-folder-open'
        });

        $('#addForumCategoryCoverImageUpload').filestyle({
            buttonName : 'btn-success',
            buttonText : ' Upload Cover',
            iconName : 'glyphicon glyphicon-folder-open'
        });
    </script>