<!-- Section features start -->
<div class="col-md-12 col-sm-12 col-xs-12 feature" style="margin-left: auto; margin-right: auto ; padding=0 0 0 0; border=0px" data-wow-delay=".3s" >

        <div class="container">
            <div class="heading-inner text-center" style="margin-bottom: 5px">

                <?php
                    if (!$this->MyUser->bLogged)
                    {
                        echo '<h2 class="sec-title">Welcome to <span>'. WEBSITE_NAME .'</span></h2>';
                        echo '<p>Connect, Discover and Discuss within these amazing forums</p>';
                    } else
                    {
                        echo '<h2 class="sec-title">TOP CATEGORIES on <span>'.WEBSITE_NAME.'</span></h2>';
                        echo '<p>Connect, Discover and Discuss within these amazing forums</p>';
                    }
                ?>


            </div>

            <?php
                $this->view('site_categories_view');
            ?>
        </div> <!-- heading row end -->
</div>
<!-- Section features end -->
