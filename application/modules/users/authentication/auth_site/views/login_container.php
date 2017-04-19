<a class="anchor" id="Login"></a>
<!-- section login start -->

<!--<section id="counter-area" style="background-color:<?php echo isset($g_clRegistrationBackgroundColor) ? $g_clRegistrationBackgroundColor : '#00AEFF !important;'?> -->


<section class="gray-area">
        <div class="row">
            <div class="heading-inner text-center">
                <h2 class="sec-title">Login to <span style="color:navy !important"><?= WEBSITE_NAME ?> </span> </h2>

                <p><strong>Login now to this amazing community</strong></p>

                <div id="loginbox" style="margin-top:10px;" class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
                    <?php
                        $this->view('login_panel');
                    ?>
                </div>

            </div>
        </div> <!-- heading row end -->
</section>
<!-- section login end -->
