<form id="registration-form" class="form-horizontal toggle-disabled" action="<?= base_url('#Registration');?>" role="form"  method="post">

    <?php
    $this->AlertsContainer->renderViewByName('g_msgRegistrationSuccess');
    $this->AlertsContainer->renderViewByName('g_msgRegistrationError');
    ?>

    <div class="form-group has-feedback" style="margin-bottom: 0px !important;">
        <div class="col-sm-5">
            <div style="margin-bottom: 15px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input id="register-username<?=$iRegistrationNo?>" type="text" class="form-control" name="register-username" value="<?=isset($_POST['register-username'])?$_POST['register-username']:''?>"  placeholder="username" OnKeyUp="validateRegistrationForm(this,<?=$iRegistrationNo?>)"  >
                <i  id="register-username<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>

        <div class="col-sm-7">
            <div style="margin-bottom: 15px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input id="register-email<?=$iRegistrationNo?>" type="text" class="form-control" name="register-email" value="<?=isset($_POST['register-email'])?$_POST['register-email']:''?>" placeholder="email" OnKeyUp="validateRegistrationForm(this,<?=$iRegistrationNo?>)" >
                <i  id="register-email<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>

    </div>

    <div class="form-group has-feedback" style="margin-bottom: 0px !important;">
        <div class="col-sm-6">
            <div style="margin-bottom: 15px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                <input id="register-firstName<?=$iRegistrationNo?>" type="text" class="form-control" name="register-firstName" value="<?=isset($_POST['register-firstName'])?$_POST['register-firstName']:''?>"  placeholder="First Name" OnKeyUp="validateRegistrationForm(this,<?=$iRegistrationNo?>)">
                <i  id="register-firstName<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>

        <div class="col-sm-6">
            <div style="margin-bottom: 15px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-bold"></i></span>
                <input id="register-lastName<?=$iRegistrationNo?>" type="text" class="form-control" name="register-lastName" value="<?=isset($_POST['register-lastName'])?$_POST['register-lastName']:''?>"  placeholder="Last Name" OnKeyUp="validateRegistrationForm(this,<?=$iRegistrationNo?>)" >
                <i  id="register-lastName<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>

    </div><!--/form-group-->

    <div class="form-group has-feedback" style="margin-bottom: 0 !important;">
        <div class="col-sm-6" >
            <div style="margin-bottom: 15px;" class="input-group">

                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input id="register-password<?=$iRegistrationNo?>" type="password" class="form-control" name="register-password" placeholder="password" OnKeyUp="validateRegistrationForm(this, <?=$iRegistrationNo?>)">
                <i  id="register-password<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>
        <div class="col-sm-6" >
            <div style="margin-bottom: 15px;" class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input id="register-passwordConfirmation<?=$iRegistrationNo?>" type="password" class="form-control" name="register-passwordConfirmation" placeholder="retype password" OnKeyUp="validateRegistrationForm(this, <?=$iRegistrationNo?>)">
                <i id="register-passwordConfirmation<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>
    </div>

    <div class="form-group has-feedback" style="margin-bottom: 0 !important;">
        <div class="col-sm-6" >
            <div style="margin-bottom: 15px;" class="input-group">
                <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                <input id="register-city<?=$iRegistrationNo?>" type="text" class="form-control" name="register-city" value="<?=isset($sRegisterCityPlaceHolder) ? $sRegisterCityPlaceHolder : 'San Francisco'?>" value="<?=isset($_POST['register-city'])?$_POST['register-city']:''?>"  OnKeyUp="validateRegistrationForm(this, <?=$iRegistrationNo?>)">
                <i id="register-city<?=$iRegistrationNo?>-feedback" class="form-control-feedback"></i>
            </div>
        </div>
        <div class="col-sm-6" >
            <div class="form-item" style="text-align: left;  ">
                <input id="register-countrySelectorCode<?=$iRegistrationNo?>" type="text" name="register-country" style="width: 100%" OnKeyUp="validateRegistrationForm(this, <?=$iRegistrationNo?>)">
                <label for="register-countrySelectorCode<?=$iRegistrationNo?>" style="display:none;">Select a country here...</label>
                <i id="register-countrySelectorCode<?=$iRegistrationNo?>-feedback"  class="form-control-feedback" style="margin-right: 0 !important;"></i>
            </div>
            <!--<div class="form-item" style="display:none; text-align: left;">
                <input type="text" id="countrySelectorCode" name="register-country" data-countrycodeinput="1"   readonly="readonly" placeholder="Selected country code will appear here"  >
                <label for="countrySelectorCode">...and the selected country code will be updated here</label>
            </div> -->
        </div>
    </div>


    <input type="hidden" name="val" value="register">

    <div class="form-group" style="text-align: center">
            <button type="submit" id="register-submitButton" OnClick="return validateRegistrationFormPost(this, <?=$iRegistrationNo?>);" class="btn btn-success" >
                <i class="fa fa-sign-in"></i> Register
            </button>

            <span style="margin-left:8px; color:black;">or</span>
    </div>



    <div style="border-top: 1px solid #999; padding-top:20px"  class="form-group">

        <div class="" style="margin: -15px 0 10px 0; text-align: center">
            <span style="color:black;">signup with:</span>
        </div>

        <div class="col-md-12" style="text-align: center">
            <?=isset($OAuth2LoginButtons ) ? $OAuth2LoginButtons : ''?>
        </div>
    </div>


</form>



<?php
    $this->BottomScriptsContainer->addScript("
    $('#register-countrySelectorCode".$iRegistrationNo."').countrySelect({
        ". ((isset($_POST['register-country'])) ? "defaultCountry: '".$_POST['register-country']."',":'').
    //onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
    "preferredCountries: ['ca', 'gb', 'us'".((isset($_POST['register-country'])&&(!in_array($_POST['register-country'],['ca', 'gb', 'us']))) ? ",'".$_POST['register-country']."'":'')."]
    }); ",true);

    $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/registration-validation.js" : 'assets/min-js/registration-validation-min.js'));
?>