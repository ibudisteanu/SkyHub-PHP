<!-- /.box-header -->
<div class="box-body">


    <a class="anchor" id="ChangePassword"></a>
    <form class="form-horizontal" action="<?= base_url('/profile/edit/#ChangePassword');?>" role="form" method="post">

        <?php
            $this->AlertsContainer->renderViewByName('g_msgChangePasswordSuccess');
            $this->AlertsContainer->renderViewByName('g_msgChangePasswordError');
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label">Enter Your Password:</label>
            <div class="col-md-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <input class="form-control" type="password" value="" name="changePassword-password" required>
                </div>
            </div>
        </div>

        <h3 style = "margin-bottom: 25px">Change your <strong>Email Address</strong>  </h3>

        <div class="form-group">
            <label class="col-lg-3 control-label">Email:</label>
            <div class="col-lg-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    <input class="form-control" type="text" name="changePassword-newEmail" value="<?=isset($_POST['changePassword-newEmail'])?$_POST['changePassword-newEmail']:$g_User->sEmail?>" >
                </div>
            </div>
        </div>

        <h3 style = "margin-bottom: 25px">Change your <strong>Password</strong>  </h3>

        <div class="form-group">
            <label class="col-md-3 control-label">New Password:</label>
            <div class="col-md-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <input class="form-control" type="password"  name="changePassword-newPassword" value="<?=isset($_POST['changePassword-newPassword'])?$_POST['changePassword-newPassword']:''?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label">Retype New Password:</label>
            <div class="col-md-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <input class="form-control" type="password"  name="changePassword-retypeNewPassword" value="<?=isset($_POST['changePassword-retypeNewPassword'])?$_POST['changePassword-retypeNewPassword']:''?>">
                </div>
            </div>
        </div>

        <input type="hidden" name="val" value="change_password">

        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-8">
                <input type="submit" class="btn btn-primary" value="Save Changes">
                <span></span>
                <input type="reset" class="btn btn-warning" ">
            </div>
        </div>
    </form>
</div>