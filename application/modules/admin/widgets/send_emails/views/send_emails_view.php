<a class="anchor" id="SendEmail"></a>
<div class="box box-primary">
    <div class="box-header">
        <i class="ion ion-clipboard"></i>

        <?php
        //Title
        switch ($sActionName)
        {
            case 'send-email':
            case 'send-email-category':
                echo '<h3 class="box-title">Send <strong>Emails </strong></h3>';
                break;
        }
        ?>

    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <form class="form-horizontal" action="<?= base_url($sFormAction);?>" role="form" method="post" enctype= "multipart/form-data">

            <?php
            $this->AlertsContainer->renderViewByName('g_msgSendEmailsSuccess');
            $this->AlertsContainer->renderViewByName('g_msgSendEmailsError');
            ?>

            <?php
            //EDIT option
            if (isset($dtSiteCurrentCategory)&&($dtSiteCurrentCategory!=null)) {
                echo '<input type="hidden" name="category_id" value="'.$dtSiteCurrentCategory->sID.'">';
            }
            ?>

            <div class="form-group">
                <label class="col-lg-3 control-label">Users:</label>
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <select class="form-control"  name="sendEmails-selectedUser">
                            <?php

                            //if ((isset($_POST['sendEmails-selectedUser']) && ($_POST['sendEmails-selectedUser']=='')))
                            echo '<option></option>';


                            foreach ($dtSiteUsers as $User)
                            {
                                echo '<option value="'.$User->sID.'" ';
                                echo ((isset($_POST['sendEmails-selectedUser']) && ($_POST['sendEmails-selectedUser']==$User->sID)) ?  ' selected="selected"' : '');
                                echo '>';
                                echo $User->getFullName().' :: '.$User->sEmail;

                                echo '</option>';
                            }

                            ?>
                        </select>
                    </div>
                </div>
                <label class="col-lg-8 control-label">OR</label>
            </div>


            <div class="form-group">
                <label class="col-lg-3 control-label">Parent Category:</label>
                <div class="col-lg-8">
                    <div class="input-group" style="margin-bottom: 20px" >
                        <span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
                        <select class="form-control"  name="sendEmails-selectedCategory">
                            <?php

                            //if ((isset($_POST['sendEmails-parentCategory']) && ($_POST['sendEmails-parentCategory']=='')))
                            echo '<option></option>';


                            foreach ($dtSiteCategories as $Category)
                            {
                                echo '<option value="'.$Category->sID.'" ';
                                echo ((isset($_POST['sendEmails-selectedCategory']) && ($_POST['sendEmails-selectedCategory']==$Category->sID)) ?  ' selected="selected"' : '');
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
                <label class="col-lg-3 control-label">Action/Template:</label>
                <div class="col-lg-8">
                    <div class="input-group" >
                        <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
                        <select class="form-control"  name="sendEmails-actionTemplate">
                            <option value="custom body"  <?php echo ((isset($_POST['sendEmails-actionTemplate']) && ($_POST['sendEmails-actionTemplate']=='custom body')) ?  ' selected="selected"' : ''); ?> >custom body</option>
                            <option value="registration" <?php echo ((isset($_POST['sendEmails-actionTemplate']) && ($_POST['sendEmails-actionTemplate']=='registration')) ?  ' selected="selected"' : ''); ?> >registration</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Title/Subject:</label>
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                        <input class="form-control" name="sendEmails-titleSubject" type="text" value="<?=isset($_POST['sendEmails-titleSubject'])?$_POST['sendEmails-titleSubject']:''?>" placeholder="Subject" required>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">Body:</label>
                <div class="col-lg-8">

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                        <textarea id="sendEmails-body" name="sendEmails-body" class="form-control" rows="5" placeholder="Email Body" ><?=isset($_POST['sendEmails-body'])?$_POST['sendEmails-body']:''?></textarea>
                    </div>
                </div>
            </div>



            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-8">

                    <?php
                    //EDIT option
                        echo '<input type="hidden" name="val" value="send_email">';
                        echo '<input type="submit" class="btn btn-primary" value="Send Email/s">';
                    ?>

                    <span></span>
                    <input type="reset"  value="Reset" class="btn btn-warning" ">
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.box -->

