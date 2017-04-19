
    <div style="margin-top:10px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-xxs-10 col-xxs-offset-1 col-tn-12 col-tn-offset-0">
        <div class="panel panel-info" >

            <div class="panel-heading">
                <div class="panel-title" style="text-align: left;">Register to <?= WEBSITE_NAME ?></div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="<?= base_url('#loginbox')?>">Already registered to <?= WEBSITE_NAME ?>?</a></div>
            </div>

                <div class="box-body" style="padding-bottom: 0px; margin-bottom: -10px;">

                    <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                    <?php
                        $data=[];
                        $this->load->view('auth_site/registration_form',$data);
                    ?>


                </div>
        </div>
    </div>



