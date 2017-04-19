<form role="form"  action="<?=base_url('contact#Contact')?>" role="form" method="post">

    <div class="col-lg-12">
        <?php
        $this->AlertsContainer->renderViewByName('g_msgContactSuccess');
        $this->AlertsContainer->renderViewByName('g_msgContactError');
        ?>
    </div>


    <input type="hidden" name="val" value="contact">

    <div class="form-group has-feedback">
        <label for="InputName"><?= $this->MyUser->bLogged ? '<strong>'.$this->MyUser->getFullName().'</strong>' : 'Your Name'?></label>
        <?php
        if (!$this->MyUser->bLogged)
            echo '<div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input type="text" class="form-control" name="contact-FullName" id="contact-FullName"'.(isset($_POST['contact-FullName'])?'value="'.$_POST['contact-FullName'].'"':'').'   placeholder="Enter Name" required>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-ok form-control-feedback"></i></span>
                  </div>';
        ?>
    </div>


    <div class="form-group has-feedback">
        <label for="InputEmail"><?= $this->MyUser->bLogged ? '<strong>'.$this->MyUser->sEmail.'</strong>':'Your Email'?></label>

        <?php
        if (!$this->MyUser->bLogged)
            echo '<div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    <input type="text" class="form-control" name="contact-Email" id="contact-Email" '.(isset($_POST['contact-Email'])?'value="'.$_POST['contact-Email'].'"':'').' placeholder="Enter Email Address" required>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-ok form-control-feedback"></i></span>
                  </div>';
        ?>
    </div>

    <div class="form-group">
        <label for="InputMessage">Topic</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>
            <input type="text" class="form-control" name="contact-Topic" id="contact-Topic"  placeholder="Topic Reason" required <?=isset($_POST['contact-Topic'])?'value="'.$_POST['contact-Topic'].'"':''?> >
            <span class="input-group-addon"><i class="glyphicon glyphicon-ok form-control-feedback"></i></span></div>
    </div>

    <div class="form-group has-feedback">
        <label for="InputMessage">Message</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
            <textarea name="contact-Message" id="contact-Message" class="form-control" rows="5" required ><?=isset($_POST['contact-Message'])?$_POST['contact-Message']:''?> Message Body</textarea>
            <span class="input-group-addon"><i class="glyphicon glyphicon-ok form-control-feedback"></i></span></div>
    </div>
    <div class="form-group has-feedback">
        <label for="InputReal">What is 4+13? </label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-question-circle"></i></span>
            <input type="text" class="form-control" name="contact-Captcha" id="contact-Captcha" required <?=isset($_POST['contact-Captcha'])?'value="'.$_POST['contact-Captcha'].'"':''?>>
            <span class="input-group-addon"><i class="glyphicon glyphicon-ok form-control-feedback"></i></span></div>
    </div>
    <button type="submit" name="submit" id="submit" value="Submit" class="btn btn-info pull-right">
        <i class="fa fa-share"></i> Send Message
    </button>
</form>