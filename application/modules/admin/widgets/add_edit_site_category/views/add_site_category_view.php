<a class="anchor" id="AddSiteCategory"></a>
<div class="box box-primary">
    <div class="box-header">
        <i class="ion ion-clipboard"></i>

        <?php
        //Title
        switch ($sActionName)
        {
            case 'add-category':
            case 'add-subcategory':
                echo '<h3 class="box-title">Add Site '.( ((isset($_POST['addSiteCategory-parentCategory'])) && ($_POST['addSiteCategory-parentCategory'] != '')) ? '<strong>Category</strong> in <strong>'.$_POST['addSiteCategory-parentCategory'].'</strong>' : '<strong>Category</strong> ') .'</h3>';
                break;
            case 'edit-category':
                echo '<h3 class="box-title">Edit <strong>'.$dtSiteCurrentCategory->sName.'</strong> from <strong>'.( ((isset($_POST['addSiteCategory-parentCategory'])) && ($_POST['addSiteCategory-parentCategory'] != '')) ? $_POST['addSiteCategory-parentCategory'] : 'no parent') .'</strong></h3>';
                break;
        }
        ?>

    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <form class="form-horizontal" action="<?= base_url($sFormAction);?>" role="form" method="post" enctype= "multipart/form-data">

            <?php
                $this->AlertsContainer->renderViewByName('g_msgAddSiteCategorySuccess');
                $this->AlertsContainer->renderViewByName('g_msgAddSiteCategoryError');
            ?>

            <?php
                //EDIT option
                if (isset($dtSiteCurrentCategory)&&($dtSiteCurrentCategory!=null)) {
                    echo '<input type="hidden" name="category_id" value="'.$dtSiteCurrentCategory->sID.'">';
                }
            ?>

            <div class="form-group">
                <label class="col-lg-3 control-label">Parent Category:</label>
                <div class="col-lg-9">
                    <div class="input-group" style="margin-bottom: 20px" >
                        <span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
                        <select class="form-control"  name="addSiteCategory-parentCategory">
                            <?php

                                //if ((isset($_POST['addSiteCategory-parentCategory']) && ($_POST['addSiteCategory-parentCategory']=='')))
                                echo '<option></option>';


                                foreach ($dtSiteCategories as $Category)
                                {
                                    echo '<option value="'.$Category->sID.'" ';
                                    echo ((isset($_POST['addSiteCategory-parentCategory']) && ($_POST['addSiteCategory-parentCategory']==$Category->sID)) ?  ' selected="selected"' : '');
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
                <label class="col-lg-3 control-label">Category Name:</label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                        <input class="form-control" name="addSiteCategory-name" type="text" value="<?=isset($_POST['addSiteCategory-name'])?$_POST['addSiteCategory-name']:''?>" placeholder="Category Name" required>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">URL Name:</label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-link"></i></span>
                        <input class="form-control" name="addSiteCategory-urlName" type="text" value="<?=isset($_POST['addSiteCategory-urlName'])?$_POST['addSiteCategory-urlName']:''?>" placeholder="URL Name">
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Importance Factor:</label>
                <div class="col-lg-3">
                    <div  class="input-group">
                        <span class="input-group-addon"><i class="fa fa-thumbs-o-up"></i></span>
                        <input class="form-control" name="addSiteCategory-importance" type="text" value="<?=isset($_POST['addSiteCategory-importance'])?$_POST['addSiteCategory-importance']:'1'?>" placeholder="Importance" >
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <label><input  name="addSiteCategory-hideNameIconImage" type="checkbox" value="checked" <?=isset($_POST['addSiteCategory-hideNameIconImage'])?$_POST['addSiteCategory-hideNameIconImage']:''?>>Display Name Icon Image</label>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="col-lg-3 control-label">Category Image / Icon:</label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                        <input class="form-control" type="text" name="addSiteCategory-imageIcon" value="<?=isset($_POST['addSiteCategory-imageIcon'])?$_POST['addSiteCategory-imageIcon']:''?>" placeholder="fa-battery-empty">
                    </div>

                    <h6><strong>OR </strong> Upload a different photo...</h6>

                    <input type="file" id="addSiteCategoryImageUpload" name="addSiteCategory-imageUpload" >
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Cover Image:</label>
                <div class="col-lg-9">
                    <div  class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                        <input class="form-control" type="text" name="addSiteCategory-coverImage" value="<?=isset($_POST['addSiteCategory-coverImage'])?$_POST['addSiteCategory-coverImage']:''?>" placeholder="http://" >
                    </div>

                    <h6><strong>OR </strong> Upload a cover photo ...</h6>

                    <input type="file" id="addSiteCategoryCoverImageUpload" name="addSiteCategory-coverImageUpload" >
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Short Description:</label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                        <textarea id="addSiteCategory-shortDescription" name="addSiteCategory-shortDescription" class="form-control" rows="2" placeholder="Short Description" ><?=isset($_POST['addSiteCategory-shortDescription'])?$_POST['addSiteCategory-shortDescription']:''?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Description:</label>
                <div class="col-lg-9">

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                        <textarea id="addSiteCategory-description" name="addSiteCategory-description" class="form-control" rows="5" placeholder="Description" ><?=isset($_POST['addSiteCategory-description'])?$_POST['addSiteCategory-description']:''?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-2 control-label">Keywords:</label>
                <div class="col-lg-10">

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-bold"></i></span>
                        <input class="form-control" type="text" name="addSiteCategory-inputKeywords" value="<?=isset($_POST['addSiteCategory-inputKeywords'])?$_POST['addSiteCategory-inputKeywords']:''?>" placeholder="Keywords">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-12 center-block text-center">

                    <?php
                        switch ($sActionName)
                        {
                            case 'add-new-category':
                            case 'add-new-subcategory':
                            case 'add-category':
                            case 'add-subcategory':
                            echo '<input type="submit" name="addSiteCategory" class="btn btn-primary" value="Add Category">';
                                break;
                            case 'edit-category':
                                echo '<input type="submit" name="editSiteCategory" class="btn btn-primary" value="Save Category: ' . $dtSiteCurrentCategory->sName . '">';
                                echo '<span style="padding-right: 30px"></span>';
                                echo '<input type="submit" name="deleteSiteCategory" class="btn btn-danger" value="Delete Category"  ">';
                                break;
                        }


                    ?>

                    <input type="reset"  value="Reset" class="btn btn-warning" ">
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.box -->

<script>
    $('#addSiteCategoryImageUpload').filestyle({
        buttonName : 'btn-success',
        buttonText : ' Upload PNG/Icon',
        iconName : 'glyphicon glyphicon-folder-open'
    });

    $('#addSiteCategoryCoverImageUpload').filestyle({
        buttonName : 'btn-success',
        buttonText : ' Upload Cover',
        iconName : 'glyphicon glyphicon-folder-open'
    });
</script>