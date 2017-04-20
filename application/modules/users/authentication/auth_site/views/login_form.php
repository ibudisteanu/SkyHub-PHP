<form id="loginform"  class="form-horizontal" action="<?= base_url('/login#Login');?>" role="form" OnSubmit="return validateLoginFormPost(this,<?=$iLoginNo?>);" method="post">


    <?php
    $this->AlertsContainer->renderViewByName('g_msgLoginSuccess');
    $this->AlertsContainer->renderViewByName('g_msgLoginError');
    ?>


    <div class="form-group has-feedback" style="margin: 0px !important;">
        <div class="input-group" style="margin-bottom: 15px" >
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input id="login-username<?=$iLoginNo?>" type="text" class="form-control" name="login-username" value="<?=isset($_POST['login-username'])?$_POST['login-username']:''?>"  placeholder="username or email" OnKeyUp="validateLoginForm(this,<?=$iLoginNo?>)" OnMouseDown="validateLoginForm(this, <?=$iLoginNo?>)">
            <i  id="login-username<?=$iLoginNo?>-feedback" class="form-control-feedback"></i>
        </div>

        <div class="input-group" style="margin-bottom: 15px">
            <span class="input-group-addon"><i class="fa fa-key"></i></span>
            <input id="login-password<?=$iLoginNo?>" type="password" class="form-control" name="login-password" placeholder="password" OnKeyUp="validateLoginForm(this,<?=$iLoginNo?>)" OnMouseDown="validateLoginForm(this,<?=$iLoginNo?>)">
            <i  id="login-password<?=$iLoginNo?>-feedback" class="form-control-feedback"></i>
        </div>
    </div>

    <input type="hidden" name="val" value="checkin">

    <div class="input-group" style="margin-top: -10px">
        <div class="checkbox">
            <label>
                <input id="login-remember" type="checkbox" name="remember" value="1" checked> Remember me
            </label>
        </div>
    </div>


    <div style="margin-top:10px; margin-bottom:0px;" class="form-group">

        <div class="col-sm-12 controls">
            <button id="login-submitButton<?=$iLoginNo?>" type="submit" value="Login" class="btn btn-success" style="margin-right:20px; margin-bottom: 10px;">
                <i class="fa fa-sign-in"></i> Login
            </button>

            <?=$OAuth2LoginButtons?>
        </div>
    </div>


    <?php
        if (isset($bShowBottom) && ($bShowBottom==true)) :
    ?>
        <div class="form-group" style="margin-bottom: 0;">
            <div class="col-md-12 control">
                <div style="border-top: 1px solid#888; padding-top:10px; font-size:85%" >
                    Don't have an account?
                    <a href="<?php echo base_url('#Registration')?>" <!--onClick="$('#loginbox').hide(); $('#signupbox').show()" -->
                    <strong>Sign Up Now</strong>
                    </a>
                </div>
            </div>
        </div>

    <?php
    endif ;
    ?>
</form>

<?php  $this->BottomScriptsContainer->addScriptResFile(base_url( defined(WEBSITE_OFFLINE) ? "app/res/js/login-validation.js" : 'assets/min-js/login-validation-min.js'));  ?>