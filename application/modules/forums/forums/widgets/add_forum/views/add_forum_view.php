<a class="anchor" id="AddForum"></a>
<div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center; padding-top:20px">
    <div class="box box-primary">
        <div class="box-header">
            <i class="ion ion-clipboard"></i>

            <?php
                //Title
                switch ($sActionName)
                {
                    case 'add-forum':
                        echo '<h3 class="box-title">Add <strong>Forum</strong> in <strong>'.(isset($_POST['addForum-parentCategoryName']) ? $_POST['addForum-parentCategoryName'] : '') .'</strong></h3>';
                        break;
                    case 'edit-forum':
                        echo '<h3 class="box-title">Edit <strong>'.$dtCurrentForum->sName.'</strong> from <strong>'.(isset($_POST['addForum-parentCategoryName']) ? $_POST['addForum-parentCategoryName'] : '') .'</strong></h3>';
                        break;
                }
            ?>


        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <form class="form-horizontal" action="<?= base_url($sFormAction);?>" role="form" method="post" enctype= "multipart/form-data">

                <?php
                    $this->AlertsContainer->renderViewByName('g_msgAddForumSuccess');
                    $this->AlertsContainer->renderViewByName('g_msgAddForumError');
                ?>


                <?php
                //EDIT option
                if (isset($dtCurrentForum)&&($dtCurrentForum!=null)) {
                    echo '<input type="hidden" name="forum_id" value="'.$dtCurrentForum->sID.'">';
                }
                ?>


                <div class="col-md-6 col-sm-6 col-xs-6 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">


                    <div class="form-group">
                        <label class="col-lg-4 control-label">Parent Category:</label>
                        <div class="col-lg-8">
                            <div class="input-group" style="margin-bottom: 20px" >
                                <span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
                                <select class="form-control"  name="addForum-parentCategory">
                                    <?php

                                    //if ((isset($_POST['addForum-parentCategory']) && ($_POST['addForum-parentCategory']=='')))

                                    foreach ($dtSiteCategories as $Category)
                                    {
                                        echo '<option value="'.$Category->sID.'" ';
                                        echo ((isset($_POST['addForum-parentCategory']) && ($_POST['addForum-parentCategory']==$Category->sID)) ?  ' selected="selected"' : '');
                                        echo '>';
                                        echo $Category->sFullURLLink;
                                        echo '</option>';
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Forum Name:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                                <input class="form-control" name="addForum-name" type="text" value="<?=isset($_POST['addForum-name'])?$_POST['addForum-name']:''?>" placeholder="Forum Name" required>
                            </div>

                        </div>
                    </div>

                    <?php  if (TUserRole::checkUserRights(TUserRole::Admin)): ?>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">URL Link:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                                <input class="form-control" name="addForum-urlName" type="text" value="<?=isset($_POST['addForum-urlName'])?$_POST['addForum-urlName']:''?>" placeholder="URL Name">
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Importance Factor:</label>
                        <div class="col-lg-3">
                            <div  class="input-group">
                                <span class="input-group-addon"><i class="fa fa-thumbs-o-up"></i></span>
                                <input class="form-control" name="addForum-importance" type="text" value="<?=(isset($_POST['addForum-importance'])?$_POST['addForum-importance']:'0')?>" placeholder="Importance" >
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>

                </div>

                <div class="col-md-6 col-sm-6 col-xs-6 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Forum Image / Icon:</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                <input class="form-control" type="text" name="addForum-imageIcon" value="<?=isset($_POST['addForum-imageIcon'])?$_POST['addForum-imageIcon']:''?>" placeholder="fa-battery-empty">
                            </div>

                            <h6><strong>OR </strong> Upload a different photo...</h6>

                            <input type="file" id="addForumImageUpload" name="addForum-imageUpload" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Cover Image:</label>
                        <div class="col-lg-8">
                            <div  class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                <input class="form-control" type="text" name="addForum-coverImage" value="<?=isset($_POST['addForum-coverImage'])?$_POST['addForum-coverImage']:''?>" placeholder="http://" >
                            </div>

                            <h6><strong>OR </strong> Upload a cover photo ...</h6>

                            <input type="file" id="addForumCoverImageUpload" name="addForum-coverImageUpload" >
                        </div>
                    </div>

                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Description:</label>
                        <div class="col-lg-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                                <textarea id="addForum-description" name="addForum-description" class="form-control" rows="2" placeholder="Short Description" ><?=isset($_POST['addForum-description'])?$_POST['addForum-description']:''?></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-lg-2 control-label">Advanced Description:</label>
                        <div class="col-lg-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                                <textarea id="addForum-detailedDescription" name="addForum-detailedDescription" class="form-control" rows="5" placeholder="Detailed Description" ><?=isset($_POST['addForum-detailedDescription'])?$_POST['addForum-detailedDescription']:''?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-5" style="padding-bottom: 10px;">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-bold"></i></span>
                                <input class="form-control" type="text" name="addForum-inputKeywords" value="<?=isset($_POST['addForum-inputKeywords'])?$_POST['addForum-inputKeywords']:''?>" placeholder="Keywords">
                            </div>
                        </div>
                        <div class="col-sm-3" style="padding-bottom: 10px;">
                            <div class="form-item" style="text-align: left; ">
                                <input id="addForumCountry" type="text" style="width: 100%;" >
                                <label for="addForumCountry" style="display:none;">Select a country here...</label>
                            </div>

                            <div class="form-item" style="display:none; text-align: left;">
                                <input type="text" id="addForumCountryCode" name="addForum-country" data-countrycodeinput="1"   readonly="readonly" placeholder="Selected country code will appear here" />
                                <label for="addForumCountryCode">...and the selected country code will be updated here</label>
                            </div>
                        </div>
                        <div class="col-sm-4" style="padding-bottom: 10px;">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                                <input type="text" class="form-control" name="addForum-city" placeholder="city" value="<?=isset($_POST['addForum-city'])?$_POST['addForum-city']:''?>"
                                       data-validation="length alpha"
                                       data-validation-length="2-18"
                                       data-validation-error-msg="City has to be an alphanumeric value (2-18 chars)" required >
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12 center-block text-center">

                            <?php if (($sActionName == 'add-new-forum') || ($sActionName == 'add-forum')) : ?>
                                <button type="submit" name="addForum" class="btn btn-primary">
                                    <i class="fa fa-plus"> Add Forum</i>
                                </button>
                            <?php elseif ($sActionName == 'edit-forum') : ?>
                                <button type="submit"  name="editForum" class="btn btn-primary" >
                                    <i class="fa fa-plus"> Save <strong><?=substr($dtCurrentForum->sName,0,40)?></strong></i>
                                </button>
                                <span style="padding-right: 30px"></span>
                                <button type="submit"  name="deleteForum" class="btn btn-danger" value="Delete forum">
                                    <i class="fa fa-times"> <strong>Delete </strong>topic</i>
                                </button>
                            <?php endif ; ?>

                            <span></span>
                            <button type="reset"  value="Reset"  class="btn btn-warning" >
                                <i class="fa fa-repeat"> Reset</i>
                            </button>
                        </div>
                    </div>
                </div>


            </form>


        </div>
        <!-- /.box -->
    </div>
</div>

    <script>
        $('#addForumImageUpload').filestyle({
            buttonName : 'btn-success',
            buttonText : ' Upload PNG/Icon',
            iconName : 'glyphicon glyphicon-folder-open'
        });

        $('#addForumCoverImageUpload').filestyle({
            buttonName : 'btn-success',
            buttonText : ' Upload Cover',
            iconName : 'glyphicon glyphicon-folder-open'
        });
    </script>


<?php $this->BottomScriptsContainer->addScript("<script>
    $('#addForumCountry').countrySelect({
        ". ((isset($_POST['addForum-country'])) ? "defaultCountry: '".$_POST['addForum-country']."',":'').
    //onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
    "preferredCountries: ['ca', 'gb', 'us'".((isset($_POST['addForum-country'])&&(!in_array($_POST['addForum-country'],['ca', 'gb', 'us']))) ? ",'".$_POST['addForum-country']."'":'')."]
    });
</script>");?>