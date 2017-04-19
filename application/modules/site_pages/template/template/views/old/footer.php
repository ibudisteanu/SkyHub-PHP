
<!-- section Footer start  -->
<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="footer-content text-center">
                    <a href="#slider-part" class="page-scroll logo-title">
                        <img src="<?php echo base_url('images/blackbg.png')?>" alt="" class="img-responsive">
                    </a>
                    <ul class="footer-socail list-inline">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="copyright">
                            <p>&copy; Copyright <a href="http://www.bit-technologies.net/"><span>BIT TECHNOLOGIES</span></a> 2016</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="copyright">

                            <p class="pull-right">
                            <?php
                                if (isset($g_arrFooterMenu))
                                foreach($g_arrFooterMenu as $nav)
                                {
                                    if ((isset($g_sActivePage))&&(strtolower($nav[0])==strtolower($g_sActivePage)))
                                        echo '<a href="'.$nav[1].'"><span>'. $nav[0].'</span></a>    ';
                                    else
                                        echo '<a href="'.$nav[1].'">'. $nav[0].'</a>    ';
                                }
                            ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- row end  -->
    </div> <!-- container end  -->
</footer>
<!-- section Footer end  -->


<!-- Back To Top Button -->
<div id="back-top">
    <a href="#top" class="page-scroll btn btn-primary" ><i class="fa fa-angle-double-up"></i></a>
</div>
<!-- End Back To Top Button -->


<!-- Style Switcher -->
<script type="text/javascript" src="<?php echo base_url('js/isotope.js')?>"></script>
<!-- Owl Carousel -->
<script type="text/javascript" src="<?php echo base_url('js/owl.carousel.js')?>"></script>
<!-- PrettyPhoto -->
<script type="text/javascript" src="<?php echo base_url('js/jquery.prettyPhoto.js')?>"></script>
<!-- Isotope -->
<script type="text/javascript" src="<?php echo base_url('js/isotope.js')?>"></script>
<!-- Wow Animation -->
<script type="text/javascript" src="<?php echo base_url('js/wow.min.js')?>"></script>
<!-- SmoothScroll -->
<script type="text/javascript" src="<?php echo base_url('js/smooth-scroll.js')?>"></script>
<!-- Eeasing -->
<script type="text/javascript" src="<?php echo base_url('js/jquery.easing.1.3.js')?>"></script>
<!-- Counter -->
<script type="text/javascript" src="<?php echo base_url('js/jquery.counterup.min.js')?>"></script>
<!-- Waypoints -->
<script type="text/javascript" src="<?php echo base_url('js/jquery.waypoints.min.js')?>"></script>
<!-- Scrolling navigation -->
<script type="text/javascript" src="<?php echo base_url('js/scrolling-nav.js"')?>></script>
<!-- Google Map API Key Source -->
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!-- Custom js -->
<script type="text/javascript" src="<?php echo base_url('js/custom.js')?>"></script>
<script>
    new WOW().init();
</script>
<script>

    $('.counter').counterUp({
        delay: 100,
        time: 2000
    });
</script>
</body>
</html>