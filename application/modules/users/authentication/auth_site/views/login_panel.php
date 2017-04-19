<div class="panel panel-info" >
    <div class="panel-heading" style="padding-left: 10px; padding-right: 10px">
        <div class="panel-title">Sign In SkyHub</div>
        <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
    </div>
    <div class="panel-body" style="padding-left: 10px; padding-right: 10px"  >


        <?php
            $data['bShowBottom']=true;

            $this->load->view('auth_site/login_form',$data);
        ?>


    </div>
</div>