<div class="col-md-12 col-sm-12 col-xs-12 slider wow fadeInUp"   data-wow-delay=".3s" style="height:auto; padding=0 0 0 0; border=0px">
<!-- slider section -->
    <div class="row">
        <div class="slider-wrap" >

                <?php
                    if (!$this->MyUser->bLogged)
                    {
                        $this->load->view('main_header_welcome_view');
                    }

                ?>

        </div>

    </div> <!-- container end  -->
</div>
<!-- slider section end -->

<?php
    echo $HeaderToolBox->renderMenu('header','float:right;');
?>