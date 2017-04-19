<!-- slider section -->
<section class="slider">
    <div class="slider-wrap">
        <div class="texture-overlay"></div>
        <div class="container">
            <div class="row">

                <?php
                    if (!$this->MyUser->bLogged)
                    {
                        echo '<div class="col-md-6 col-md-offset-1 ">';
                        echo '<div class="logo-slider" style="background-image:url('.base_url('images/logo.png').')"></div>';
                        //echo '<img src="'.base_url('images/logo.png').'" width=100% alt="SkyHUB Social Platform">';
                        //echo '<h1>'.WEBSITE_NAME.'</h1>';
                        echo '<h2>Connect, discover & change the world</h2>';
                        echo '<ul class="top-link list-inline">';
                        echo '<li><a href="'.base_url('#Registration').'"><i class="fa fa-android"></i> Register</a></li>';
                        echo '</ul>';
                        echo '</div>';

                        if (isset($g_dtLoginBox ))
                            echo $g_dtLoginBox;
                    }

                ?>

            </div>
        </div> <!-- row end  -->
    </div> <!-- container end  -->
</section>
<!-- slider section end -->
