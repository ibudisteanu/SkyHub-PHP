<a class="anchor" id="loginbox"></a>
    <div id="loginbox" <?=((isset($sSpecialStyle) && ($sSpecialStyle == '')) ? 'style="margin-top:40px;" class="col-md-4 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"' : (isset($sSpecialStyle) ? $sSpecialStyle : '') )?> >
        <?php
            $this->load->view('auth_site/login_panel',null);
        ?>
    </div>

