<a class="anchor" id="AddTopicForm<?=$sParentId?><?=$sFormIndex?>"> </a>
<div id="addTopicFormContainer_<?=$sParentId?>_<?=$sFormIndex?>" class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center; padding-top:20px">
    <div class="panel panel-primary">
        <div class="panel-heading">

            <?php
                //Title
                if (isset($sActionName))
                switch ($sActionName)
                {
                    case 'add-topic':
                        echo '<h2 class="panel-title"><i class="ion ion-clipboard"></i>Add <strong>Topic</strong> in <strong>'.(isset($dtCurrentTopic) ? $dtCurrentTopic->sName : '').' '.(isset($dtParent) ? $dtParent->sName : ''). (isset($dtParentForumCategory) ? '->'.$dtParentForumCategory->sName : '') .'</strong></h2>';
                        break;
                    case 'edit-topic':
                        echo '<h2 class="panel-title"><i class="ion ion-clipboard"></i>Edit <strong>'.$dtCurrentTopic->sTitle.'</strong> from <strong>'.(isset($dtParent) ? $dtParent->sName: '').(isset($dtParentForumCategory) ? '->'.$dtParentForumCategory->sName.'</strong>' : '') .'</h2>';
                        break;
                }
            ?>

            <span id="addTopicFormChevron_<?=$sParentId?>_<?=$sFormIndex?>" bodyName="addTopicFormBody_<?=$sParentId?>_<?=$sFormIndex?>" class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>

        </div>
        <!-- /.box-header -->
        <div id="addTopicFormBody_<?=$sParentId?>_<?=$sFormIndex?>" class="panel-body" style="font-weight:normal">

            <form id="addTopicForm_<?=$sParentId?>_<?=$sFormIndex?>" class="form-horizontal" action="<?=base_url($sFormAction); ?>" role="form" method="post" enctype= "multipart/form-data">

                <div id="addForumTopicAlertsContainer_<?=$sParentId?>_<?=$sFormIndex?>">
                    <?php
                        $this->AlertsContainer->renderViewByName('g_msgAddForumTopicSuccess','center');
                        $this->AlertsContainer->renderViewByName('g_msgAddForumTopicError','center');
                    ?>
                </div>

                <?php
                    //EDIT option
                    if (isset($dtCurrentTopic)&&($dtCurrentTopic!=null))
                        echo '<input type="hidden" id="addForumTopicId_'.$sParentId.'_'.$sFormIndex.'_" name="addForumTopic-Id" value="'.$dtCurrentTopic->sID.'">';


                    if (isset($dtParent)&&($dtParent!=null))
                        echo '<input type="hidden" id="addForumTopicParentId_'.$sParentId.'_'.$sFormIndex.'" name="addForumTopic-ParentId" value="'.$dtParent->sID.'">';
                ?>


                <div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">
                    <div class="form-group">
                        <label class="col-md-1">Title</label>
                        <div class="col-md-11">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                                <input class="form-control" id="addForumTopicTitle_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-title" type="text" value="<?=isset($_POST['addForumTopic-title'])?$_POST['addForumTopic-title']:''?>" placeholder="<?=(isset($dtParent) && is_object($dtParent) && isset($dtParent->sName) ? $dtParent->sName : 'Topic Title')?>" onkeypress="titleKeyDown(this, '<?=$sParentId?>','<?=$sFormIndex?>')" onkeydown="titleKeyDown(this, '<?=$sParentId?>','<?=$sFormIndex?>')" onkeyup="titleKeyDown(this, '<?=$sParentId?>','<?=$sFormIndex?>')" required>
                                <label id="addForumTopicTitleCountLabel_<?=$sParentId?>_<?=$sFormIndex?>">Title: 0 chars</label>
                            </div>

                        </div>
                    </div>

                    <?php  if (TUserRole::checkUserRights(TUserRole::Admin)) : ?>

                    <div class="form-group">
                        <label class="col-md-2 col-sm-2">Importance Factor:</label>
                        <div class="cold-md-2 col-sm-3">
                            <div  class="input-group">
                                <span class="input-group-addon"><i class="fa fa-thumbs-o-up"></i></span>
                                <input class="form-control" id="addForumTopicImportance_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-importance" type="text" value="<?=(isset($_POST['addForumTopic-importance'])?$_POST['addForumTopic-importance']:'0')?>" placeholder="Importance" >
                            </div>
                        </div>

                        <label class="col-sm-1">URL Link:</label>
                        <div class="cold-md-7 col-sm-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                                <input class="form-control" id="addForumTopicUrlName_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-urlName" type="text" value="<?= (isset($_POST['addForumTopic-urlName'])?$_POST['addForumTopic-urlName']:'') ?>" placeholder="URL Link">
                            </div>

                        </div>

                    </div>

                    <?php endif ; ?>

                    <div class="form-group">
                        <label class="col-sm-2">Keywords:</label>
                        <div class="col-sm-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-bold"></i></span>
                                <input class="form-control" type="text" id="addForumTopicInputKeywords_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-inputKeywords" value="<?=isset($_POST['addForumTopic-inputKeywords'])?$_POST['addForumTopic-inputKeywords']:''?>" placeholder="<?=(isset($dtParent) && is_object($dtParent) && method_exists($dtParent,'getInputKeywordsToString') ? $dtParent->getInputKeywordsToString() : 'Keywords')?>">
                            </div>
                        </div>
                    </div>

                </div>


                <div class="col-md-12 col-sm-12 col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-lg-1 col-md-2 control-label">Topic Message:</label>
                        <div class="col-lg-11 col-md-10">
                            <textarea class="input-block-level" id="addForumTopicBodyCode_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-bodyCode" contenteditable="true" rows="18"><?=isset($_POST['addForumTopic-bodyCode'])?$_POST['addForumTopic-bodyCode']:'<p></p>'?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 ">Short Description:</label>
                        <div class="col-lg-10">

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                                <textarea id="addForumTopicShortDescription_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-shortDescription" class="form-control" rows="4" placeholder="Short Description" ><?=isset($_POST['addForumTopic-shortDescription'])?$_POST['addForumTopic-shortDescription']:''?></textarea>
                            </div>
                        </div>
                    </div>


                    <div id="addForumTopicLoadingDiv_<?=$sParentId?>_<?=$sFormIndex?>"  style="padding-top:10px; text-align: center; font-size: 30px; display: none">
                        <i class="fa fa-refresh fa-spin" style=" padding-bottom:10px; height: 30px;"></i>
                    </div>

                    <div id="addForumTopicSubmissionDiv_<?=$sParentId?>_<?=$sFormIndex?>" class="form-group" >

                        <div class="form-group">
                            <div class="col-md-12 center-block text-center">

                                <?php if($sActionName == 'add-topic') : ?>
                                    <button type="button" name="addForumTopic" onclick='return addForumTopicSubmission("addForumTopic","<?=$sParentId?>","","<?=$sFormIndex?>","<?=$sFormResponseType?>");' class="btn btn-primary">
                                         <i class="fa fa-plus"> Add a new Topic in <strong><?=substr($dtParent->sName,0,40)?></strong></i>
                                    </button>
                                <?php elseif ($sActionName == 'edit-topic') : ?>
                                    <button type="button"  name="editForumTopic" onclick='return addForumTopicSubmission("editForumTopic","<?=$sParentId?>","<?=$dtCurrentTopic->sID?>","<?=$sFormIndex?>","<?=$sFormResponseType?>");' class="btn btn-primary" >
                                        <i class="fa fa-plus"> Save <strong><?=substr($dtCurrentTopic->sTitle,0,40)?></strong></i>
                                    </button>
                                    <span style="padding-right: 30px"></span>
                                    <button type="submit"  name="deleteForumTopic" onclick='return addForumTopicSubmission("deleteForumTopic","<?=$sParentId?>","<?=$dtCurrentTopic->sID?>","<?=$sFormIndex?>","<?=$sFormResponseType?>");' class="btn btn-danger" value="Delete topic">
                                        <i class="fa fa-times"> <strong>Delete </strong>topic</i>
                                    </button>
                                <?php endif ; ?>

                                <span>   </span>
                                <button type="reset"  value="Reset"  class="btn btn-danger" onclick='return addForumTopicReset("<?=$sParentId?>","<?=$sFormIndex?>");'>
                                    <i class="fa fa-repeat"> Reset</i>
                                </button>

                                <button type="button" class="btn btn-warning" onclick='return addForumTopicCancel("<?=$sParentId?>","<?=$sFormIndex?>");'>
                                    <i class="fa fa-times"> Cancel</i>
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="col-xs-12 wow fadeInUp" data-wow-delay=".3s" style="align-content: center;">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-image"></i> Advanced Images</h3>
                        <span bodyName="advancedImagesFormBody_<?=$sParentId?>_<?=$sFormIndex?>" class="pull-right clickable panel-collapsed"><i class="glyphicon glyphicon-chevron-down"></i></span>
                    </div>

                    <div id="advancedImagesFormBody_<?=$sParentId?>_<?=$sFormIndex?>"  class="panel-body" style="display: none;">

                        <?=$this->FilesUploadSystemController->showFileUploadContainer('forumTopicImages','forumTopicsImages_'.$sParentId.'_'.$sFormIndex,'jpeg|jpg|png|icon',true)?>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Image / Icon:</label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                    <input class="form-control" type="text" id="addForumTopicImage_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-image" value="<?=isset($_POST['addForumTopic-image'])?$_POST['addForumTopic-image']:''?>" placeholder="http://picture.jpg            OR                fa-battery-empty">
                                </div>

                                <h6><strong>OR </strong> Upload a different photo...</h6>

                                <input type="file" id="addForumTopicImageUpload_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-imageUpload" >
                            </div>
                        </div>


                        <div class="form-group" >
                            <label class="col-lg-4 control-label">Cover Image:</label>
                            <div class="col-lg-8">
                                <div  class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                                    <input class="form-control" type="text" id="addForumTopicCoverImage_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-coverImage" value="<?=isset($_POST['addForumTopic-coverImage'])?$_POST['addForumTopic-coverImage']:''?>" placeholder="http://" >
                                </div>

                                <h6><strong>OR </strong> Upload a cover photo ...</h6>

                                <input type="file" id="addForumTopicCoverImageUpload_<?=$sParentId?>_<?=$sFormIndex?>" name="addForumTopic-coverImageUpload" >
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- /.box -->

    </div>

</div>

<?php
    $this->BottomScriptsContainer->addScript('window.onload = function() { initializeAddForumTopicForm("'.$sParentId.'","'.$sFormIndex.'") };', TRUE);
?>
