<!--<section id="counter-area" style="background-color:<?php echo isset($g_clRegistrationBackgroundColor) ? $g_clRegistrationBackgroundColor : '#00AEFF !important;'?> -->
<div class="col-md-12 col-sm-12 col-xs-12 blue-area" style="margin-left: auto ;margin-right: auto ; padding=0 0 0 0; border=0px" data-wow-delay=".3s" >
    <a class="anchor" id="Registration"> </a>
    <div class="container">
        <div class="row">
            <div class="heading-inner text-center">
                <h2 class="sec-title">Register to <span style="color:white !important"><?= WEBSITE_NAME ?> </span> </h2>

                <p><strong>Register now to <span style="color:white !important"> JOIN </span> this amazing community</strong></p>

                <?php
                    $this->view('registration_box');
                ?>

            </div>
        </div> <!-- heading row end -->
    </div>	<!-- container end -->
</div>
<!-- section registration end -->
